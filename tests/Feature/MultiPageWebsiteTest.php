<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiPageWebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure clean test settings
        Setting::set('clinic_name', 'مركز ريادة لطب وجراحة الأسنان');
        Setting::set('clinic_phone', '0596482371');
        Setting::set('clinic_email', 'infoclini@riyadaclinic.com');
        Setting::set('clinic_address', 'فلسطين - غزة - مفترق السرايا - برج الإتقان');
        Setting::set('maintenance_mode', '0');

        Service::firstOrCreate(
            ['name' => 'كشف واستشارة طبية عامة'],
            ['description' => 'فحص شامل لصحة الفم والأسنان', 'price' => 150, 'duration_minutes' => 30]
        );

        User::firstOrCreate(
            ['email' => 'dr.multipage.default@clinic.com'],
            ['name' => 'د. محمد على شراب', 'phone' => '0594534219', 'role' => 'dentist', 'password' => bcrypt('password'), 'is_active' => true]
        );
    }

    /**
     * Test 1: Home page (/) can be rendered directly and contains core sections.
     */
    public function test_home_page_can_be_rendered_directly(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('مركز ريادة لطب وجراحة الأسنان');
        $response->assertSee('ابتسامة مشرقة وصحية');
        $response->assertSee(route('about'));
        $response->assertSee(route('services.index'));
        $response->assertSee(route('doctors.index'));
        $response->assertSee(route('appointments.book'));
        $response->assertSee(route('contact'));
    }

    /**
     * Test 2: About page (/about) can be rendered directly with vision and mission.
     */
    public function test_about_page_can_be_rendered_directly(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('من نحن');
        $response->assertSee('رؤيتنا');
        $response->assertSee('رسالتنا');
        $response->assertSee('أهدافنا وقيمنا');
        $response->assertSee('برج الإتقان');
    }

    /**
     * Test 3: Services page (/services) displays real services from database.
     */
    public function test_services_index_page_displays_database_services(): void
    {
        $service = Service::firstOrCreate(
            ['name' => 'تنظيف وتلميع تجريبي'],
            ['description' => 'تنظيف جير متخصص', 'price' => 200, 'duration_minutes' => 30]
        );

        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertSee($service->name);
        $response->assertSee(number_format($service->price, 0));
        $response->assertSee(route('services.show', $service->id));
        $response->assertSee(route('appointments.book', ['service' => $service->id]));
    }

    /**
     * Test 4: Service details page (/services/{service}) with Route Model Binding.
     */
    public function test_service_details_page_renders_with_model_binding(): void
    {
        $service = Service::firstOrCreate(
            ['name' => 'تبييض أسنان احترافي'],
            ['description' => 'تبييض ليزر بارد متطور', 'price' => 750, 'duration_minutes' => 45]
        );

        $response = $this->get("/services/{$service->id}");

        $response->assertStatus(200);
        $response->assertSee($service->name);
        $response->assertSee('مراحل وخطوات الجلسة العلاجية');
        $response->assertSee(number_format($service->price, 0));
        $response->assertSee(route('appointments.book', ['service' => $service->id]));
    }

    /**
     * Test 5: Doctors page (/doctors) displays active dentists.
     */
    public function test_doctors_index_page_displays_active_dentists(): void
    {
        $doctor = User::firstOrCreate(
            ['email' => 'dr.test.multipage@clinic.com'],
            ['name' => 'د. خالد العمري', 'phone' => '0591112233', 'role' => 'dentist', 'password' => bcrypt('password'), 'is_active' => true]
        );

        $response = $this->get('/doctors');

        $response->assertStatus(200);
        $response->assertSee($doctor->name);
        $response->assertSee(route('doctors.show', $doctor->id));
        $response->assertSee(route('appointments.book', ['doctor' => $doctor->id]));
    }

    /**
     * Test 6: Doctor details page (/doctors/{doctor}) renders for dentists and 404s for non-dentists.
     */
    public function test_doctor_details_page_renders_and_guards_non_dentists(): void
    {
        $doctor = User::firstOrCreate(
            ['email' => 'dr.profile.test@clinic.com'],
            ['name' => 'د. مصطفى الشريف', 'phone' => '0592223344', 'role' => 'dentist', 'password' => bcrypt('password'), 'is_active' => true]
        );

        $patient = User::firstOrCreate(
            ['email' => 'patient.fake.doctor@clinic.com'],
            ['name' => 'مريض تجريبي عادي', 'phone' => '0593334455', 'role' => 'patient', 'password' => bcrypt('password'), 'is_active' => true]
        );

        // Valid dentist details page
        $response = $this->get("/doctors/{$doctor->id}");
        $response->assertStatus(200);
        $response->assertSee($doctor->name);
        $response->assertSee('نبذة عن الطبيب والخبرة السريرية');
        $response->assertSee(route('appointments.book', ['doctor' => $doctor->id]));

        // Non-dentist user must return 404
        $invalidResponse = $this->get("/doctors/{$patient->id}");
        $invalidResponse->assertStatus(404);
    }

    /**
     * Test 7: Appointments booking page (/appointments/book) renders and respects query params.
     */
    public function test_appointments_book_page_renders_with_preselected_parameters(): void
    {
        $service = Service::first();
        $doctor = User::where('role', 'dentist')->first();

        $response = $this->get("/appointments/book?service={$service->id}&doctor={$doctor->id}");

        $response->assertStatus(200);
        $response->assertSee('حجز موعدك الطبي مؤكد بالدقيقة');
        $response->assertSee($service->name);
        $response->assertSee($doctor->name);
    }

    /**
     * Test 8: Guest submitting booking is safely prompted to login without 500 error.
     */
    public function test_guest_submitting_appointment_redirects_to_login(): void
    {
        $service = Service::first();
        $doctor = User::where('role', 'dentist')->first();

        $response = $this->post('/appointments/book', [
            'dentist_id' => $doctor->id,
            'service_id' => $service->id,
            'appointment_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'start_time' => '10:00',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('warning');
    }

    /**
     * Test 9: Authenticated patient booking from /appointments/book validates and creates appointment.
     */
    public function test_authenticated_patient_can_book_from_public_booking_page(): void
    {
        $patient = User::firstOrCreate(
            ['email' => 'patient.booking.multipage@clinic.com'],
            ['name' => 'سامي أحمد', 'phone' => '0599988776', 'role' => 'patient', 'password' => bcrypt('password'), 'is_active' => true]
        );

        $doctor = User::where('role', 'dentist')->first();
        $service = Service::first();

        // Pick next Saturday to guarantee within working hours
        $bookingDate = Carbon::now()->next(Carbon::SATURDAY)->format('Y-m-d');

        $response = $this->actingAs($patient)->post('/appointments/book', [
            'dentist_id' => $doctor->id,
            'service_id' => $service->id,
            'appointment_date' => $bookingDate,
            'start_time' => '10:00',
            'notes' => 'حجز تجريبي من صفحة الحجز العامة',
        ]);

        $response->assertRedirect(route('patient.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'dentist_id' => $doctor->id,
            'service_id' => $service->id,
            'start_time' => '10:00:00',
        ]);
        $savedAppointment = Appointment::where('patient_id', $patient->id)->latest('id')->first();
        $this->assertNotNull($savedAppointment);
        $this->assertEquals(Carbon::parse($bookingDate)->format('Y-m-d'), Carbon::parse($savedAppointment->appointment_date)->format('Y-m-d'));
    }

    /**
     * Test 10: Contact page (/contact) renders contact info and message form.
     */
    public function test_contact_page_renders_directly(): void
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
        $response->assertSee('تواصل معنا');
        $response->assertSee('0596482371');
        $response->assertSee('infoclini@riyadaclinic.com');
        $response->assertSee('أرسل لنا رسالة أو استفساراً');
        $response->assertSee(route('contact.submit'));
    }

    /**
     * Test 11: Contact message submission validates and logs inquiry.
     */
    public function test_contact_submission_validates_and_stores_inquiry(): void
    {
        $response = $this->post('/contact', [
            'name' => 'عبدالله خليل',
            'phone' => '0597778899',
            'email' => 'abdallah@example.com',
            'subject' => 'استشارة طبية عامة',
            'message' => 'أود الاستفسار عن إمكانية تركيب تقويم شفاف ومواعيده.',
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'contact_message',
        ]);
    }

    /**
     * Test 12: Unified Navbar and Footer with Active State highlighting.
     */
    public function test_navbar_and_footer_active_states_on_different_pages(): void
    {
        // 1. Visit /about -> should have active class on About link
        $aboutRes = $this->get('/about');
        $aboutRes->assertStatus(200);
        $aboutRes->assertSee('bg-blue-50 text-blue-700 font-black shadow-xs', false);

        // 2. Visit /services -> should have active class on Services link
        $servicesRes = $this->get('/services');
        $servicesRes->assertStatus(200);
        $servicesRes->assertSee('bg-blue-50 text-blue-700 font-black shadow-xs', false);

        // 3. Visit /contact -> should have active class on Contact link
        $contactRes = $this->get('/contact');
        $contactRes->assertStatus(200);
        $contactRes->assertSee('bg-blue-50 text-blue-700 font-black shadow-xs', false);
    }
}
