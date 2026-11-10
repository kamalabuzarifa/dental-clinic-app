<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $dentist;
    protected User $receptionist;
    protected User $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'مدير النظام',
            'email' => 'admin@clinic.com',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->dentist = User::factory()->create([
            'name' => 'د. أحمد',
            'email' => 'dentist@clinic.com',
            'role' => 'dentist',
            'is_active' => true,
        ]);

        $this->receptionist = User::factory()->create([
            'name' => 'موظف الاستقبال',
            'email' => 'recep@clinic.com',
            'role' => 'receptionist',
            'is_active' => true,
        ]);

        $this->patient = User::factory()->create([
            'name' => 'محمد خالد',
            'email' => 'patient@clinic.com',
            'role' => 'patient',
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/settings');
        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_settings(): void
    {
        $response = $this->actingAs($this->receptionist)->get('/admin/settings');
        $response->assertStatus(403);

        $responseDentist = $this->actingAs($this->dentist)->get('/admin/settings');
        $responseDentist->assertStatus(403);
    }

    public function test_admin_can_view_settings_hub(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/settings');
        $response->assertStatus(200);
        $response->assertViewIs('admin.settings.index');
        $response->assertViewHasAll([
            'settings',
            'workingHours',
            'rolesDefinition',
            'userCountsByRole',
            'permissionModules',
            'rolePermissions',
            'users',
            'systemInfo',
        ]);
        $response->assertSee('إعدادات النظام والمركز');
        $response->assertSee('بيانات المركز والشعار');
        $response->assertSee('الحجوزات والمواعيد');
        $response->assertSee('الأدوار والصلاحيات');
        $response->assertSee('إدارة المستخدمين');
    }

    public function test_admin_can_update_general_settings(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/settings', [
            'clinic_name' => 'مركز ريادة الدولي المطور',
            'clinic_phone' => '0599998888',
            'clinic_email' => 'contact@riyada.com',
            'clinic_address' => 'الرياض - طريق الملك عبد العزيز',
            'default_session_duration' => '45',
            'min_advance_booking_hours' => '4',
            'appointment_buffer_minutes' => '15',
            'max_daily_appointments_per_dentist' => '25',
            'cancellation_cutoff_hours' => '24',
            'confirmation_cutoff_hours' => '12',
            'announcement_bar' => 'عرض خاص جديد لفترة محدودة',
            'social_twitter' => 'https://x.com/riyada_new',
            'maintenance_mode' => '1',
            'whatsapp_reminder' => '1',
            'auto_cancel_24h' => '0',
            'allow_dentist_edit_prices' => '1',
            'session_lifetime_minutes' => '180',
            'max_login_attempts' => '6',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('مركز ريادة الدولي المطور', Setting::get('clinic_name'));
        $this->assertEquals('0599998888', Setting::get('clinic_phone'));
        $this->assertEquals('45', Setting::get('default_session_duration'));
        $this->assertEquals('1', Setting::get('maintenance_mode'));
        $this->assertEquals('0', Setting::get('auto_cancel_24h'));
        $this->assertEquals('1', Setting::get('allow_dentist_edit_prices'));
        $this->assertEquals('180', Setting::get('session_lifetime_minutes'));
    }

    public function test_admin_can_update_working_hours(): void
    {
        $daysData = [
            ['name' => 'السبت', 'is_off' => '0', 'start' => '08:00', 'end' => '22:00'],
            ['name' => 'الأحد', 'is_off' => '0', 'start' => '08:00', 'end' => '22:00'],
            ['name' => 'الجمعة', 'is_off' => '1', 'start' => '14:00', 'end' => '20:00'],
        ];

        $response = $this->actingAs($this->admin)->post('/admin/settings', [
            'days' => $daysData,
        ]);

        $response->assertSessionHas('success');
        $stored = json_decode(Setting::get('working_hours'), true);
        $this->assertIsArray($stored);
        $this->assertEquals('السبت', $stored[0]['name']);
        $this->assertEquals('08:00', $stored[0]['start']);
        $this->assertTrue($stored[2]['is_off']);
    }

    public function test_admin_can_update_role_permissions_and_core_admin_perms_are_preserved(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/settings/roles', [
            'permissions' => [
                'admin' => ['dashboard.view'], // Even if someone tries to strip admin perms
                'dentist' => ['dashboard.view', 'appointments.view', 'appointments.change_status'],
                'receptionist' => ['dashboard.view', 'patients.view', 'appointments.create'],
            ],
        ]);

        $response->assertSessionHas('success');
        $stored = json_decode(Setting::get('role_permissions'), true);

        // Core admin permissions must be automatically preserved
        $this->assertContains('dashboard.view', $stored['admin']);
        $this->assertContains('settings.manage_permissions', $stored['admin']);
        $this->assertContains('settings.view', $stored['admin']);
        $this->assertContains('users.assign_roles', $stored['admin']);

        // Dentist permissions updated
        $this->assertContains('appointments.change_status', $stored['dentist']);
    }

    public function test_last_active_admin_cannot_be_downgraded(): void
    {
        // $this->admin is the only admin
        $response = $this->actingAs($this->admin)->post("/admin/settings/users/{$this->admin->id}/role", [
            'role' => 'receptionist',
        ]);

        $response->assertSessionHas('error');
        $this->admin->refresh();
        $this->assertEquals('admin', $this->admin->role);
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/settings/users/{$this->admin->id}/toggle-status");
        $response->assertSessionHas('error');
        $this->admin->refresh();
        $this->assertTrue($this->admin->is_active);
    }

    public function test_admin_can_toggle_receptionist_status(): void
    {
        $this->assertTrue($this->receptionist->is_active);

        $response = $this->actingAs($this->admin)->post("/admin/settings/users/{$this->receptionist->id}/toggle-status");
        $response->assertSessionHas('info');
        $this->receptionist->refresh();
        $this->assertFalse($this->receptionist->is_active);

        // Toggle back to active
        $this->actingAs($this->admin)->post("/admin/settings/users/{$this->receptionist->id}/toggle-status");
        $this->receptionist->refresh();
        $this->assertTrue($this->receptionist->is_active);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $this->receptionist->update(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => $this->receptionist->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_reset_user_password(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/settings/users/{$this->receptionist->id}/reset-password", [
            'password' => 'NewSecretPassword123!',
            'password_confirmation' => 'NewSecretPassword123!',
        ]);

        $response->assertSessionHas('success');
        $this->receptionist->refresh();
        $this->assertTrue(Hash::check('NewSecretPassword123!', $this->receptionist->password));
    }

    public function test_registration_disabled_setting_blocks_registration(): void
    {
        Setting::set('allow_user_registration', '0');

        $response = $this->get('/register');
        $response->assertRedirect('/login');

        $postResponse = $this->post('/register', [
            'name' => 'مريض جديد',
            'email' => 'newpatient@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $postResponse->assertRedirect('/login');
        $this->assertDatabaseMissing('users', ['email' => 'newpatient@test.com']);
    }
}
