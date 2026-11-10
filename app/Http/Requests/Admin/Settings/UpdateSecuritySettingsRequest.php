<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSecuritySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'session_lifetime_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'max_login_attempts'       => ['required', 'integer', 'min:3', 'max:20'],
            'allow_user_registration'  => ['nullable'],
            'log_failed_logins'        => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'session_lifetime_minutes.required' => 'مدة الجلسة بالدقائق مطلوبة.',
            'max_login_attempts.required'       => 'الحد الأقصى لمحاولات الدخول مطلوب.',
        ];
    }
}
