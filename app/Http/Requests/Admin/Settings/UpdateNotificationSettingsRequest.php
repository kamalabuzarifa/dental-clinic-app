<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'sms_reminder_hours_before'  => ['required', 'integer', 'min:1', 'max:72'],
            'whatsapp_reminder'          => ['nullable'],
            'auto_cancel_24h'            => ['nullable'],
            'dentist_email_notification' => ['nullable'],
            'patient_confirmation_sms'   => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'sms_reminder_hours_before.required' => 'يرجى تحديد وقت إرسال التذكير.',
        ];
    }
}
