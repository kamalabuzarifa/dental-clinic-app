<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'is_active',
        'show_on_website',
        'specialty',
        'bio',
        'avatar',
        'sort_order',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'show_on_website'   => 'boolean',
            'sort_order'        => 'integer',
        ];
    }

    /**
     * Scope for active dentists displayed on public website.
     */
    public function scopeActiveDentistsForWebsite(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('role', 'dentist')
                     ->where('is_active', true)
                     ->where('show_on_website', true)
                     ->orderBy('sort_order', 'asc')
                     ->orderBy('name', 'asc');
    }

    /**
     * Appointments as a patient.
     */
    public function patientAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    /**
     * Appointments as a dentist.
     */
    public function dentistAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'dentist_id');
    }

    /**
     * Role checking helper functions.
     */
    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    public function isDentist(): bool
    {
        return $this->role === 'dentist';
    }

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has one of the given roles.
     *
     * @param string|array $roles
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    /**
     * Check if user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        // Admin has all permissions unconditionally
        if ($this->isAdmin()) {
            return true;
        }

        // Inactive users have no permissions
        if (isset($this->is_active) && !$this->is_active) {
            return false;
        }

        $allRolePermissions = Setting::get('role_permissions');
        if (is_string($allRolePermissions)) {
            $allRolePermissions = json_decode($allRolePermissions, true);
        }

        if (!is_array($allRolePermissions)) {
            $allRolePermissions = static::defaultRolePermissions();
        }

        $rolePerms = $allRolePermissions[$this->role] ?? [];

        return in_array($permission, $rolePerms, true);
    }

    /**
     * Default permissions matrix for roles.
     *
     * @return array
     */
    public static function defaultRolePermissions(): array
    {
        return [
            'admin' => [
                'dashboard.view',
                'dentists.view', 'dentists.create', 'dentists.edit', 'dentists.delete',
                'patients.view', 'patients.create', 'patients.edit', 'patients.delete', 'patients.view_medical_records',
                'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel', 'appointments.delete', 'appointments.confirm', 'appointments.change_status',
                'services.view', 'services.create', 'services.edit', 'services.delete', 'services.manage_prices',
                'reports.view', 'reports.export',
                'users.view', 'users.create', 'users.edit', 'users.delete', 'users.assign_roles',
                'settings.view', 'settings.edit', 'settings.manage_permissions',
            ],
            'receptionist' => [
                'dashboard.view',
                'dentists.view',
                'patients.view', 'patients.create', 'patients.edit',
                'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel', 'appointments.confirm', 'appointments.change_status',
                'services.view',
            ],
            'dentist' => [
                'dashboard.view',
                'patients.view', 'patients.view_medical_records',
                'appointments.view', 'appointments.change_status',
                'services.view',
            ],
            'patient' => [
                'dashboard.view',
                'appointments.view', 'appointments.create', 'appointments.cancel',
            ],
        ];
    }
}
