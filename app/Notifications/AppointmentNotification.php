<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentNotification extends Notification
{
    use Queueable;

    public Appointment $appointment;
    public string $action;
    public string $title;
    public string $message;
    public string $icon;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment, string $action = 'created', ?string $customMessage = null)
    {
        $this->appointment = $appointment;
        $this->action = $action;

        $patientName = $appointment->patient?->name ?? 'مريض';
        $serviceName = $appointment->service?->name ?? 'خدمة طبية';
        $dentistName = $appointment->dentist?->name ?? 'طبيب';

        switch ($action) {
            case 'cancelled':
                $this->title = 'إلغاء موعد طبي';
                $this->message = $customMessage ?? "تم إلغاء موعد المريض ({$patientName}) لدى ({$dentistName}) لخدمة ({$serviceName}).";
                $this->icon = 'cancel';
                break;

            case 'status_updated':
                $statusLabels = [
                    'confirmed' => 'تأكيد الحجز',
                    'completed' => 'اكتمال الجلسة',
                    'no_show'   => 'عدم حضور المريض',
                    'pending'   => 'قيد المراجعة',
                ];
                $label = $statusLabels[$appointment->status] ?? $appointment->status;
                $this->title = "تحديث حالة الموعد: {$label}";
                $this->message = $customMessage ?? "تم تحديث حالة موعد ({$patientName}) إلى ({$label}).";
                $this->icon = 'status';
                break;

            case 'created':
            default:
                $this->title = 'حجز موعد طبي جديد';
                $this->message = $customMessage ?? "تم حجز موعد جديد للمريض ({$patientName}) - خدمة ({$serviceName}) لدى ({$dentistName}).";
                $this->icon = 'appointment';
                break;
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id'   => $this->appointment->id,
            'patient_id'       => $this->appointment->patient_id,
            'patient_name'     => $this->appointment->patient?->name ?? 'مريض',
            'dentist_name'     => $this->appointment->dentist?->name ?? 'طبيب',
            'service_name'     => $this->appointment->service?->name ?? 'خدمة',
            'appointment_date' => $this->appointment->appointment_date,
            'start_time'       => $this->appointment->start_time,
            'status'           => $this->appointment->status,
            'action'           => $this->action,
            'title'            => $this->title,
            'message'          => $this->message,
            'icon'             => $this->icon,
            'link'             => route('admin.appointments.index'),
        ];
    }
}
