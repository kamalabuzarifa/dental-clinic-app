<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAppointmentsCalendarAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $dentist;
    protected User $patient;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->dentist = User::factory()->create([
            'name'      => 'د. معاذ الشوا',
            'role'      => 'dentist',
            'is_active' => true,
        ]);

        $this->patient = User::factory()->create([
            'name'      => 'أحمد رضوان',
            'phone'     => '0591234567',
            'role'      => 'patient',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name'             => 'تنظيف وتلميع جير',
            'price'            => 250,
            'duration_minutes' => 45,
        ]);
    }

    /**
     * Test 1: Calendar view renders with ZERO mock/fake random numbers when no appointments exist.
     */
    public function test_calendar_renders_clean_empty_state_without_fake_numbers(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.appointments.index', ['tab' => 'calendar']));

        $response->assertStatus(200);
        // Assert absence of any random or fake dummy numbers
        $response->assertDontSee('مواعيد مؤكدة');
        $response->assertSee('لا توجد أي مواعيد أو حجوزات مسجلة في شهر');
        $response->assertSee('إجمالي الشهر: <strong class="text-slate-900">0</strong>', false);
        $response->assertSee('مؤكد: <strong>0</strong>', false);
    }

    /**
     * Test 2: Calendar accurately renders real database appointments.
     */
    public function test_calendar_renders_real_database_appointments_accurately(): void
    {
        $now = now();
        $date = Carbon::create($now->year, $now->month, 10)->format('Y-m-d');

        $appointment = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $date,
            'start_time'       => '11:00:00',
            'end_time'         => '11:45:00',
            'status'           => 'confirmed',
            'notes'            => 'جلسة تنظيف وتلميع أسنان',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.appointments.index', [
            'tab'   => 'calendar',
            'year'  => $now->year,
            'month' => $now->month,
        ]));

        $response->assertStatus(200);
        $response->assertSee('إجمالي الشهر: <strong class="text-slate-900">1</strong>', false);
        $response->assertSee('مؤكد: <strong>1</strong>', false);
        $response->assertSee('1 مؤكد');
        $response->assertSee($this->patient->name);
        $response->assertSee($this->dentist->name);
        $response->assertSee($this->service->name);
    }

    /**
     * Test 3: Calendar respects doctor filter and filters out other doctors' appointments.
     */
    public function test_calendar_respects_doctor_filter(): void
    {
        $otherDentist = User::factory()->create([
            'name'      => 'د. سارة عادل',
            'role'      => 'dentist',
            'is_active' => true,
        ]);

        $now = now();
        $date = Carbon::create($now->year, $now->month, 12)->format('Y-m-d');

        // Appointment for Dentist 1
        Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $date,
            'start_time'       => '09:00:00',
            'end_time'         => '09:45:00',
            'status'           => 'confirmed',
        ]);

        // Appointment for Dentist 2
        Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $otherDentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $date,
            'start_time'       => '10:00:00',
            'end_time'         => '10:45:00',
            'status'           => 'confirmed',
        ]);

        // Filter by Dentist 1
        $response = $this->actingAs($this->admin)->get(route('admin.appointments.index', [
            'tab'       => 'calendar',
            'doctor_id' => $this->dentist->id,
            'year'      => $now->year,
            'month'     => $now->month,
        ]));

        $response->assertStatus(200);
        $response->assertSee('إجمالي الشهر: <strong class="text-slate-900">1</strong>', false);
        $response->assertSee($this->dentist->name);
        $response->assertDontSee('"dentist_name":"' . $otherDentist->name . '"', false);
    }

    /**
     * Test 4: Calendar respects status filter.
     */
    public function test_calendar_respects_status_filter(): void
    {
        $now = now();
        $date = Carbon::create($now->year, $now->month, 15)->format('Y-m-d');

        // Confirmed appointment
        Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $date,
            'start_time'       => '14:00:00',
            'end_time'         => '14:45:00',
            'status'           => 'confirmed',
        ]);

        // Pending appointment
        Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $date,
            'start_time'       => '16:00:00',
            'end_time'         => '16:45:00',
            'status'           => 'pending',
        ]);

        // Filter for pending only
        $response = $this->actingAs($this->admin)->get(route('admin.appointments.index', [
            'tab'    => 'calendar',
            'status' => 'pending',
            'year'   => $now->year,
            'month'  => $now->month,
        ]));

        $response->assertStatus(200);
        $response->assertSee('إجمالي الشهر: <strong class="text-slate-900">1</strong>', false);
        $response->assertSee('انتظار: <strong>1</strong>', false);
        $response->assertSee('1 انتظار');
    }

    /**
     * Test 5: Month navigation between previous, current, and next months.
     */
    public function test_calendar_month_navigation(): void
    {
        // Visit October 2026
        $octoberRes = $this->actingAs($this->admin)->get(route('admin.appointments.index', [
            'tab'   => 'calendar',
            'year'  => 2026,
            'month' => 10,
        ]));

        $octoberRes->assertStatus(200);
        $octoberRes->assertSee('أكتوبر 2026');

        // Visit December 2026
        $decemberRes = $this->actingAs($this->admin)->get(route('admin.appointments.index', [
            'tab'   => 'calendar',
            'year'  => 2026,
            'month' => 12,
        ]));

        $decemberRes->assertStatus(200);
        $decemberRes->assertSee('ديسمبر 2026');
    }

    /**
     * Test 6: Creating an appointment updates both the Table and the Calendar.
     */
    public function test_creating_appointment_updates_both_table_and_calendar(): void
    {
        $now = now();
        $date = Carbon::create($now->year, $now->month, 18)->format('Y-m-d');

        $response = $this->actingAs($this->admin)->post(route('admin.appointments.store'), [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $date,
            'start_time'       => '10:00',
            'status'           => 'confirmed',
            'notes'            => 'حجز جديد تم إنشاؤه من الإدارة',
        ]);

        $response->assertRedirect(route('admin.appointments.index'));

        // Verify in Table view
        $tableRes = $this->actingAs($this->admin)->get(route('admin.appointments.index', ['tab' => 'table']));
        $tableRes->assertSee($this->patient->name);
        $tableRes->assertSee($this->dentist->name);

        // Verify in Calendar view
        $calendarRes = $this->actingAs($this->admin)->get(route('admin.appointments.index', [
            'tab'   => 'calendar',
            'year'  => $now->year,
            'month' => $now->month,
        ]));
        $calendarRes->assertSee('1 مؤكد');
        $calendarRes->assertSee($this->patient->name);
    }

    /**
     * Test 7: Updating appointment status updates both Table and Calendar.
     */
    public function test_updating_appointment_status_updates_both_table_and_calendar(): void
    {
        $now = now();
        $date = Carbon::create($now->year, $now->month, 20)->format('Y-m-d');

        $appointment = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $date,
            'start_time'       => '11:00:00',
            'end_time'         => '11:45:00',
            'status'           => 'pending',
        ]);

        // Change status to completed
        $this->actingAs($this->admin)->patch(route('admin.appointments.status', $appointment), [
            'status' => 'completed',
        ]);

        $this->assertEquals('completed', $appointment->fresh()->status);

        // Verify Calendar view shows completed
        $calendarRes = $this->actingAs($this->admin)->get(route('admin.appointments.index', [
            'tab'   => 'calendar',
            'year'  => $now->year,
            'month' => $now->month,
        ]));

        $calendarRes->assertSee('مكتمل: <strong>1</strong>', false);
        $calendarRes->assertSee('1 مكتمل');
    }

    /**
     * Test 8: Deleting appointment immediately removes it from both Table and Calendar.
     */
    public function test_deleting_appointment_removes_from_both_table_and_calendar(): void
    {
        $now = now();
        $date = Carbon::create($now->year, $now->month, 22)->format('Y-m-d');

        $appointment = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $date,
            'start_time'       => '12:00:00',
            'end_time'         => '12:45:00',
            'status'           => 'confirmed',
        ]);

        $this->actingAs($this->admin)->delete(route('admin.appointments.destroy', $appointment));

        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);

        // Verify Calendar view has 0 appointments
        $calendarRes = $this->actingAs($this->admin)->get(route('admin.appointments.index', [
            'tab'   => 'calendar',
            'year'  => $now->year,
            'month' => $now->month,
        ]));

        $calendarRes->assertSee('إجمالي الشهر: <strong class="text-slate-900">0</strong>', false);
        $calendarRes->assertDontSee('1 مؤكد');
    }

    /**
     * Test 9: Non-admin users cannot access appointments management or calendar.
     */
    public function test_non_admin_cannot_access_appointments_calendar(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($patient)->get(route('admin.appointments.index'));
        $response->assertStatus(403);
    }
}
