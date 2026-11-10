<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            \App\Models\AuditLog::record('failed_login', "فشل تسجيل الدخول للبريد [{$this->input('email')}]");
            \Illuminate\Support\Facades\Log::warning("فشل تسجيل الدخول للبريد [{$this->input('email')}] من العنوان [{$this->ip()}]");

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $user = Auth::user();
        if ($user && isset($user->is_active) && !$user->is_active) {
            \App\Models\AuditLog::record('blocked_login', "محاولة دخول بحساب معطل: [{$user->email}]", $user, null, null, $user);
            \Illuminate\Support\Facades\Log::warning("محاولة دخول بحساب معطل: [{$user->email}] من العنوان [{$this->ip()}]");
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'هذا الحساب معطل حالياً من قِبل إدارة المركز. يرجى مراجعة المسؤول.',
            ]);
        }

        \App\Models\AuditLog::record('login', "تسجيل دخول ناجح للمستخدم [{$user->name}] - الدور: [{$user->role}]", $user, null, null, $user);
        \Illuminate\Support\Facades\Log::info("تسجيل دخول ناجح للمستخدم [{$user->name}] - الدور: [{$user->role}] من العنوان [{$this->ip()}]");

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        $maxAttempts = (int) \App\Models\Setting::get('max_login_attempts', 5);
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $maxAttempts ?: 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
