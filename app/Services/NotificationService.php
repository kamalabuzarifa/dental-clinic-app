<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use App\Notifications\AppointmentNotification;

class NotificationService
{
    /**
     * Notify administrators and receptionists of appointment events without duplication.
     */
    public static function notifyStaff(Appointment $appointment, string $action = 'created', ?string $customMessage = null): void
    {
        $staff = User::whereIn('role', ['admin', 'receptionist'])
            ->where('is_active', true)
            ->get();

        foreach ($staff as $user) {
            // Check for duplicate notification to prevent spamming
            $exists = $user->notifications()
                ->where('data->appointment_id', $appointment->id)
                ->where('data->action', $action)
                ->where('data->status', $appointment->status)
                ->exists();

            if (!$exists) {
                $user->notify(new AppointmentNotification($appointment, $action, $customMessage));
            }
        }
    }

    /**
     * Backfill recent appointments as database notifications if user has 0 notifications.
     */
    public static function syncRecentAppointmentsForUser(User $user): void
    {
        if ($user->notifications()->count() > 0) {
            return;
        }

        $recent = Appointment::with(['patient', 'dentist', 'service'])
            ->latest('id')
            ->take(5)
            ->get();

        foreach ($recent as $appointment) {
            $user->notify(new AppointmentNotification($appointment, 'created'));
        }
    }
}
