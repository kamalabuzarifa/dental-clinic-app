<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWebsiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'announcement_bar' => ['nullable', 'string', 'max:500'],
            'social_twitter'   => ['nullable', 'string', 'max:255'],
            'social_instagram' => ['nullable', 'string', 'max:255'],
            'social_facebook'  => ['nullable', 'string', 'max:255'],
            'social_whatsapp'  => ['nullable', 'string', 'max:255'],
            'maintenance_mode' => ['nullable'],
        ];
    }
}
