<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Service;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FinalVerificationThreeFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $patient;
    protected User $dentist;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'      => 'مدير النظام التنفيذي',
            'email'     => 'superadmin@riyada.com',
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->patient = User::factory()->create([
            'name'      => 'سامي الحربي',
            'email'     => 'sami@example.com',
            'role'      => 'patient',
            'is_active' => true,
        ]);

        $this->dentist = User::factory()->create([
            'name'      => 'د. حازم العلي',
            'email'     => 'hazem@example.com',
            'role'      => 'dentist',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name'             => 'علاج جذور الأسنان',
            'price'            => 450.00,
            'duration_minutes' => 45,
            'description'      => 'معالجة لب وجذور الأسنان بأحدث أجهزة الروتاري',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Security Audit Log Tests
    |--------------------------------------------------------------------------
    */

    public function test_audit_log_is_saved_in_database_with_sanitized_data(): void
    {
        $log = AuditLog::record(
            'password_change',
            'تم تغيير كلمة المرور للمستخدم',
            $this->patient,
            ['password' => 'old_secret_123', 'status' => 'active'],
            ['password' => 'new_secret_456', 'status' => 'active'],
            $this->admin
        );

        $this->assertDatabaseHas('audit_logs', [
            'id'             => $log->id,
            'user_id'        => $this->admin->id,
            'user_name'      => $this->admin->name,
            'action'         => 'password_change',
            'auditable_type' => get_class($this->patient),
            'auditable_id'   => $this->patient->id,
        ]);

        // Sensitive password must be masked
        $this->assertEquals('********', $log->old_values['password']);
        $this->assertEquals('********', $log->new_values['password']);
        $this->assertEquals('active', $log->new_values['status']);
    }

    public function test_admin_can_view_and_filter_audit_logs(): void
    {
        AuditLog::record('login', 'عملية_دخول_مميزة_رقم_999', null, null, null, $this->admin);
        AuditLog::record('role_change', 'تعديل_دور_مستخدم_خاص_888', null, null, null, $this->admin);
        AuditLog::record('delete', 'حذف_سجل_طبي_فريد_777', null, null, null, $this->admin);

        $this->actingAs($this->admin);

        // 1. View all
        $response = $this->get(route('admin.settings.security'));
        $response->assertOk();
        $response->assertSee('سجل التدقيق والعمليات الدائم');
        $response->assertSee('تعديل_دور_مستخدم_خاص_888');

        // 2. Filter by action
        $filterResponse = $this->get(route('admin.settings.security', ['action' => 'role_change']));
        $filterResponse->assertOk();
        $filterResponse->assertSee('تعديل_دور_مستخدم_خاص_888');
        $filterResponse->assertDontSee('عملية_دخول_مميزة_رقم_999');

        // 3. Search by text
        $searchResponse = $this->get(route('admin.settings.security', ['q' => 'طبي_فريد']));
        $searchResponse->assertOk();
        $searchResponse->assertSee('حذف_سجل_طبي_فريد_777');
    }

    public function test_clear_logs_does_not_wipe_permanent_database_audit_logs(): void
    {
        AuditLog::record('critical_action', 'عملية تدقيق هامة للغاية لا يجوز حذفها', null, null, null, $this->admin);

        $this->assertDatabaseCount('audit_logs', 1);

        $this->actingAs($this->admin);
        $response = $this->post(route('admin.settings.security.clear-logs'));
        $response->assertRedirect(route('admin.settings.security'));

        // Permanent DB audit log must still exist!
        $this->assertDatabaseCount('audit_logs', 2); // original + the clear_logs audit entry itself
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'critical_action',
        ]);
    }

    public function test_non_admin_cannot_access_audit_logs(): void
    {
        $this->actingAs($this->patient);
        $this->get(route('admin.settings.security'))->assertForbidden();

        $this->actingAs($this->dentist);
        $this->get(route('admin.settings.security'))->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Database Notifications & Read Status Synchronization
    |--------------------------------------------------------------------------
    */

    public function test_appointment_creation_dispatches_database_notification_to_staff(): void
    {
        $appointment = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time'       => '11:00:00',
            'end_time'         => '11:45:00',
            'status'           => 'confirmed',
        ]);

        NotificationService::notifyStaff($appointment, 'created');

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id'   => $this->admin->id,
            'read_at'         => null,
        ]);

        $this->assertEquals(1, $this->admin->unreadNotifications()->count());

        // Duplicate notification must be prevented
        NotificationService::notifyStaff($appointment, 'created');
        $this->assertEquals(1, $this->admin->unreadNotifications()->count());
    }

    public function test_marking_notifications_as_read_persists_in_database(): void
    {
        $appointment = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time'       => '12:00:00',
            'end_time'         => '12:45:00',
            'status'           => 'confirmed',
        ]);

        NotificationService::notifyStaff($appointment, 'created');
        $notif = $this->admin->notifications()->first();
        $this->assertNotNull($notif);
        $this->assertNull($notif->read_at);

        // 1. Mark single notification as read via AJAX
        $this->actingAs($this->admin);
        $response = $this->postJson(route('admin.notifications.read', $notif->id));
        $response->assertOk();
        $response->assertJson(['success' => true, 'unread_count' => 0]);

        $notif->refresh();
        $this->assertNotNull($notif->read_at);

        // 2. Mark all as read via AJAX
        $appointment2 = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->addDay()->format('Y-m-d'),
            'start_time'       => '14:00:00',
            'end_time'         => '14:45:00',
            'status'           => 'pending',
        ]);
        NotificationService::notifyStaff($appointment2, 'created');
        $this->assertEquals(1, $this->admin->unreadNotifications()->count());

        $allReadResponse = $this->postJson(route('admin.notifications.mark-all-read'));
        $allReadResponse->assertOk();
        $allReadResponse->assertJson(['success' => true, 'unread_count' => 0]);

        $this->assertEquals(0, $this->admin->unreadNotifications()->count());
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Email and SMS Diagnostics & Status Reporting
    |--------------------------------------------------------------------------
    */

    public function test_test_email_connection_in_log_mode_reports_truthful_warning(): void
    {
        Config::set('mail.default', 'log');

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.settings.notifications.test-email'), [
            'test_email' => 'doctor@example.com',
        ]);

        $response->assertRedirect(route('admin.settings.notifications'));
        $response->assertSessionHas('warning');
        $this->assertStringContainsString('Log Mode', session('warning'));

        // Action is recorded in audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'test_email',
        ]);
    }

    public function test_test_email_validation_requires_valid_email(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.settings.notifications.test-email'), [
            'test_email' => 'invalid-email-address',
        ]);

        $response->assertSessionHasErrors(['test_email']);
    }

    public function test_test_sms_reports_unconfigured_gateway_without_false_claims(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.settings.notifications.test-sms'), [
            'test_phone' => '0555001122',
        ]);

        $response->assertRedirect(route('admin.settings.notifications'));
        $response->assertSessionHas('warning');
        $this->assertStringContainsString('غير مهيأة', session('warning'));

        // Action is recorded in audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'test_sms',
        ]);
    }
}
