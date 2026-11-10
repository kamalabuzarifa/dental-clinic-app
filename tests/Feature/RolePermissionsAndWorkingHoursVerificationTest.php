<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionsAndWorkingHoursVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $dentist;
    protected User $receptionist;
    protected User $patient;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'      => 'مدير النظام الأساسي',
            'email'     => 'admin@riyada-dental.com',
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->dentist = User::factory()->create([
            'name'      => 'د. طارق الحكيم',
            'email'     => 'dentist@riyada-dental.com',
            'role'      => 'dentist',
            'is_active' => true,
        ]);

        $this->receptionist = User::factory()->create([
            'name'      => 'سارة الاستقبال',
            'email'     => 'reception@riyada-dental.com',
            'role'      => 'receptionist',
            'is_active' => true,
        ]);

        $this->patient = User::factory()->create([
            'name'      => 'خالد منصور',
            'email'     => 'patient@riyada-dental.com',
            'role'      => 'patient',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name'             => 'كشف واستشارة طبية',
            'price'            => 100.00,
            'duration_minutes' => 30,
            'description'      => 'فحص سريري شامل وتشخيص لحالة الأسنان',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Role Permissions & Route Access Isolation
    |--------------------------------------------------------------------------
    */

    public function test_patient_cannot_access_admin_routes_directly(): void
    {
        $this->actingAs($this->patient);

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.settings.index'))->assertForbidden();
        $this->get(route('admin.appointments.index'))->assertForbidden();
        $this->get(route('admin.doctors.index'))->assertForbidden();
        $this->get(route('admin.patients.index'))->assertForbidden();
        $this->get(route('admin.services.index'))->assertForbidden();
        $this->get(route('admin.reports.index'))->assertForbidden();
    }

    public function test_dentist_cannot_access_admin_settings_or_manage_doctors(): void
    {
        $this->actingAs($this->dentist);

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.settings.index'))->assertForbidden();
        $this->get(route('admin.doctors.index'))->assertForbidden();
        $this->get(route('admin.settings.roles'))->assertForbidden();
        $this->get(route('admin.settings.security'))->assertForbidden();

        // Dentist has access to their own dashboard
        $this->get(route('dentist.dashboard'))->assertOk();
    }

    public function test_receptionist_cannot_access_admin_settings_or_reports(): void
    {
        $this->actingAs($this->receptionist);

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.settings.index'))->assertForbidden();
        $this->get(route('admin.settings.security'))->assertForbidden();

        // Receptionist has access to their own dashboard
        $this->get(route('receptionist.dashboard'))->assertOk();
    }

    public function test_admin_has_full_access_to_all_management_modules(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.appointments.index'))->assertOk();
        $this->get(route('admin.doctors.index'))->assertOk();
        $this->get(route('admin.patients.index'))->assertOk();
        $this->get(route('admin.services.index'))->assertOk();
        $this->get(route('admin.reports.index'))->assertOk();
        $this->get(route('admin.settings.index'))->assertOk();
        $this->get(route('admin.settings.roles'))->assertOk();
        $this->get(route('admin.settings.security'))->assertOk();
    }

    public function test_last_active_admin_account_is_strictly_protected(): void
    {
        $this->actingAs($this->admin);

        // 1. Admin cannot deactivate themselves
        $response1 = $this->post(route('admin.settings.users.toggle-status', $this->admin));
        $response1->assertSessionHas('error', 'لا يمكنك تعطيل حسابك الشخصي أثناء تسجيل الدخول به.');
        $this->admin->refresh();
        $this->assertTrue((bool)$this->admin->is_active);

        // 2. Admin cannot downgrade the sole active admin's role
        $response2 = $this->post(route('admin.settings.users.role', $this->admin), [
            'role' => 'receptionist',
        ]);
        $response2->assertSessionHas('error', 'لا يمكن تغيير دور هذا المستخدم: يجب أن يبقى على الأقل مدير نظام واحد نشط لحماية النظام.');
        $this->admin->refresh();
        $this->assertEquals('admin', $this->admin->role);
    }

    /*
    |--------------------------------------------------------------------------
    | Working Hours & Holiday Enforcement Tests
    |--------------------------------------------------------------------------
    */

    public function test_patient_booking_is_blocked_on_clinic_holidays_or_off_days(): void
    {
        // Setup working hours: Friday is OFF
        $workingHours = [
            ['name' => 'السبت', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الأحد', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الاثنين', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الثلاثاء', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الأربعاء', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الخميس', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الجمعة', 'is_off' => true,  'start' => '14:00', 'end' => '20:00'],
        ];
        Setting::set('working_hours', json_encode($workingHours));

        // Find the next Friday
        $nextFriday = Carbon::now()->next(Carbon::FRIDAY)->format('Y-m-d');

        $this->actingAs($this->patient);

        $response = $this->post(route('patient.appointments.store'), [
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $nextFriday,
            'start_time'       => '16:00',
        ]);

        $response->assertSessionHasErrors(['appointment_date']);
        $this->assertDatabaseMissing('appointments', [
            'patient_id'       => $this->patient->id,
            'appointment_date' => $nextFriday,
        ]);
    }

    public function test_patient_booking_is_blocked_outside_working_hours(): void
    {
        // Setup working hours: Sunday is open 09:00 to 17:00
        $workingHours = [
            ['name' => 'السبت', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الأحد', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الاثنين', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الثلاثاء', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الأربعاء', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الخميس', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الجمعة', 'is_off' => true,  'start' => '14:00', 'end' => '20:00'],
        ];
        Setting::set('working_hours', json_encode($workingHours));

        // Find the next Sunday
        $nextSunday = Carbon::now()->next(Carbon::SUNDAY)->format('Y-m-d');

        $this->actingAs($this->patient);

        // Try booking at 20:00 (outside 09:00 - 17:00)
        $response = $this->post(route('patient.appointments.store'), [
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $nextSunday,
            'start_time'       => '20:00',
        ]);

        $response->assertSessionHasErrors(['start_time']);
        $this->assertDatabaseMissing('appointments', [
            'patient_id'       => $this->patient->id,
            'appointment_date' => $nextSunday,
        ]);
    }

    public function test_patient_booking_succeeds_within_working_hours_on_working_day(): void
    {
        $workingHours = [
            ['name' => 'السبت', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الأحد', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الاثنين', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الثلاثاء', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الأربعاء', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الخميس', 'is_off' => false, 'start' => '09:00', 'end' => '17:00'],
            ['name' => 'الجمعة', 'is_off' => true,  'start' => '14:00', 'end' => '20:00'],
        ];
        Setting::set('working_hours', json_encode($workingHours));

        $nextSunday = Carbon::now()->next(Carbon::SUNDAY)->format('Y-m-d');

        $this->actingAs($this->patient);

        // Try booking at 10:00 (within 09:00 - 17:00)
        $response = $this->post(route('patient.appointments.store'), [
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $nextSunday,
            'start_time'       => '10:00',
        ]);

        $response->assertSessionHasNoErrors();
        $appointment = Appointment::where('patient_id', $this->patient->id)->latest('id')->first();
        $this->assertNotNull($appointment);
        $this->assertEquals($nextSunday, Carbon::parse($appointment->appointment_date)->format('Y-m-d'));
        $this->assertEquals('10:00:00', $appointment->start_time);
    }
}
