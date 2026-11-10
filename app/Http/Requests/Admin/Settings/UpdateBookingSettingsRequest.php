<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'default_session_duration'           => ['required', 'in:15,30,45,60,90'],
            'min_advance_booking_hours'          => ['required', 'integer', 'min:0', 'max:168'],
            'appointment_buffer_minutes'         => ['required', 'integer', 'min:0', 'max:60'],
            'max_daily_appointments_per_dentist' => ['required', 'integer', 'min:1', 'max:100'],
            'cancellation_cutoff_hours'          => ['required', 'integer', 'min:0', 'max:72'],
            'confirmation_cutoff_hours'          => ['required', 'integer', 'min:0', 'max:72'],
            'prevent_overlapping_appointments'   => ['nullable'],
            'emergency_immediate_booking'        => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'default_session_duration.required'           => 'الرجاء اختيار مدة الجلسة الافتراضية.',
            'min_advance_booking_hours.required'          => 'يرجى تحديد أقل مهلة للحجز المسبق بالساعات.',
            'appointment_buffer_minutes.required'         => 'يرجى تحديد فاصل التعقيم بالدقائق.',
            'max_daily_appointments_per_dentist.required' => 'يرجى تحديد الحد الأقصى للمواعيد اليومية لكل طبيب.',
        ];
    }
}
