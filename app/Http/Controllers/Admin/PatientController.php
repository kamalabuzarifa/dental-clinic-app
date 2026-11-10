<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PatientController extends Controller
{
    /**
     * Display a listing of patients.
     */
    public function index(Request $request)
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
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.patients.index', compact('patients', 'search'));
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create()
    {
        return view('admin.patients.create');
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20'],
            'password' => ['nullable', Password::defaults()],
        ], [
            'name.required'  => 'يرجى إدخال اسم المريض.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني للمريض.',
            'email.unique'   => 'هذا البريد الإلكتروني مسجل لمريض آخر.',
            'phone.required' => 'يرجى إدخال رقم هاتف المريض.',
        ]);

        $validated['role'] = 'patient';
        $validated['password'] = Hash::make($validated['password'] ?? 'patient123');

        $newPatient = User::create($validated);
        \App\Models\AuditLog::record('create_patient', "فتح ملف طبي جديد للمريض [{$newPatient->name}] ({$newPatient->email})", $newPatient);

        return redirect()
            ->route('admin.patients.index')
            ->with('success', 'تم فتح ملف طبي جديد للمريض بنجاح.');
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(User $patient)
    {
        abort_if($patient->role !== 'patient', 404);

        return view('admin.patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, User $patient)
    {
        abort_if($patient->role !== 'patient', 404);

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

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $oldValues = $patient->only(['name', 'email', 'phone']);
        $patient->update($validated);
        \App\Models\AuditLog::record('update_patient', "تحديث بيانات الملف الطبي للمريض [{$patient->name}]", $patient, $oldValues, $patient->only(['name', 'email', 'phone']));

        return redirect()
            ->route('admin.patients.index')
            ->with('info', 'تم تحديث بيانات الملف الطبي للمريض بنجاح.');
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(User $patient)
    {
        abort_if($patient->role !== 'patient', 404);

        if ($patient->patientAppointments()->exists()) {
            return redirect()
                ->route('admin.patients.index')
                ->with('error', 'لا يمكن حذف ملف المريض لوجود حجوزات وسجلات علاجية مرتبطة به.');
        }

        \App\Models\AuditLog::record('delete_patient', "حذف وإغلاق الملف الطبي للمريض [{$patient->name}] ({$patient->email})", $patient);
        $patient->delete();

        return redirect()
            ->route('admin.patients.index')
            ->with('error', 'تم إغلاق وحذف ملف المريض بنجاح.');
    }
}
