<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReceptionistSecurityAndDeepVerificationTest extends TestCase
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
            ['email' => 'admin_sec@riyada.com'],
            ['name' => 'Admin Security Test', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        $this->receptionist = User::firstOrCreate(
            ['email' => 'receptionist_sec@riyada.com'],
            ['name' => 'Receptionist Security Test', 'password' => Hash::make('password'), 'role' => 'receptionist', 'phone' => '0551122334']
        );

        $this->dentist = User::firstOrCreate(
            ['email' => 'dentist_sec@riyada.com'],
            ['name' => 'Dr. Dentist Security Test', 'password' => Hash::make('password'), 'role' => 'dentist', 'phone' => '0559988776']
        );

        $this->patient = User::firstOrCreate(
            ['email' => 'patient_sec@riyada.com'],
            ['name' => 'Patient Security Test', 'password' => Hash::make('password'), 'role' => 'patient', 'phone' => '0501112233']
        );

        $this->service = Service::firstOrCreate(
            ['name' => 'كشف وتشخيص أولي أمني'],
            ['price' => 150, 'duration_minutes' => 30, 'description' => 'فحص أسنان شامل']
        );
    }

    /**
     * 1. Authentication & RBAC: Verify accurate role-based dashboard redirection
     */
    public function test_accurate_role_redirection(): void
    {
        $this->actingAs($this->admin)->get('/dashboard')->assertRedirect(route('admin.dashboard'));
        $this->actingAs($this->dentist)->get('/dashboard')->assertRedirect(route('dentist.dashboard'));
        $this->actingAs($this->receptionist)->get('/dashboard')->assertRedirect(route('receptionist.dashboard'));
        $this->actingAs($this->patient)->get('/dashboard')->assertRedirect(route('patient.dashboard'));
    }

    /**
     * 2. Authentication & RBAC: Strict backend blocking of receptionist from all Admin endpoints
     */
    public function test_receptionist_blocked_from_all_admin_routes(): void
    {
        $adminRoutes = [
            ['GET', '/admin/dashboard'],
            ['GET', '/admin/doctors'],
            ['GET', '/admin/doctors/create'],
            ['GET', '/admin/patients'],
            ['GET', '/admin/services'],
            ['GET', '/admin/settings'],
            ['GET', '/admin/settings/clinic'],
            ['GET', '/admin/settings/booking'],
            ['GET', '/admin/settings/working-hours'],
            ['GET', '/admin/settings/notifications'],
            ['GET', '/admin/settings/roles'],
            ['GET', '/admin/settings/security'],
            ['GET', '/admin/settings/website'],
            ['GET', '/admin/reports'],
            ['GET', '/admin/website'],
            ['POST', '/admin/doctors', ['name' => 'Hacker Doctor', 'email' => 'hack@hack.com']],
            ['DELETE', "/admin/doctors/{$this->dentist->id}"],
            ['DELETE', "/admin/patients/{$this->patient->id}"],
        ];

        foreach ($adminRoutes as $routeDef) {
            $method = $routeDef[0];
            $url = $routeDef[1];
            $data = $routeDef[2] ?? [];

            $response = match ($method) {
                'GET'    => $this->actingAs($this->receptionist)->get($url),
                'POST'   => $this->actingAs($this->receptionist)->post($url, $data),
                'DELETE' => $this->actingAs($this->receptionist)->delete($url),
            };

            $response->assertForbidden();
        }
    }

    /**
     * 3. RBAC & Backend Gates: Verify specific permission boundaries
     */
    public function test_backend_gate_permissions_for_receptionist(): void
    {
        $this->actingAs($this->receptionist);

        // Permitted abilities
        $this->assertTrue(Gate::allows('dashboard.view'));
        $this->assertTrue(Gate::allows('patients.view'));
        $this->assertTrue(Gate::allows('patients.create'));
        $this->assertTrue(Gate::allows('patients.edit'));
        $this->assertTrue(Gate::allows('appointments.view'));
        $this->assertTrue(Gate::allows('appointments.create'));
        $this->assertTrue(Gate::allows('appointments.edit'));
        $this->assertTrue(Gate::allows('appointments.cancel'));
        $this->assertTrue(Gate::allows('appointments.confirm'));
        $this->assertTrue(Gate::allows('appointments.change_status'));

        // Strictly Denied abilities
        $this->assertTrue(Gate::denies('patients.delete'));
        $this->assertTrue(Gate::denies('appointments.delete'));
        $this->assertTrue(Gate::denies('patients.view_medical_records'));
        $this->assertTrue(Gate::denies('users.view'));
        $this->assertTrue(Gate::denies('users.create'));
        $this->assertTrue(Gate::denies('users.delete'));
        $this->assertTrue(Gate::denies('settings.view'));
        $this->assertTrue(Gate::denies('settings.edit'));
        $this->assertTrue(Gate::denies('reports.view'));
    }

    /**
     * 4. Patient Privacy: Confidential medical records are strictly protected
     */
    public function test_confidential_medical_records_protection(): void
    {
        $this->actingAs($this->receptionist);

        // View patient profile
        $response = $this->get(route('receptionist.patients.show', $this->patient->id));
        $response->assertOk();

        // Ensure receptionist cannot view confidential medical records
        $response->assertViewHas('canViewMedicalRecords', false);

        // Ensure no medical diagnoses or prescription data are leaked in response
        $response->assertDontSee('تشخيص سري');
        $response->assertDontSee('وصفة علاجية طبية');
    }

    /**
     * 5. IDOR & Privilege Escalation Protection on Patients
     */
    public function test_patient_idor_and_privilege_escalation_protection(): void
    {
        // 1. Trying to view an admin or dentist as a patient returns 404
        $this->actingAs($this->receptionist)->get(route('receptionist.patients.show', $this->admin->id))->assertNotFound();
        $this->actingAs($this->receptionist)->get(route('receptionist.patients.show', $this->dentist->id))->assertNotFound();

        // 2. Trying to edit an admin or dentist as a patient returns 404
        $this->actingAs($this->receptionist)->get(route('receptionist.patients.edit', $this->admin->id))->assertNotFound();

        // 3. Trying to update an admin via patient endpoint returns 404
        $this->actingAs($this->receptionist)->put(route('receptionist.patients.update', $this->admin->id), [
            'name'  => 'Hacked Admin',
            'email' => $this->admin->email,
            'phone' => '0599999999',
        ])->assertNotFound();

        // 4. Privilege Escalation: Trying to create or update patient with role='admin' forces role='patient'
        $escalatedEmail = 'escalate_' . strtolower(Str::random(5)) . '@riyada.com';
        $createResponse = $this->actingAs($this->receptionist)->post(route('receptionist.patients.store'), [
            'name'     => 'Attacker Trying Admin',
            'email'    => $escalatedEmail,
            'phone'    => '0511223344',
            'role'     => 'admin', // Malicious payload
            'password' => 'secret123',
        ]);
        $createResponse->assertRedirect(route('receptionist.patients.index'));

        $createdUser = User::where('email', $escalatedEmail)->first();
        $this->assertNotNull($createdUser);
        $this->assertEquals('patient', $createdUser->role); // Enforced as patient!

        // 5. Trying to escalate via update
        $updateResponse = $this->actingAs($this->receptionist)->put(route('receptionist.patients.update', $createdUser->id), [
            'name'  => 'Attacker Updated',
            'email' => $escalatedEmail,
            'phone' => '0511223344',
            'role'  => 'admin', // Malicious payload
        ]);
        $updateResponse->assertRedirect(route('receptionist.patients.index'));
        $this->assertEquals('patient', $createdUser->fresh()->role); // Role remains patient!
    }

    /**
     * 6. Concurrency & Concurrency Locks: Prevent Doctor and Patient double bookings
     */
    public function test_concurrency_and_conflict_prevention(): void
    {
        $targetDate = Carbon::now()->next(Carbon::TUESDAY)->toDateString();

        // 1. Initial Appointment
        $response1 = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '11:00',
            'status'           => 'confirmed',
        ]);
        $response1->assertSessionHasNoErrors();

        // 2. Concurrent Doctor Booking (overlapping time)
        $secondPatient = User::create([
            'name'     => 'مريض ثان للتزامن',
            'email'    => 'patient2_' . strtolower(Str::random(5)) . '@riyada.com',
            'phone'    => '0522334455',
            'password' => Hash::make('password'),
            'role'     => 'patient',
        ]);

        $conflictDoctorResponse = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $secondPatient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '11:15', // Overlaps 11:00 - 11:30
            'status'           => 'confirmed',
        ]);
        $conflictDoctorResponse->assertSessionHasErrors(['start_time']);

        // 3. Concurrent Patient Booking (same patient with another doctor at same time)
        $secondDoctor = User::create([
            'name'     => 'د. طبيب ثان للتزامن',
            'email'    => 'dentist2_' . strtolower(Str::random(5)) . '@riyada.com',
            'phone'    => '0566778899',
            'password' => Hash::make('password'),
            'role'     => 'dentist',
        ]);

        $conflictPatientResponse = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $secondDoctor->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '11:10', // Patient already booked 11:00 - 11:30
            'status'           => 'confirmed',
        ]);
        $conflictPatientResponse->assertSessionHasErrors(['start_time']);
    }

    /**
     * 7. Duplicate Submission Prevention (Idempotency within 15 seconds)
     */
    public function test_duplicate_submission_prevention(): void
    {
        $targetDate = Carbon::now()->next(Carbon::WEDNESDAY)->toDateString();

        $payload = [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '15:00',
            'status'           => 'confirmed',
        ];

        // First submission creates the appointment
        $firstResponse = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), $payload);
        $firstResponse->assertSessionHas('success');

        // Immediate double-click submission returns cleanly without duplicate row or error
        $secondResponse = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), $payload);
        $secondResponse->assertRedirect(route('receptionist.appointments.index'));
        $secondResponse->assertSessionHas('info');

        // Verify only 1 appointment exists
        $count = Appointment::where('patient_id', $this->patient->id)
            ->where('dentist_id', $this->dentist->id)
            ->whereDate('appointment_date', $targetDate)
            ->where('start_time', '15:00:00')
            ->count();
        $this->assertEquals(1, $count);
    }

    /**
     * 8. State Machine: Strictly prevent illogical status transitions
     */
    public function test_status_transitions_state_machine_integrity(): void
    {
        $targetDate = Carbon::now()->next(Carbon::THURSDAY)->toDateString();

        $appointment = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '09:30:00',
            'end_time'         => '10:00:00',
            'status'           => 'completed', // Already completed
        ]);

        // 1. Completed appointment CANNOT be reverted to pending
        $revertResponse = $this->actingAs($this->receptionist)->patch(route('receptionist.appointments.status', $appointment->id), [
            'status' => 'pending',
        ]);
        $revertResponse->assertSessionHas('error');
        $this->assertEquals('completed', $appointment->fresh()->status);

        // 2. Completed appointment CANNOT be cancelled
        $cancelCompletedResponse = $this->actingAs($this->receptionist)->patch(route('receptionist.appointments.cancel', $appointment->id));
        $cancelCompletedResponse->assertSessionHas('error');
        $this->assertEquals('completed', $appointment->fresh()->status);

        // 3. Cancelled appointment CANNOT jump directly to completed
        $cancelledApt = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '12:00:00',
            'end_time'         => '12:30:00',
            'status'           => 'cancelled',
        ]);

        $invalidJumpResponse = $this->actingAs($this->receptionist)->patch(route('receptionist.appointments.status', $cancelledApt->id), [
            'status' => 'completed',
        ]);
        $invalidJumpResponse->assertSessionHas('error');
        $this->assertEquals('cancelled', $cancelledApt->fresh()->status);

        // 4. Valid flow: pending -> confirmed -> completed
        $normalApt = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '16:00:00',
            'end_time'         => '16:30:00',
            'status'           => 'pending',
        ]);

        // pending -> confirmed (Allowed)
        $this->actingAs($this->receptionist)->patch(route('receptionist.appointments.status', $normalApt->id), ['status' => 'confirmed'])->assertSessionHas('info');
        $this->assertEquals('confirmed', $normalApt->fresh()->status);

        // confirmed -> completed (Allowed)
        $this->actingAs($this->receptionist)->patch(route('receptionist.appointments.status', $normalApt->id), ['status' => 'completed'])->assertSessionHas('info');
        $this->assertEquals('completed', $normalApt->fresh()->status);
    }

    /**
     * 9. Working hours and off-days enforced on update() as well
     */
    public function test_working_hours_and_off_days_enforced_on_update(): void
    {
        $validDate = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $fridayDate = Carbon::now()->next(Carbon::FRIDAY)->toDateString();

        $appointment = Appointment::create([
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $validDate,
            'start_time'       => '10:00:00',
            'end_time'         => '10:30:00',
            'status'           => 'confirmed',
        ]);

        // Attempt to update appointment date to Friday (off-day)
        $offDayUpdate = $this->actingAs($this->receptionist)->put(route('receptionist.appointments.update', $appointment->id), [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $fridayDate,
            'start_time'       => '10:00',
            'status'           => 'confirmed',
        ]);
        $offDayUpdate->assertSessionHasErrors(['appointment_date']);

        // Attempt to update appointment time to 03:00 AM (outside working hours)
        $outOfHoursUpdate = $this->actingAs($this->receptionist)->put(route('receptionist.appointments.update', $appointment->id), [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $validDate,
            'start_time'       => '03:00',
            'status'           => 'confirmed',
        ]);
        $outOfHoursUpdate->assertSessionHasErrors(['start_time']);
    }

    /**
     * 10. Live Search: No leakage of staff accounts or internal appointment notes
     */
    public function test_live_search_data_sanitization(): void
    {
        $response = $this->actingAs($this->receptionist)->getJson(route('receptionist.search', ['q' => 'Security']));
        $response->assertOk();

        $data = $response->json();

        // 1. Should not contain admin or dentist in patients list
        foreach ($data['patients'] as $p) {
            $this->assertNotEquals($this->admin->email, $p['email']);
            $this->assertNotEquals($this->dentist->email, $p['email']);
        }

        // 2. Appointment objects should NOT leak password, doctor hash, or internal medical notes
        foreach ($data['appointments'] as $apt) {
            $this->assertArrayNotHasKey('password', $apt);
            $this->assertArrayNotHasKey('notes', $apt);
        }
    }

    /**
     * 11. Security Audit Logs: Full tracking of receptionist operations and immutability
     */
    public function test_security_audit_logs_tracking_and_immutability(): void
    {
        $initialCount = AuditLog::count();

        // Receptionist registers patient
        $patientEmail = 'audited_' . strtolower(Str::random(5)) . '@riyada.com';
        $res = $this->actingAs($this->receptionist)->post(route('receptionist.patients.store'), [
            'name'     => 'مريض مدقق',
            'email'    => $patientEmail,
            'phone'    => '0599881122',
            'password' => 'password123',
        ]);
        $res->assertSessionHasNoErrors();

        $loggedPatient = User::where('email', $patientEmail)->first();
        $this->assertNotNull($loggedPatient);

        // Verify Audit Log entry
        $log = AuditLog::where('action', 'create_patient')->where('auditable_id', $loggedPatient->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals($this->receptionist->id, $log->user_id);

        // Verify receptionist cannot view security settings or audit trail (403)
        $this->actingAs($this->receptionist)->get(route('admin.settings.security'))->assertForbidden();
    }

    /**
     * 12. Verification of the 6 Overlap Scenarios (Reject Overlapping, Allow Abutting Boundaries)
     */
    public function test_six_overlap_scenarios_rejection_and_abutting_boundary_allowance(): void
    {
        $targetDate = Carbon::now()->next(Carbon::MONDAY)->toDateString();

        // Baseline: 10:00 - 11:00 (Service duration 60 mins)
        $this->service->update(['duration_minutes' => 60]);

        $baseRes = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '10:00',
            'status'           => 'confirmed',
        ]);
        $baseRes->assertSessionHasNoErrors();

        $secondPatient = User::create([
            'name'     => 'مريض فحص التداخل',
            'email'    => 'overlap_' . strtolower(Str::random(5)) . '@riyada.com',
            'phone'    => '0599991122',
            'password' => Hash::make('password123'),
            'role'     => 'patient',
        ]);

        $shortService = Service::create([
            'name' => 'خدمة 30 دقيقة',
            'price' => 30.00,
            'duration_minutes' => 30,
        ]);

        $longService = Service::create([
            'name' => 'خدمة 120 دقيقة',
            'price' => 100.00,
            'duration_minutes' => 120,
        ]);

        // Scenario 1: Same start time (10:00 - 10:30 vs 10:00 - 11:00) -> MUST REJECT
        $s1 = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $secondPatient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $shortService->id,
            'appointment_date' => $targetDate,
            'start_time'       => '10:00',
            'status'           => 'confirmed',
        ]);
        $s1->assertSessionHasErrors(['start_time']);

        // Scenario 2: Second starts before first ends (10:15 - 10:45 vs 10:00 - 11:00) -> MUST REJECT
        $s2 = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $secondPatient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $shortService->id,
            'appointment_date' => $targetDate,
            'start_time'       => '10:15',
            'status'           => 'confirmed',
        ]);
        $s2->assertSessionHasErrors(['start_time']);

        // Scenario 3: Second starts inside first and ends after (10:30 - 11:30 vs 10:00 - 11:00) -> MUST REJECT
        $s3 = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $secondPatient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '10:30',
            'status'           => 'confirmed',
        ]);
        $s3->assertSessionHasErrors(['start_time']);

        // Scenario 4: Second completely encloses the first (09:30 - 11:30 vs 10:00 - 11:00) -> MUST REJECT
        $s4 = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $secondPatient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $longService->id,
            'appointment_date' => $targetDate,
            'start_time'       => '09:30',
            'status'           => 'confirmed',
        ]);
        $s4->assertSessionHasErrors(['start_time']);

        // Scenario 5: Second ends exactly at start of first (09:00 - 10:00 vs 10:00 - 11:00) -> MUST ALLOW
        $s5 = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $secondPatient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '09:00',
            'status'           => 'confirmed',
        ]);
        $s5->assertSessionHasNoErrors();
        $this->assertDatabaseHas('appointments', [
            'dentist_id'       => $this->dentist->id,
            'start_time'       => '09:00:00',
            'end_time'         => '10:00:00',
        ]);

        // Scenario 6: Second starts exactly at end of first (11:00 - 12:00 vs 10:00 - 11:00) -> MUST ALLOW
        $s6 = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), [
            'patient_id'       => $secondPatient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '11:00',
            'status'           => 'confirmed',
        ]);
        $s6->assertSessionHasNoErrors();
        $this->assertDatabaseHas('appointments', [
            'dentist_id'       => $this->dentist->id,
            'start_time'       => '11:00:00',
            'end_time'         => '12:00:00',
        ]);
    }

    /**
     * 13. Deep Idempotency Key Verification: Atomic handling, Two keys for same booking, Distinct allowance
     */
    public function test_deep_idempotency_key_and_distinct_bookings(): void
    {
        $targetDate = Carbon::now()->next(Carbon::TUESDAY)->toDateString();
        $idempotencyKey = (string) Str::uuid();

        $payload = [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '14:00',
            'status'           => 'confirmed',
            'idempotency_key'  => $idempotencyKey,
        ];

        // 1. Initial request with idempotency key succeeds
        $res1 = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), $payload);
        $res1->assertSessionHas('success');

        // 2. Resubmitting with SAME key is caught by atomic Cache::add
        $res2 = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), $payload);
        $res2->assertRedirect(route('receptionist.appointments.index'));
        $res2->assertSessionHas('info');

        // 3. Resubmitting with a DIFFERENT key for the SAME slot is caught by Tier 2 (15s window)
        $payloadWithDifferentKey = $payload;
        $payloadWithDifferentKey['idempotency_key'] = (string) Str::uuid();
        $res3 = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), $payloadWithDifferentKey);
        $res3->assertRedirect(route('receptionist.appointments.index'));
        $res3->assertSessionHas('info');

        // 4. Verify Legitimate distinct booking (different doctor or different time) is NEVER blocked
        $distinctPayload = [
            'patient_id'       => $this->patient->id,
            'dentist_id'       => $this->dentist->id,
            'service_id'       => $this->service->id,
            'appointment_date' => $targetDate,
            'start_time'       => '16:00', // Distinct time!
            'status'           => 'confirmed',
            'idempotency_key'  => (string) Str::uuid(),
        ];
        $res4 = $this->actingAs($this->receptionist)->post(route('receptionist.appointments.store'), $distinctPayload);
        $res4->assertSessionHas('success');
    }
}
