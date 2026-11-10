<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Display the appointment creation form for patients.
     */
    public function create(): View|RedirectResponse
    {
        // Enforce maintenance mode setting
        if (\App\Models\Setting::get('maintenance_mode', '0') === '1' && !in_array(Auth::user()?->role, ['admin', 'receptionist'])) {
            return redirect()->route('patient.dashboard')->with('error', 'نظام حجز المواعيد معلق حالياً لأعمال الصيانة المجدولة في المركز.');
        }

        $dentists = User::where('role', 'dentist')->orderBy('name')->get();
        $services = Service::orderBy('name')->get();

        return view('patient.appointments.create', compact('dentists', 'services'));
    }

    /**
     * Store a newly created appointment with strong conflict prevention.
     */
    public function store(Request $request): RedirectResponse
    {
        // Enforce maintenance mode setting
        if (\App\Models\Setting::get('maintenance_mode', '0') === '1' && !in_array(Auth::user()?->role, ['admin', 'receptionist'])) {
            return redirect()->route('patient.dashboard')->with('error', 'نظام حجز المواعيد معلق حالياً لأعمال الصيانة.');
        }

        $validated = $request->validate([
            'dentist_id' => ['required', 'exists:users,id'],
            'service_id' => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'dentist_id.required' => 'يرجى اختيار طبيب الأسنان.',
            'dentist_id.exists' => 'الطبيب المختار غير موجود في النظام.',
            'service_id.required' => 'يرجى اختيار نوع الخدمة الطبية.',
            'service_id.exists' => 'الخدمة المختارة غير صحيحة.',
            'appointment_date.required' => 'يرجى تحديد تاريخ الموعد.',
            'appointment_date.after_or_equal' => 'تاريخ الموعد لا يمكن أن يكون في الماضي.',
            'start_time.required' => 'يرجى تحديد وقت بدء الموعد.',
            'start_time.date_format' => 'صيغة الوقت غير صحيحة.',
        ]);

        // Verify the selected user is indeed a dentist
        $dentist = User::where('id', $validated['dentist_id'])
            ->where('role', 'dentist')
            ->firstOrFail();

        // Retrieve service details to compute duration
        $service = Service::findOrFail($validated['service_id']);
        $durationMinutes = $service->duration_minutes ?: 30;

        // Compute start and end times
        $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
        $endTime = (clone $startTime)->addMinutes($durationMinutes);

        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');
        $appointmentDate = $validated['appointment_date'];

        // Determine patient ID (if receptionist is creating for another user or patient self-booking)
        $patientId = Auth::user()->role === 'patient' 
            ? Auth::id() 
            : ($request->input('patient_id') ?: Auth::id());

        // Enforce working hours and clinic off-days for patients
        $workingHoursRaw = \App\Models\Setting::get('working_hours');
        if ($workingHoursRaw && Auth::user()->role === 'patient') {
            $workingHours = is_array($workingHoursRaw) ? $workingHoursRaw : json_decode($workingHoursRaw, true);
            if (is_array($workingHours)) {
                $carbonDayMap = [
                    Carbon::SATURDAY  => 0,
                    Carbon::SUNDAY    => 1,
                    Carbon::MONDAY    => 2,
                    Carbon::TUESDAY   => 3,
                    Carbon::WEDNESDAY => 4,
                    Carbon::THURSDAY  => 5,
                    Carbon::FRIDAY    => 6,
                ];
                $dayOfWeek = Carbon::parse($appointmentDate)->dayOfWeek;
                $dayIndex = $carbonDayMap[$dayOfWeek] ?? null;

                if ($dayIndex !== null && isset($workingHours[$dayIndex])) {
                    $daySchedule = $workingHours[$dayIndex];
                    if (!empty($daySchedule['is_off'])) {
                        return back()
                            ->withInput()
                            ->withErrors([
                                'appointment_date' => "عذراً، المركز مغلق في يوم {$daySchedule['name']} (عطلة رسمية). يرجى اختيار يوم عمل متاح.",
                            ]);
                    }

                    $workStart = $daySchedule['start'] ?? '09:00';
                    $workEnd = $daySchedule['end'] ?? '21:00';
                    $bookingStart = $startTime->format('H:i');
                    $bookingEnd = $endTime->format('H:i');

                    if ($bookingStart < $workStart || $bookingEnd > $workEnd) {
                        return back()
                            ->withInput()
                            ->withErrors([
                                'start_time' => "عذراً، توقيت الحجز المختار خارج ساعات العمل الرسمية ليوم {$daySchedule['name']} (ساعات العمل من {$workStart} إلى {$workEnd}).",
                            ]);
                    }
                }
            }
        }

        /*
         |--------------------------------------------------------------------------
         | Double-Booking Conflict Prevention Algorithm
         |--------------------------------------------------------------------------
         | Two intervals [A, B] and [C, D] overlap if and only if:
         | (existing.start_time < new.end_time) AND (existing.end_time > new.start_time)
         | We ignore appointments with status 'cancelled'.
         */
        $dentistConflict = Appointment::where('dentist_id', $dentist->id)
            ->where('appointment_date', $appointmentDate)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($query) use ($startTimeStr, $endTimeStr) {
                $query->where('start_time', '<', $endTimeStr)
                      ->where('end_time', '>', $startTimeStr);
            })
            ->exists();

        if ($dentistConflict) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' => "عذراً، الطبيب {$dentist->name} لديه موعد محجوز مسبقاً يتعارض مع هذا التوقيت ({$startTime->format('h:i A')} - {$endTime->format('h:i A')}). يرجى اختيار موعد أو وقت آخر.",
                ]);
        }

        // Check if the patient already has another appointment on the same date and overlapping time
        $patientConflict = Appointment::where('patient_id', $patientId)
            ->where('appointment_date', $appointmentDate)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($query) use ($startTimeStr, $endTimeStr) {
                $query->where('start_time', '<', $endTimeStr)
                      ->where('end_time', '>', $startTimeStr);
            })
            ->exists();

        if ($patientConflict) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' => 'لديك بالفعل موعد آخر مسجل في نفس التوقيت والتاريخ. يرجى مراجعة مواعيدك.',
                ]);
        }

        // Enforce auto_confirm_bookings setting (if disabled, set to pending)
        $autoConfirm = \App\Models\Setting::get('auto_confirm_bookings', '1') === '1';
        $initialStatus = $autoConfirm ? 'confirmed' : 'pending';

        // Save appointment
        $appointment = Appointment::create([
            'patient_id' => $patientId,
            'dentist_id' => $dentist->id,
            'service_id' => $service->id,
            'appointment_date' => $appointmentDate,
            'start_time' => $startTimeStr,
            'end_time' => $endTimeStr,
            'status' => $initialStatus,
            'notes' => $validated['notes'] ?? null,
        ]);

        \App\Services\NotificationService::notifyStaff($appointment, 'created');
        \App\Models\AuditLog::record('booking', "حجز موعد طبي جديد للمريض [{$appointment->patient?->name}] مع الطبيب [{$dentist->name}]", $appointment);

        $redirectRoute = match (Auth::user()->role) {
            'receptionist' => route('receptionist.dashboard'),
            'admin' => route('admin.dashboard'),
            default => route('patient.dashboard'),
        };

        $msg = $initialStatus === 'confirmed'
            ? 'تم حجز وتأكيد موعدك بنجاح! يسعدنا حضورك في الموعد المحدد.'
            : 'تم استلام طلب حجز موعدك بنجاح، وهو قيد المراجعة والتأكيد من قِبل إدارة المركز.';

        return redirect($redirectRoute)->with('success', $msg);
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Appointment $appointment): RedirectResponse
    {
        $user = Auth::user();

        // Patients can only cancel their own appointments
        if ($user->role === 'patient') {
            if ($appointment->patient_id !== $user->id) {
                abort(403, 'غير مصرح لك بإلغاء هذا الحجز.');
            }

            // Enforce allow_patient_cancel setting
            if (\App\Models\Setting::get('allow_patient_cancel', '1') === '0') {
                return back()->with('error', 'إلغاء المواعيد ذاتياً من قِبل المريض غير متاح حالياً وفق سياسة العيادة. يرجى التواصل هاتفياً مع إدارة المركز.');
            }
        }

        if ($appointment->status === 'completed') {
            return back()->with('error', 'لا يمكن إلغاء موعد تم اكتماله بالفعل.');
        }

        $appointment->update(['status' => 'cancelled']);

        \App\Services\NotificationService::notifyStaff($appointment, 'cancelled');
        \App\Models\AuditLog::record('cancel_booking', "إلغاء الموعد الطبي رقم [{$appointment->id}]", $appointment);

        return back()->with('success', 'تم إلغاء الحجز بنجاح.');
    }

    /**
     * Update appointment status (for Dentist, Receptionist, Admin).
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = Auth::user();

        // Dentist can only update their own appointments
        if ($user->role === 'dentist' && $appointment->dentist_id !== $user->id) {
            abort(403, 'غير مصرح لك بتعديل موعد لطبيب آخر.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
        ]);

        $appointment->update([
            'status' => $validated['status'],
        ]);

        $statusLabels = [
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'no_show' => 'لم يحضر المريض',
            'pending' => 'قيد الانتظار',
        ];

        $label = $statusLabels[$validated['status']] ?? $validated['status'];

        return back()->with('success', "تم تحديث حالة الموعد إلى: ({$label}) بنجاح.");
    }
}
