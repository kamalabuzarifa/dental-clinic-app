<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ReceptionistProfileController extends Controller
{
    /**
     * Show the receptionist profile edit page.
     */
    public function edit(Request $request): View
    {
        $user = Auth::user();
        return view('receptionist.profile.edit', compact('user'));
    }

    /**
     * Update receptionist profile information and optional password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'name.required'      => 'يرجى إدخال الاسم الكريم.',
            'email.required'     => 'يرجى إدخال عنوان البريد الإلكتروني.',
            'email.unique'       => 'هذا البريد الإلكتروني مستخدم بالفعل.',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AuditLog::record(
            'update_profile',
            "تحديث الملف الشخصي لموظف الاستقبال [{$user->name}]",
            $user
        );

        return back()->with('success', 'تم تحديث بيانات الملف الشخصي بنجاح.');
    }
}
