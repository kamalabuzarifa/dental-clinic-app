<?php

namespace App\Providers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Register Gates for System Permissions.
         * Admin role bypasses all checks via Gate::before.
         */
        Gate::before(function ($user) {
            if ($user && $user->role === 'admin') {
                return true;
            }
        });

        $abilities = [
            'dashboard.view',
            'dentists.view', 'dentists.create', 'dentists.edit', 'dentists.delete',
            'patients.view', 'patients.create', 'patients.edit', 'patients.delete', 'patients.view_medical_records',
            'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel', 'appointments.delete', 'appointments.confirm', 'appointments.change_status',
            'services.view', 'services.create', 'services.edit', 'services.delete', 'services.manage_prices',
            'reports.view', 'reports.export',
            'users.view', 'users.create', 'users.edit', 'users.delete', 'users.assign_roles',
            'settings.view', 'settings.edit', 'settings.manage_permissions',
        ];

        foreach ($abilities as $ability) {
            Gate::define($ability, function ($user) use ($ability) {
                return $user->hasPermission($ability);
            });
        }

        /**
         * View Composer: inject persistent database notifications into admin and receptionist layouts.
         */
        View::composer(['layouts.admin', 'layouts.receptionist'], function ($view) {
            if (auth()->check()) {
                try {
                    $user = auth()->user();

                    // Sync recent appointments as database notifications if user has 0 notifications
                    \App\Services\NotificationService::syncRecentAppointmentsForUser($user);

                    $dbNotifications = $user->notifications()->take(10)->get();

                    $defaultLink = $user->role === 'receptionist'
                        ? route('receptionist.appointments.index')
                        : route('admin.appointments.index');

                    $formattedNotifications = $dbNotifications->map(function ($n) use ($defaultLink) {
                        $data = $n->data;
                        return [
                            'id'      => (string) $n->id,
                            'title'   => $data['title'] ?? 'إشعار إداري',
                            'message' => $data['message'] ?? '',
                            'link'    => $data['link'] ?? $defaultLink,
                            'date'    => $n->created_at ? $n->created_at->diffForHumans() : 'الآن',
                            'read'    => !is_null($n->read_at),
                            'type'    => $data['icon'] ?? 'appointment',
                        ];
                    });

                    $view->with('adminNotifications', $formattedNotifications);
                    $view->with('unreadNotificationsCount', $user->unreadNotifications()->count());
                } catch (\Throwable $e) {
                    // Fall back to empty collection if table is not yet migrated
                    $view->with('adminNotifications', collect());
                    $view->with('unreadNotificationsCount', 0);
                }
            } else {
                $view->with('adminNotifications', collect());
                $view->with('unreadNotificationsCount', 0);
            }
        });
    }
}
