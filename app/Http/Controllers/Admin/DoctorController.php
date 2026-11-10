<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DoctorController extends Controller
{
    /**
     * Display a listing of doctors.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $doctors = User::where('role', 'dentist')
            ->withCount('dentistAppointments')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('admin.doctors.index', compact('doctors', 'search'));
    }

    /**
     * Show the form for creating a new doctor.
     */
    public function create()
    {
        return view('admin.doctors.create');
    }

    /**
     * Store a newly created doctor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', Password::defaults()],
        ], [
            'name.required'     => 'يرجى إدخال اسم الطبيب الرباعي.',
            'email.required'    => 'يرجى إدخال البريد الإلكتروني.',
            'email.unique'      => 'هذا البريد الإلكتروني مسجل لطبيب أو مستخدم آخر.',
            'password.required' => 'يرجى إدخال كلمة المرور لحساب الطبيب.',
        ]);

        $validated['role'] = 'dentist';
        $validated['password'] = Hash::make($validated['password']);

        $newDoctor = User::create($validated);
        \App\Models\AuditLog::record('create_doctor', "إضافة طبيب استشاري جديد [{$newDoctor->name}] ({$newDoctor->email})", $newDoctor);

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', 'تمت إضافة الطبيب بنجاح ومنحه حساباً معتمداً بالنظام.');
    }

    /**
     * Show the form for editing the specified doctor.
     */
    public function edit(User $doctor)
    {
        abort_if($doctor->role !== 'dentist', 404);

        return view('admin.doctors.edit', compact('doctor'));
    }

    /**
     * Update the specified doctor in storage.
     */
    public function update(Request $request, User $doctor)
    {
        abort_if($doctor->role !== 'dentist', 404);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $doctor->id],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', Password::defaults()],
        ], [
            'name.required'  => 'يرجى إدخال اسم الطبيب.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.unique'   => 'هذا البريد الإلكتروني مستخدم بالفعل.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $oldValues = $doctor->only(['name', 'email', 'phone']);
        $doctor->update($validated);
        \App\Models\AuditLog::record('update_doctor', "تحديث بيانات الطبيب [{$doctor->name}]", $doctor, $oldValues, $doctor->only(['name', 'email', 'phone']));

        return redirect()
            ->route('admin.doctors.index')
            ->with('info', 'تم تحديث بيانات الطبيب بنجاح.');
    }

    /**
     * Remove the specified doctor from storage.
     */
    public function destroy(User $doctor)
    {
        abort_if($doctor->role !== 'dentist', 404);

        if ($doctor->dentistAppointments()->exists()) {
            return redirect()
                ->route('admin.doctors.index')
                ->with('error', 'لا يمكن حذف الطبيب لوجود سجلات ومواعيد مرضى مرتبطة به.');
        }

        \App\Models\AuditLog::record('delete_doctor', "حذف حساب الطبيب [{$doctor->name}] ({$doctor->email})", $doctor);
        $doctor->delete();

        return redirect()
            ->route('admin.doctors.index')
            ->with('error', 'تم حذف حساب الطبيب بنجاح.');
    }
}
