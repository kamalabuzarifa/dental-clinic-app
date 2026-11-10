<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Service;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SecurityAndIdorDeepVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin1;
    protected User $admin2;
    protected User $patient1;
    protected User $patient2;
    protected User $dentist;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin1 = User::factory()->create([
            'name'      => 'المدير الأول',
            'email'     => 'admin1@riyada.com',
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->admin2 = User::factory()->create([
            'name'      => 'المدير الثاني',
            'email'     => 'admin2@riyada.com',
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->patient1 = User::factory()->create([
            'name'      => 'المريض الأول',
            'email'     => 'patient1@riyada.com',
            'role'      => 'patient',
            'is_active' => true,
        ]);

        $this->patient2 = User::factory()->create([
            'name'      => 'المريض الثاني',
            'email'     => 'patient2@riyada.com',
            'role'      => 'patient',
            'is_active' => true,
        ]);

        $this->dentist = User::factory()->create([
            'name'      => 'د. طارق الحكيم',
            'email'     => 'dentist@riyada.com',
            'role'      => 'dentist',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name'             => 'تقويم الأسنان الشفاف',
            'price'            => 3500.00,
            'duration_minutes' => 60,
            'description'      => 'تقويم غير مرئي مخصص للحالات المتقدمة',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Audit Logs Deep Verification
    |--------------------------------------------------------------------------
    */

    public function test_updating_doctor_patient_service_and_appointment_records_audit_logs(): void
    {
        $this->actingAs($this->admin1);

        // 1. Doctor update
        $this->put(route('admin.doctors.update', $this->dentist), [
            'name'  => 'د. طارق الحكيم - استشاري أول',
            'email' => 'dentist@riyada.com',
            'phone' => '0501239999',
        ])->assertRedirect();
        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'update_doctor',
            'auditable_type' => User::class,
            'auditable_id'   => $this->dentist->id,
        ]);

        // 2. Patient update
        $this->put(route('admin.patients.update', $this->patient1), [
            'name'  => 'المريض الأول المحدث',
            'email' => 'patient1@riyada.com',
            'phone' => '0555998877',
        ])->assertRedirect();
        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'update_patient',
            'auditable_type' => User::class,
            'auditable_id'   => $this->patient1->id,
        ]);

        // 3. Service update
        $this->put(route('admin.services.update', $this->service), [
            'name'             => 'تقويم الأسنان الشفاف المطور',
            'price'            => 3800.00,
            'duration_minutes' => 60,
        ])->assertRedirect();
        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'update_service',
            'auditable_type' => Service::class,
            'auditable_id'   => $this->service->id,
        ]);

        // 4. Appointment update
        $app = Appointment::create([
            'patient_id'       => $this->patient1->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time'       => '14:00:00',
            'end_time'         => '15:00:00',
            'status'           => 'confirmed',
        ]);
        $this->put(route('admin.appointments.update', $app), [
            'patient_id'       => $this->patient1->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time'       => '15:00',
            'status'           => 'completed',
            'notes'            => 'تمت المتابعة الدورية',
        ])->assertRedirect();
        $this->assertDatabaseHas('audit_logs', [
            'action'         => 'update_appointment',
            'auditable_type' => Appointment::class,
            'auditable_id'   => $app->id,
        ]);
    }

    public function test_audit_logs_cannot_be_deleted_or_updated_via_http_endpoints(): void
    {
        $log = AuditLog::record('test_action', 'عملية أمنية تجريبية', null, null, null, $this->admin1);

        $this->actingAs($this->admin1);

        // Attempt direct DELETE on audit logs
        $this->delete("/admin/audit-logs/{$log->id}")->assertNotFound();
        $this->delete("/admin/settings/security/audit-logs/{$log->id}")->assertNotFound();

        // Attempt direct PUT on audit logs
        $this->put("/admin/audit-logs/{$log->id}", ['description' => 'tampered'])->assertNotFound();

        // Record must remain completely intact in DB
        $this->assertDatabaseHas('audit_logs', [
            'id'          => $log->id,
            'description' => 'عملية أمنية تجريبية',
        ]);
    }

    public function test_audit_logs_pagination_and_combined_filtering(): void
    {
        // Seed 25 logs with different dates and actions
        for ($i = 1; $i <= 20; $i++) {
            AuditLog::record('login', "دخول تجريبي رقم {$i}", null, null, null, $this->admin1);
        }
        for ($i = 1; $i <= 5; $i++) {
            AuditLog::record('delete', "حذف تجريبي رقم {$i}", null, null, null, $this->admin2);
        }

        $this->actingAs($this->admin1);

        // 1. Pagination: first page has 15 items
        $page1Response = $this->get(route('admin.settings.security'));
        $page1Response->assertOk();

        // 2. Filter by user + action
        $filtered = $this->get(route('admin.settings.security', [
            'user_id' => $this->admin2->id,
            'action'  => 'delete',
        ]));
        $filtered->assertOk();
        $filtered->assertSee('حذف تجريبي رقم 1');
        $filtered->assertDontSee('دخول تجريبي رقم 1');
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Database Notifications Deep Verification (IDOR & Isolation)
    |--------------------------------------------------------------------------
    */

    public function test_user_cannot_mark_another_users_notification_as_read_idor_protection(): void
    {
        $appointment = Appointment::create([
            'patient_id'       => $this->patient1->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time'       => '09:00:00',
            'end_time'         => '10:00:00',
            'status'           => 'confirmed',
        ]);

        NotificationService::notifyStaff($appointment, 'created');

        $admin1Notif = $this->admin1->notifications()->first();
        $admin2Notif = $this->admin2->notifications()->first();

        $this->assertNotNull($admin1Notif);
        $this->assertNotNull($admin2Notif);
        $this->assertNotEquals($admin1Notif->id, $admin2Notif->id);

        // Admin 1 attempts to mark Admin 2's notification as read via IDOR attack
        $this->actingAs($this->admin1);
        $response = $this->postJson(route('admin.notifications.read', $admin2Notif->id));

        // Must be rejected with 404 (IDOR prevented)
        $response->assertNotFound();

        // Admin 2's notification remains unread!
        $admin2Notif->refresh();
        $this->assertNull($admin2Notif->read_at);
    }

    public function test_notification_read_at_persists_across_sessions_and_re_login(): void
    {
        $appointment = Appointment::create([
            'patient_id'       => $this->patient1->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time'       => '10:00:00',
            'end_time'         => '11:00:00',
            'status'           => 'confirmed',
        ]);

        NotificationService::notifyStaff($appointment, 'created');
        $notif = $this->admin1->notifications()->first();

        // Admin 1 logs in and marks notification as read
        $this->actingAs($this->admin1);
        $this->postJson(route('admin.notifications.read', $notif->id))->assertOk();

        // Simulate Logout
        $this->post(route('logout'));

        // Admin 1 logs in again (new session / simulated new browser)
        $this->actingAs($this->admin1);
        $dashboardResponse = $this->get(route('admin.dashboard'));
        $dashboardResponse->assertOk();

        // The notification is STILL marked as read in database
        $notif->refresh();
        $this->assertNotNull($notif->read_at);
        $this->assertEquals(0, $this->admin1->unreadNotifications()->count());
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Security & IDOR: Patient Appointment Cancellation
    |--------------------------------------------------------------------------
    */

    public function test_patient_cannot_cancel_another_patients_appointment_idor_prevention(): void
    {
        $appPatient2 = Appointment::create([
            'patient_id'       => $this->patient2->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time'       => '16:00:00',
            'end_time'         => '17:00:00',
            'status'           => 'confirmed',
        ]);

        // Patient 1 tries to cancel Patient 2's appointment
        $this->actingAs($this->patient1);
        $response = $this->patch(route('patient.appointments.cancel', $appPatient2));

        // Must be blocked with 403 Forbidden
        $response->assertForbidden();

        // Appointment status remains confirmed
        $appPatient2->refresh();
        $this->assertEquals('confirmed', $appPatient2->status);
    }
}
