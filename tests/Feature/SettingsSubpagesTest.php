<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsSubpagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $receptionist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'مدير النظام',
            'email' => 'admin@clinic.com',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->receptionist = User::factory()->create([
            'name' => 'موظف الاستقبال',
            'email' => 'recep@clinic.com',
            'role' => 'receptionist',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_all_settings_subpages(): void
    {
        $subpages = [
            ['route' => 'admin.settings.clinic', 'view' => 'admin.settings.clinic', 'see' => 'بيانات المركز والشعار'],
            ['route' => 'admin.settings.booking', 'view' => 'admin.settings.booking', 'see' => 'الحجوزات والمواعيد'],
            ['route' => 'admin.settings.working-hours', 'view' => 'admin.settings.working-hours', 'see' => 'أوقات العمل والدوام'],
            ['route' => 'admin.settings.notifications', 'view' => 'admin.settings.notifications', 'see' => 'الإشعارات والتذكيرات'],
            ['route' => 'admin.settings.website', 'view' => 'admin.settings.website', 'see' => 'الموقع والواجهة العامة'],
            ['route' => 'admin.settings.roles', 'view' => 'admin.settings.roles', 'see' => 'الأدوار والصلاحيات'],
            ['route' => 'admin.settings.users', 'view' => 'admin.settings.users', 'see' => 'إدارة المستخدمين'],
            ['route' => 'admin.settings.security', 'view' => 'admin.settings.security', 'see' => 'الأمان وسجل النظام'],
        ];

        foreach ($subpages as $item) {
            $response = $this->actingAs($this->admin)->get(route($item['route']));
            $response->assertStatus(200);
            $response->assertViewIs($item['view']);
            $response->assertSee($item['see']);
            // Verify sidebar contains all 8 sub-routes
            $response->assertSee(route('admin.settings.clinic'));
            $response->assertSee(route('admin.settings.booking'));
            $response->assertSee(route('admin.settings.working-hours'));
            $response->assertSee(route('admin.settings.notifications'));
            $response->assertSee(route('admin.settings.website'));
            $response->assertSee(route('admin.settings.roles'));
            $response->assertSee(route('admin.settings.users'));
            $response->assertSee(route('admin.settings.security'));
        }
    }

    public function test_non_admin_cannot_access_settings_subpages(): void
    {
        $routes = [
            'admin.settings.clinic',
            'admin.settings.booking',
            'admin.settings.working-hours',
            'admin.settings.notifications',
            'admin.settings.website',
            'admin.settings.roles',
            'admin.settings.users',
            'admin.settings.security',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->receptionist)->get(route($route));
            $response->assertStatus(403);
        }
    }

    public function test_admin_can_update_via_dedicated_post_routes(): void
    {
        // 1. Clinic update
        $resClinic = $this->actingAs($this->admin)->post(route('admin.settings.clinic.update'), [
            'clinic_name' => 'عيادة ريادة التخصصية الحديثة',
            'clinic_phone' => '0511223344',
            'clinic_email' => 'info@riyada-modern.com',
            'clinic_address' => 'الرياض - حي الياسمين',
        ]);
        $resClinic->assertRedirect(route('admin.settings.clinic'));
        $resClinic->assertSessionHas('success');
        $this->assertEquals('عيادة ريادة التخصصية الحديثة', Setting::get('clinic_name'));

        // 2. Booking update
        $resBooking = $this->actingAs($this->admin)->post(route('admin.settings.booking.update'), [
            'default_session_duration' => '45',
            'min_advance_booking_hours' => '3',
            'appointment_buffer_minutes' => '15',
            'max_daily_appointments_per_dentist' => '18',
            'cancellation_cutoff_hours' => '12',
            'confirmation_cutoff_hours' => '24',
            'prevent_overlapping_appointments' => '1',
        ]);
        $resBooking->assertRedirect(route('admin.settings.booking'));
        $resBooking->assertSessionHas('success');
        $this->assertEquals('45', Setting::get('default_session_duration'));
        $this->assertEquals('1', Setting::get('prevent_overlapping_appointments'));

        // 3. Working Hours update
        $resHours = $this->actingAs($this->admin)->post(route('admin.settings.working-hours.update'), [
            'days' => [
                ['name' => 'السبت', 'is_off' => '0', 'start' => '10:00', 'end' => '20:00'],
            ],
        ]);
        $resHours->assertRedirect(route('admin.settings.working-hours'));
        $resHours->assertSessionHas('success');
        $this->assertStringContainsString('10:00', Setting::get('working_hours'));

        // 4. Notifications update
        $resNotif = $this->actingAs($this->admin)->post(route('admin.settings.notifications.update'), [
            'sms_reminder_hours_before' => '12',
            'whatsapp_reminder' => '1',
            'auto_cancel_24h' => '0',
        ]);
        $resNotif->assertRedirect(route('admin.settings.notifications'));
        $resNotif->assertSessionHas('success');
        $this->assertEquals('12', Setting::get('sms_reminder_hours_before'));
        $this->assertEquals('0', Setting::get('auto_cancel_24h'));

        // 5. Website update
        $resWeb = $this->actingAs($this->admin)->post(route('admin.settings.website.update'), [
            'announcement_bar' => 'عرض خاص جديد في الموقع',
            'maintenance_mode' => '1',
        ]);
        $resWeb->assertRedirect(route('admin.settings.website'));
        $resWeb->assertSessionHas('success');
        $this->assertEquals('1', Setting::get('maintenance_mode'));

        // 6. Security update
        $resSec = $this->actingAs($this->admin)->post(route('admin.settings.security.update'), [
            'session_lifetime_minutes' => '60',
            'max_login_attempts' => '4',
            'allow_user_registration' => '0',
        ]);
        $resSec->assertRedirect(route('admin.settings.security'));
        $resSec->assertSessionHas('success');
        $this->assertEquals('60', Setting::get('session_lifetime_minutes'));
        $this->assertEquals('0', Setting::get('allow_user_registration'));

        // 7. Clear Cache action
        $resCache = $this->actingAs($this->admin)->post(route('admin.settings.security.clear-cache'));
        $resCache->assertRedirect(route('admin.settings.security'));
        $resCache->assertSessionHas('success');

        // 8. Clear Logs action
        $resLogs = $this->actingAs($this->admin)->post(route('admin.settings.security.clear-logs'));
        $resLogs->assertRedirect(route('admin.settings.security'));
        $resLogs->assertSessionHas('success');
    }
}
