<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClinicSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'clinic_name'    => ['required', 'string', 'max:255'],
            'clinic_phone'   => ['required', 'string', 'max:50'],
            'clinic_email'   => ['required', 'email', 'max:255'],
            'clinic_address' => ['nullable', 'string', 'max:500'],
            'clinic_logo'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'clinic_name.required'  => 'اسم المركز / العيادة مطلوب.',
            'clinic_phone.required' => 'هاتف التواصل المباشر مطلوب.',
            'clinic_email.required' => 'البريد الإلكتروني للعيادة مطلوب.',
            'clinic_email.email'    => 'صيغة البريد الإلكتروني غير صحيحة.',
            'clinic_logo.image'     => 'يجب أن يكون ملف الشعار صورة صالحة.',
            'clinic_logo.max'       => 'الحد الأقصى لحجم الشعار هو 2 ميجابايت.',
        ];
    }
}
