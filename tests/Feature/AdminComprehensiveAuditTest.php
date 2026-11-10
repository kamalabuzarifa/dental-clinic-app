<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminComprehensiveAuditTest extends TestCase
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
            'name'      => 'د. أحمد خليل',
            'role'      => 'dentist',
            'is_active' => true,
        ]);

        $this->patient = User::factory()->create([
            'name'      => 'محمد العمري',
            'role'      => 'patient',
            'is_active' => true,
        ]);

        $this->service = Service::create([
            'name'             => 'تنظيف وتلميع الأسنان',
            'price'            => 150.00,
            'duration_minutes' => 30,
            'description'      => 'جلسة تنظيف وتلميع لإزالة الجير والرواسب',
        ]);
    }

    public function test_admin_can_view_all_core_management_pages(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.appointments.index'))->assertOk();
        $this->get(route('admin.doctors.index'))->assertOk();
        $this->get(route('admin.patients.index'))->assertOk();
        $this->get(route('admin.services.index'))->assertOk();
        $this->get(route('admin.reports.index'))->assertOk();
        $this->get(route('admin.settings.index'))->assertOk();
    }

    public function test_admin_can_perform_full_crud_on_appointments(): void
    {
        $this->actingAs($this->admin);

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        // 1. Create
        $response = $this->post(route('admin.appointments.store'), [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $tomorrow,
            'start_time'       => '10:00',
            'status'           => 'confirmed',
            'notes'            => 'فحص دوري',
        ]);
        $response->assertRedirect(route('admin.appointments.index'));

        $appointment = Appointment::first();
        $this->assertNotNull($appointment);
        $this->assertEquals('confirmed', $appointment->status);

        // 2. Edit View
        $editResponse = $this->get(route('admin.appointments.edit', $appointment));
        $editResponse->assertOk();
        $editResponse->assertSee('تعديل بيانات حجز الموعد');
        $editResponse->assertSee($this->patient->name);

        // 3. Update
        $updateResponse = $this->put(route('admin.appointments.update', $appointment), [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $tomorrow,
            'start_time'       => '11:00',
            'status'           => 'completed',
            'notes'            => 'تمت المعالجة بنجاح',
        ]);
        $updateResponse->assertRedirect(route('admin.appointments.index'));

        $appointment->refresh();
        $this->assertEquals('completed', $appointment->status);
        $this->assertEquals('تمت المعالجة بنجاح', $appointment->notes);

        // 4. Quick Status Update
        $this->patch(route('admin.appointments.status', $appointment), [
            'status' => 'cancelled',
        ])->assertRedirect();
        $appointment->refresh();
        $this->assertEquals('cancelled', $appointment->status);

        // 5. Delete
        $deleteResponse = $this->delete(route('admin.appointments.destroy', $appointment));
        $deleteResponse->assertRedirect(route('admin.appointments.index'));
        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
    }

    public function test_admin_can_perform_full_crud_on_doctors(): void
    {
        $this->actingAs($this->admin);

        // Create
        $response = $this->post(route('admin.doctors.store'), [
            'name'                  => 'د. سارة المنصور',
            'email'                 => 'sara.mansoor@example.com',
            'phone'                 => '0555123456',
            'password'              => 'DoctorSecurePass123!',
            'password_confirmation' => 'DoctorSecurePass123!',
        ]);
        $response->assertRedirect(route('admin.doctors.index'));

        $doctor = User::where('email', 'sara.mansoor@example.com')->first();
        $this->assertNotNull($doctor);
        $this->assertEquals('dentist', $doctor->role);

        // Edit
        $this->get(route('admin.doctors.edit', $doctor))->assertOk();

        // Update
        $this->put(route('admin.doctors.update', $doctor), [
            'name'  => 'د. سارة المنصور - استشاري تقويم',
            'email' => 'sara.mansoor@example.com',
            'phone' => '0555999888',
        ])->assertRedirect(route('admin.doctors.index'));

        $doctor->refresh();
        $this->assertEquals('د. سارة المنصور - استشاري تقويم', $doctor->name);

        // Delete doctor with no appointments
        $this->delete(route('admin.doctors.destroy', $doctor))->assertRedirect(route('admin.doctors.index'));
        $this->assertDatabaseMissing('users', ['id' => $doctor->id]);
    }

    public function test_doctor_with_appointments_cannot_be_deleted(): void
    {
        $this->actingAs($this->admin);

        Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time'       => '14:00:00',
            'end_time'         => '14:30:00',
            'status'           => 'confirmed',
        ]);

        $response = $this->delete(route('admin.doctors.destroy', $this->dentist));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->dentist->id]);
    }

    public function test_admin_can_perform_full_crud_on_patients(): void
    {
        $this->actingAs($this->admin);

        // Create
        $response = $this->post(route('admin.patients.store'), [
            'name'     => 'فاطمة الزهراء',
            'email'    => 'fatima@example.com',
            'phone'    => '0501112233',
            'password' => 'Password123!',
        ]);
        $response->assertRedirect(route('admin.patients.index'));

        $patient = User::where('email', 'fatima@example.com')->first();
        $this->assertNotNull($patient);
        $this->assertEquals('patient', $patient->role);

        // Edit
        $this->get(route('admin.patients.edit', $patient))->assertOk();

        // Update
        $this->put(route('admin.patients.update', $patient), [
            'name'  => 'فاطمة الزهراء الشامي',
            'email' => 'fatima@example.com',
            'phone' => '0509998877',
        ])->assertRedirect(route('admin.patients.index'));

        $patient->refresh();
        $this->assertEquals('فاطمة الزهراء الشامي', $patient->name);

        // Delete patient with no appointments
        $this->delete(route('admin.patients.destroy', $patient))->assertRedirect(route('admin.patients.index'));
        $this->assertDatabaseMissing('users', ['id' => $patient->id]);
    }

    public function test_admin_can_perform_full_crud_on_services(): void
    {
        $this->actingAs($this->admin);

        // Create
        $response = $this->post(route('admin.services.store'), [
            'name'             => 'زراعة الأسنان الفورية',
            'price'            => 2500.00,
            'duration_minutes' => 60,
            'description'      => 'زراعة فورية باستخدام أحدث التقنيات السويسرية',
        ]);
        $response->assertRedirect(route('admin.services.index'));

        $service = Service::where('name', 'زراعة الأسنان الفورية')->first();
        $this->assertNotNull($service);

        // Edit
        $this->get(route('admin.services.edit', $service))->assertOk();

        // Update
        $this->put(route('admin.services.update', $service), [
            'name'             => 'زراعة الأسنان الفورية المتقدمة',
            'price'            => 2800.00,
            'duration_minutes' => 75,
            'description'      => 'تحديث الوصف والتقنية',
        ])->assertRedirect(route('admin.services.index'));

        $service->refresh();
        $this->assertEquals('زراعة الأسنان الفورية المتقدمة', $service->name);
        $this->assertEquals(2800.00, $service->price);

        // Delete
        $this->delete(route('admin.services.destroy', $service))->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_search_endpoint_returns_json_results(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson(route('admin.search', ['q' => 'أحمد']));
        $response->assertOk();
        $data = $response->json();
        $this->assertArrayHasKey('dentists', $data);
        $this->assertNotEmpty($data['dentists']);
        $this->assertEquals('د. أحمد خليل', $data['dentists'][0]['name']);
    }

    public function test_patient_booking_respects_maintenance_mode(): void
    {
        Setting::set('maintenance_mode', '1');

        $this->actingAs($this->patient);

        $response = $this->post(route('patient.appointments.store'), [
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time'       => '12:00',
        ]);

        $response->assertSessionHas('error', 'نظام حجز المواعيد معلق حالياً لأعمال الصيانة.');
    }

    public function test_patient_booking_respects_allow_patient_cancel_setting(): void
    {
        $appointment = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => Carbon::tomorrow()->format('Y-m-d'),
            'start_time'       => '15:00:00',
            'end_time'         => '15:30:00',
            'status'           => 'pending',
        ]);

        // When cancellation is disallowed
        Setting::set('allow_patient_cancel', '0');

        $this->actingAs($this->patient);
        $response = $this->patch(route('patient.appointments.cancel', $appointment));
        $response->assertSessionHas('error', 'إلغاء المواعيد ذاتياً من قِبل المريض غير متاح حالياً وفق سياسة العيادة. يرجى التواصل هاتفياً مع إدارة المركز.');
        $appointment->refresh();
        $this->assertEquals('pending', $appointment->status);

        // When cancellation is allowed
        Setting::set('allow_patient_cancel', '1');
        $response = $this->patch(route('patient.appointments.cancel', $appointment));
        $response->assertSessionHas('success');
        $appointment->refresh();
        $this->assertEquals('cancelled', $appointment->status);
    }

    public function test_patient_booking_respects_auto_confirm_setting(): void
    {
        Setting::set('maintenance_mode', '0');
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        // When auto_confirm is 0 -> creates pending
        Setting::set('auto_confirm_bookings', '0');
        $this->actingAs($this->patient);
        $res = $this->post(route('patient.appointments.store'), [
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $tomorrow,
            'start_time'       => '09:00',
        ]);
        $res->assertSessionHasNoErrors();
        $this->assertDatabaseCount('appointments', 1);
        $app1 = Appointment::first();
        $this->assertNotNull($app1);
        $this->assertEquals('pending', $app1->status);

        // When auto_confirm is 1 -> creates confirmed
        Setting::set('auto_confirm_bookings', '1');
        $res2 = $this->post(route('patient.appointments.store'), [
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $tomorrow,
            'start_time'       => '11:00',
        ]);
        $res2->assertSessionHasNoErrors();
        $this->assertDatabaseCount('appointments', 2);
        $app2 = Appointment::latest('id')->first();
        $this->assertEquals('confirmed', $app2->status);
    }
}
