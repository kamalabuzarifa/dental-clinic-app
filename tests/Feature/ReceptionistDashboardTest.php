<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReceptionistDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $receptionist;
    protected User $dentist;
    protected User $patient;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $workingHours = [
            ['name' => 'السبت', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الأحد', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الإثنين', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الثلاثاء', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الأربعاء', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الخميس', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الجمعة', 'is_off' => true, 'start' => '09:00', 'end' => '21:00'],
        ];
        Setting::set('working_hours', json_encode($workingHours, JSON_UNESCAPED_UNICODE));

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_rcp@riyada.com'],
            ['name' => 'Admin RCP Test', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        $this->receptionist = User::firstOrCreate(
            ['email' => 'receptionist_rcp@riyada.com'],
            ['name' => 'Receptionist Test', 'password' => Hash::make('password'), 'role' => 'receptionist', 'phone' => '0551122334']
        );

        $this->dentist = User::firstOrCreate(
            ['email' => 'dentist_rcp@riyada.com'],
            ['name' => 'Dr. Dentist RCP Test', 'password' => Hash::make('password'), 'role' => 'dentist', 'phone' => '0559988776']
        );

        $this->patient = User::firstOrCreate(
            ['email' => 'patient_rcp@riyada.com'],
            ['name' => 'Patient RCP Test', 'password' => Hash::make('password'), 'role' => 'patient', 'phone' => '0501112233']
        );

        $this->service = Service::firstOrCreate(
            ['name' => 'تنظيف وتلميع الأسنان التجريبي'],
            ['price' => 250, 'duration_minutes' => 30, 'description' => 'جلسة وقائية']
        );
    }

    /**
     * 1. Test role-based dashboard redirection
     */
    public function test_dashboard_route_redirects_receptionist_to_receptionist_dashboard(): void
    {
        $response = $this->actingAs($this->receptionist)->get('/dashboard');
        $response->assertRedirect(route('receptionist.dashboard'));

        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertRedirect(route('admin.dashboard'));

        $response = $this->actingAs($this->patient)->get('/dashboard');
        $response->assertRedirect(route('patient.dashboard'));
    }

    /**
     * 2. Test RBAC access control and security protection
     */
    public function test_access_control_receptionist_and_other_roles(): void
    {
        // Guest is redirected to login
        $this->get(route('receptionist.dashboard'))->assertRedirect(route('login'));

        // Patient gets 403 Forbidden on receptionist routes
        $this->actingAs($this->patient)->get(route('receptionist.dashboard'))->assertForbidden();
        $this->actingAs($this->patient)->get(route('receptionist.patients.index'))->assertForbidden();

        // Receptionist gets 200 on receptionist dashboard
        $this->actingAs($this->receptionist)->get(route('receptionist.dashboard'))->assertOk();

        // Receptionist is blocked with 403 from admin routes
        $this->actingAs($this->receptionist)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($this->receptionist)->get(route('admin.doctors.index'))->assertForbidden();
        $this->actingAs($this->receptionist)->get(route('admin.settings.index'))->assertForbidden();
        $this->actingAs($this->receptionist)->get(route('admin.settings.clinic'))->assertForbidden();
        $this->actingAs($this->receptionist)->get(route('admin.reports.index'))->assertForbidden();
    }

    /**
     * 3. Test receptionist dashboard renders real statistics and sidebar items
     */
    public function test_receptionist_dashboard_renders_metrics_and_proper_sidebar(): void
    {
        $today = Carbon::today()->toDateString();

        // Create an appointment for today
        Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $today,
            'start_time'       => '11:00:00',
            'end_time'         => '11:30:00',
            'status'           => 'confirmed',
        ]);

        $response = $this->actingAs($this->receptionist)->get(route('receptionist.dashboard'));
        $response->assertOk();
        $response->assertSee('لوحة تحكم موظف الاستقبال');
        $response->assertSee('إجمالي مواعيد اليوم');
        $response->assertSee('مؤكدة اليوم');
        $response->assertSee('جدول مواعيد الأطباء');
        $response->assertSee('إدارة المرضى');

        // Verify admin-only links are NOT present in receptionist sidebar
        $response->assertDontSee(route('admin.settings.index'));
        $response->assertDontSee('إدارة الموقع الإلكتروني');
        $response->assertDontSee('سجل العمليات والأمان');
    }

    /**
     * 4. Test patient management: List, Search, Create, Edit, View History, and NO delete
     */
    public function test_receptionist_can_manage_patients_without_delete_capability(): void
    {
        // 1. List
        $response = $this->actingAs($this->receptionist)->get(route('receptionist.patients.index'));
        $response->assertOk();
        $response->assertSee($this->patient->name);

        // 2. Search
        $response = $this->actingAs($this->receptionist)->get(route('receptionist.patients.index', ['search' => $this->patient->phone]));
        $response->assertOk();
        $response->assertSee($this->patient->name);

        // 3. Create Patient
        $newEmail = 'new_patient_' . strtolower(Str::random(5)) . '@riyada.com';
        $createResponse = $this->actingAs($this->receptionist)->post(route('receptionist.patients.store'), [
            'name'     => 'مريض تجريبي للاستقبال',
            'email'    => $newEmail,
            'phone'    => '0544556677',
            'password' => 'secret123',
        ]);
        $createResponse->assertRedirect(route('receptionist.patients.index'));

        $newPatient = User::where('email', $newEmail)->first();
        $this->assertNotNull($newPatient);
        $this->assertEquals('patient', $newPatient->role);

        // Verify audit log recorded
        $this->assertTrue(AuditLog::where('action', 'create_patient')->where('auditable_id', $newPatient->id)->exists());

        // 4. View Patient Profile (Show)
        $showResponse = $this->actingAs($this->receptionist)->get(route('receptionist.patients.show', $newPatient->id));
        $showResponse->assertOk();
        $showResponse->assertSee($newPatient->name);
        $showResponse->assertSee('سجل المواعيد والزيارات');

        // 5. Edit and Update Patient
        $editResponse = $this->actingAs($this->receptionist)->get(route('receptionist.patients.edit', $newPatient->id));
        $editResponse->assertOk();

        $updateResponse = $this->actingAs($this->receptionist)->put(route('receptionist.patients.update', $newPatient->id), [
            'name'  => 'مريض معدل بواسطة الاستقبال',
            'email' => $newEmail,
            'phone' => '0544556688',
        ]);
        $updateResponse->assertRedirect(route('receptionist.patients.index'));
        $this->assertEquals('مريض معدل بواسطة الاستقبال', $newPatient->fresh()->name);

        // 6. Delete Patient is Forbidden (403)
        $deleteResponse = $this->actingAs($this->receptionist)->delete(url("/receptionist/patients/{$newPatient->id}"));
        $deleteResponse->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $newPatient->id]);
    }

    /**
     * 5. Test appointment creation, working hours enforcement, and double-booking conflict prevention
     */
    public function test_receptionist_appointment_booking_and_conflict_prevention(): void
    {
        // Tomorrow at 10:00 (Saturday to Thursday are normal working days)
        $date = Carbon::tomorrow();
        if ($date->isFriday()) {
            $date->addDay(); // move to Saturday
        }
        $targetDate = $date->toDateString();

        // 1. Successful booking
        $response = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '10:00',
            'status'           => 'confirmed',
            'notes'            => 'حجز من الاستقبال',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('receptionist.appointments.index'));

        $appointment = Appointment::where('dentist_id', $this->dentist->id)
            ->whereDate('appointment_date', $targetDate)
            ->latest('id')
            ->first();
        $this->assertNotNull($appointment);
        $this->assertEquals('10:30:00', $appointment->end_time);

        // 2. Double-booking conflict prevention for the same doctor at the same time
        $conflictPatient = User::create([
            'name'     => 'مريض متداخل',
            'email'    => 'conflict_' . strtolower(Str::random(5)) . '@riyada.com',
            'phone'    => '0599001122',
            'password' => Hash::make('password'),
            'role'     => 'patient',
        ]);

        $conflictResponse = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $conflictPatient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '10:15', // Overlaps with 10:00 - 10:30
            'status'           => 'confirmed',
        ]);
        $conflictResponse->assertSessionHasErrors(['start_time']);

        // 3. Status change to completed
        $statusResponse = $this->actingAs($this->receptionist)->patch(route('receptionist.appointments.status', $appointment->id), [
            'status' => 'completed',
        ]);
        $statusResponse->assertSessionHas('info');
        $this->assertEquals('completed', $appointment->fresh()->status);

        // 4. Attempting to cancel an already completed appointment is blocked by state machine
        $cancelCompletedResponse = $this->actingAs($this->receptionist)->patch(route('receptionist.appointments.cancel', $appointment->id));
        $cancelCompletedResponse->assertSessionHas('error');
        $this->assertEquals('completed', $appointment->fresh()->status);

        // 5. Create a separate confirmed appointment to test valid cancellation
        $cancellableApt = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '14:00:00',
            'end_time'         => '14:30:00',
            'status'           => 'confirmed',
        ]);
        $cancelResponse = $this->actingAs($this->receptionist)->patch(route('receptionist.appointments.cancel', $cancellableApt->id));
        $cancelResponse->assertSessionHas('error');
        $this->assertEquals('cancelled', $cancellableApt->fresh()->status);

        // 6. Delete Appointment is Forbidden (403)
        $deleteResponse = $this->actingAs($this->receptionist)->delete(url("/receptionist/appointments/{$appointment->id}"));
        $deleteResponse->assertForbidden();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
    }

    /**
     * 6. Test working hours & clinic off-days check
     */
    public function test_receptionist_appointment_respects_working_hours_and_off_days(): void
    {
        // Next Friday (standard clinic off-day in Saudi Arabia / settings)
        $nextFriday = Carbon::now()->next(Carbon::FRIDAY)->toDateString();

        $response = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $nextFriday,
            'start_time'       => '10:00',
            'status'           => 'confirmed',
        ]);

        $response->assertSessionHasErrors(['appointment_date']);

        // Test booking outside working hours (e.g. 03:00 AM on a working day)
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $outOfHoursResponse = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $nextMonday,
            'start_time'       => '03:00',
            'status'           => 'confirmed',
        ]);

        $outOfHoursResponse->assertSessionHasErrors(['start_time']);
    }

    /**
     * 7. Test doctors schedule / calendar view
     */
    public function test_doctors_calendar_renders_accurately_for_receptionist(): void
    {
        $response = $this->actingAs($this->receptionist)->get(route('receptionist.appointments.calendar'));
        $response->assertOk();
        $response->assertSee('جدول مواعيد الأطباء الشهري');
        $response->assertSee('السبت');
        $response->assertSee('الجمعة');

        // Test month navigation
        $nextMonth = now()->addMonth();
        $navResponse = $this->actingAs($this->receptionist)->get(route('receptionist.appointments.calendar', [
            'year'  => $nextMonth->year,
            'month' => $nextMonth->month,
        ]));
        $navResponse->assertOk();

        // Test doctor filter
        $docFilterResponse = $this->actingAs($this->receptionist)->get(route('receptionist.appointments.calendar', [
            'doctor_id' => $this->dentist->id,
        ]));
        $docFilterResponse->assertOk();
    }

    /**
     * 8. Test notifications center and IDOR protection
     */
    public function test_receptionist_notifications_center_and_idor_protection(): void
    {
        // 1. View Notifications
        $response = $this->actingAs($this->receptionist)->get(route('receptionist.notifications.index'));
        $response->assertOk();
        $response->assertSee('مركز الإشعارات والتنبيهات');

        // 2. Mark specific notification as read
        $notif = $this->receptionist->notifications()->create([
            'id'             => (string) Str::uuid(),
            'type'           => 'App\\Notifications\\ClinicNotification',
            'data'           => ['title' => 'تنبيه موعد', 'message' => 'تم حجز موعد جديد'],
            'read_at'        => null,
        ]);

        $markResponse = $this->actingAs($this->receptionist)->postJson(route('receptionist.notifications.read', $notif->id));
        $markResponse->assertOk();
        $markResponse->assertJson(['success' => true]);
        $this->assertNotNull($notif->fresh()->read_at);

        // 3. IDOR protection: cannot mark another user's notification
        $otherNotif = $this->admin->notifications()->create([
            'id'             => (string) Str::uuid(),
            'type'           => 'App\\Notifications\\ClinicNotification',
            'data'           => ['title' => 'إشعار إداري سري', 'message' => 'بيانات خاصة بالمدير'],
            'read_at'        => null,
        ]);

        $idorResponse = $this->actingAs($this->receptionist)->postJson(route('receptionist.notifications.read', $otherNotif->id));
        $idorResponse->assertNotFound();

        // 4. Mark all as read
        $markAllResponse = $this->actingAs($this->receptionist)->post(route('receptionist.notifications.mark-all-read'));
        $markAllResponse->assertRedirect();
        $this->assertEquals(0, $this->receptionist->unreadNotifications()->count());
    }

    /**
     * 9. Test receptionist personal profile management
     */
    public function test_receptionist_can_edit_and_update_profile(): void
    {
        $response = $this->actingAs($this->receptionist)->get(route('receptionist.profile.edit'));
        $response->assertOk();
        $response->assertSee('إعدادات الحساب وكلمة المرور');

        $updateResponse = $this->actingAs($this->receptionist)->patch(route('receptionist.profile.update'), [
            'name'  => 'موظف الاستقبال المحدث',
            'email' => $this->receptionist->email,
            'phone' => '0559998888',
        ]);
        $updateResponse->assertSessionHas('success');
        $this->assertEquals('موظف الاستقبال المحدث', $this->receptionist->fresh()->name);
        $this->assertEquals('0559998888', $this->receptionist->fresh()->phone);
    }

    /**
     * 10. Test receptionist live search endpoint
     */
    public function test_receptionist_live_search_endpoint_returns_json(): void
    {
        $response = $this->actingAs($this->receptionist)->getJson(route('receptionist.search', ['q' => mb_substr($this->patient->name, 0, 5)]));
        $response->assertOk();
        $response->assertJsonStructure(['patients', 'appointments', 'services']);
    }
}
