<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkingHoursSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'days'           => ['required', 'array', 'min:1'],
            'days.*.name'    => ['required', 'string'],
            'days.*.is_off'  => ['nullable'],
            'days.*.start'   => ['nullable', 'string'],
            'days.*.end'     => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'days.required' => 'جدول أيام العمل مطلوب.',
        ];
    }
}
