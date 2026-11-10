<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Sensitive keys that must be stripped out before storing.
     */
    protected static array $sensitiveKeys = [
        'password',
        'password_confirmation',
        'remember_token',
        'token',
        '_token',
        'api_key',
        'secret',
    ];

    /**
     * User who triggered the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an audit event safely and immutably.
     */
    public static function record(
        string $action,
        string $description,
        ?Model $target = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?User $user = null
    ): self {
        $currentUser = $user ?? Auth::user();

        return static::create([
            'user_id'        => $currentUser?->id,
            'user_name'      => $currentUser?->name ?? 'زائر / النظام',
            'action'         => $action,
            'auditable_type' => $target ? get_class($target) : null,
            'auditable_id'   => $target?->getKey(),
            'description'    => $description,
            'ip_address'     => Request::ip(),
            'user_agent'     => substr((string) Request::userAgent(), 0, 500),
            'old_values'     => static::sanitize($oldValues),
            'new_values'     => static::sanitize($newValues),
            'created_at'     => now(),
        ]);
    }

    /**
     * Sanitize array by masking or removing sensitive keys.
     */
    protected static function sanitize(?array $data): ?array
    {
        if (is_null($data)) {
            return null;
        }

        $sanitized = [];
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), static::$sensitiveKeys, true)) {
                $sanitized[$key] = '********';
            } elseif (is_array($value)) {
                $sanitized[$key] = static::sanitize($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
