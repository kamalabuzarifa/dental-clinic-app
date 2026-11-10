<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ReceptionistPatientController extends Controller
{
    /**
     * Display a listing of patients with live search, pagination, and appointment stats.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $patients = User::where('role', 'patient')
            ->withCount('patientAppointments')
            ->with(['patientAppointments' => function ($query) {
                $query->latest('appointment_date')->limit(1);
            }])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $totalPatientsCount = User::where('role', 'patient')->count();

        return view('receptionist.patients.index', compact('patients', 'search', 'totalPatientsCount'));
    }

    /**
     * Show the form for creating a new patient record.
     */
    public function create(): View
    {
        return view('receptionist.patients.create');
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20'],
            'password' => ['nullable', Password::defaults()],
        ], [
            'name.required'  => 'يرجى إدخال اسم المريض الرباعي.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني للمريض.',
            'email.unique'   => 'هذا البريد الإلكتروني مسجل مسبقاً لمريض آخر في المركز.',
            'phone.required' => 'يرجى إدخال رقم هاتف المريض للتواصل والتذكيرات.',
        ]);

        // Force patient role (prevent any privilege escalation)
        $validated['role'] = 'patient';
        $plainPassword = $validated['password'] ?? 'patient123';
        $validated['password'] = Hash::make($plainPassword);

        $patient = User::create($validated);

        // Security Audit Log
        AuditLog::record(
            'create_patient',
            "تسجيل مريض جديد بالعيادة بواسطة موظف الاستقبال: [{$patient->name}] ({$patient->phone})",
            $patient
        );

        return redirect()
            ->route('receptionist.patients.index')
            ->with('success', "تم فتح وتوثيق ملف المريض [{$patient->name}] بنجاح.");
    }

    /**
     * Display the specified patient's profile and administrative appointment history.
     * Note: Strict medical confidentiality is maintained. Confidential medical records/diagnoses
     * are strictly withheld and inaccessible to receptionists.
     */
    public function show(User $patient): View
    {
        abort_if($patient->role !== 'patient', 404, 'الملف المطلوب ليس مريضاً.');

        // Verify that receptionists cannot access confidential clinical records
        // Gate 'patients.view_medical_records' is denied to receptionists
        $canViewMedicalRecords = Gate::allows('patients.view_medical_records');

        // Only load safe administrative appointment history
        $appointments = $patient->patientAppointments()
            ->with([
                'dentist:id,name',
                'service:id,name,price,duration_minutes'
            ])
            ->latest('appointment_date')
            ->latest('start_time')
            ->paginate(10);

        // Administrative stats summary
        $stats = [
            'total'     => $patient->patientAppointments()->count(),
            'completed' => $patient->patientAppointments()->where('status', 'completed')->count(),
            'upcoming'  => $patient->patientAppointments()->where('appointment_date', '>=', now()->toDateString())->whereNotIn('status', ['cancelled'])->count(),
            'cancelled' => $patient->patientAppointments()->where('status', 'cancelled')->count(),
        ];

        return view('receptionist.patients.show', compact('patient', 'appointments', 'stats', 'canViewMedicalRecords'));
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(User $patient): View
    {
        abort_if($patient->role !== 'patient', 404, 'الملف المطلوب ليس مريضاً.');

        return view('receptionist.patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, User $patient): RedirectResponse
    {
        abort_if($patient->role !== 'patient', 404, 'الملف المطلوب ليس مريضاً.');

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $patient->id],
            'phone'    => ['required', 'string', 'max:20'],
            'password' => ['nullable', Password::defaults()],
        ], [
            'name.required'  => 'يرجى إدخال اسم المريض.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'phone.required' => 'يرجى إدخال رقم الهاتف.',
        ]);

        $updateData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role'  => 'patient', // Strict role preservation
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $oldValues = $patient->only(['name', 'email', 'phone']);
        $patient->update($updateData);

        // Security Audit Log
        AuditLog::record(
            'update_patient',
            "تحديث بيانات ملف المريض [{$patient->name}] بواسطة موظف الاستقبال",
            $patient,
            $oldValues,
            $patient->only(['name', 'email', 'phone'])
        );

        return redirect()
            ->route('receptionist.patients.index')
            ->with('info', "تم تحديث بيانات ملف المريض [{$patient->name}] بنجاح.");
    }

    /**
     * Remove the specified patient from storage.
     * Blocked for receptionist to protect patient records.
     */
    public function destroy(User $patient)
    {
        abort(403, 'حذف ملفات المرضى محظور لموظف الاستقبال، هذه العملية مخصصة لمدير النظام حصراً.');
    }
}
