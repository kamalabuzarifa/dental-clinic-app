<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReceptionistAppointmentController extends Controller
{
    /**
     * Display a listing of appointments with search, filters (date, doctor, status), and pagination.
     */
    public function index(Request $request): View
    {
        $selectedDate = $request->query('date');
        $selectedDoctor = $request->query('doctor_id');
        $selectedStatus = $request->query('status');
        $search = $request->query('search');

        $doctors = User::where('role', 'dentist')->orderBy('name')->get();

        $appointments = Appointment::with(['patient', 'dentist', 'service'])
            ->when($selectedDate, fn($q) => $q->whereDate('appointment_date', $selectedDate))
            ->when($selectedDoctor, fn($q) => $q->where('dentist_id', $selectedDoctor))
            ->when($selectedStatus, fn($q) => $q->where('status', $selectedStatus))
            ->when($search, function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest('appointment_date')
            ->latest('start_time')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'     => Appointment::count(),
            'today'     => Appointment::whereDate('appointment_date', now()->toDateString())->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'pending'   => Appointment::where('status', 'pending')->count(),
        ];

        return view('receptionist.appointments.index', compact(
            'appointments',
            'doctors',
            'selectedDate',
            'selectedDoctor',
            'selectedStatus',
            'search',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create(Request $request): View
    {
        $defaultDate = $request->query('date', date('Y-m-d'));
        $selectedPatientId = $request->query('patient_id');
        $selectedDentistId = $request->query('dentist_id');

        $patients = User::where('role', 'patient')->orderBy('name')->get();
        $doctors = User::where('role', 'dentist')->orderBy('name')->get();
        $services = Service::orderBy('name')->get();

        // Clinic working hours for reference
        $workingHoursRaw = Setting::get('working_hours');
        $workingHours = is_array($workingHoursRaw) ? $workingHoursRaw : json_decode($workingHoursRaw, true);

        return view('receptionist.appointments.create', compact(
            'patients',
            'doctors',
            'services',
            'defaultDate',
            'selectedPatientId',
            'selectedDentistId',
            'workingHours'
        ));
    }

    /**
     * Store a newly created appointment with atomic DB transactions, concurrency locks,
     * working hours validation, and duplicate submission prevention.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id'       => ['required', 'exists:users,id'],
            'dentist_id'       => ['required', 'exists:users,id'],
            'service_id'       => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time'       => ['required', 'date_format:H:i'],
            'status'           => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ], [
            'patient_id.required'             => 'يرجى اختيار المريض.',
            'patient_id.exists'               => 'المريض المختار غير موجود بالمركز.',
            'dentist_id.required'             => 'يرجى اختيار الطبيب المعالج.',
            'dentist_id.exists'               => 'الطبيب المختار غير مسجل بالنظام.',
            'service_id.required'             => 'يرجى اختيار الخدمة الطبية.',
            'appointment_date.required'       => 'يرجى تحديد تاريخ الموعد.',
            'appointment_date.after_or_equal' => 'تاريخ الموعد لا يمكن أن يكون في الماضي.',
            'start_time.required'             => 'يرجى تحديد وقت بدء الجلسة.',
            'status.required'                 => 'يرجى تحديد حالة الحجز.',
        ]);

        $dentist = User::where('id', $validated['dentist_id'])->where('role', 'dentist')->firstOrFail();
        $patient = User::where('id', $validated['patient_id'])->where('role', 'patient')->firstOrFail();
        $service = Service::findOrFail($validated['service_id']);

        $durationMinutes = $service->duration_minutes ?: 30;
        $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
        $endTime = (clone $startTime)->addMinutes($durationMinutes);

        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');
        $appointmentDate = $validated['appointment_date'];

        // 1. Working Hours & Clinic Off-Days Validation
        $workingHoursError = $this->validateWorkingHoursAndOffDays($appointmentDate, $startTime, $endTime);
        if ($workingHoursError) {
            return back()->withInput()->withErrors($workingHoursError);
        }

        // 2. Prevent Double Form Submission (Two-tier Idempotency: Atomic Cache::add + 15-second Duplicate Check)
        $idempotencyKey = $request->input('idempotency_key');
        if (!empty($idempotencyKey)) {
            // Atomic check-and-set to prevent concurrent duplicate submissions with the same key
            $acquired = Cache::add('idempotency_apt_' . $idempotencyKey, 'processing', 120);
            if (!$acquired) {
                return redirect()
                    ->route('receptionist.appointments.index')
                    ->with('info', "تم استلام الطلب وتأكيد الحجز مسبقاً بنجاح.");
            }
        }

        $isMysql = in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb']);

        $recentDuplicate = Appointment::where('patient_id', $patient->id)
            ->where('dentist_id', $dentist->id)
            ->when($isMysql, fn($q) => $q->where('appointment_date', $appointmentDate), fn($q) => $q->whereDate('appointment_date', $appointmentDate))
            ->where('start_time', $startTimeStr)
            ->where('created_at', '>=', now()->subSeconds(15))
            ->first();

        if ($recentDuplicate) {
            return redirect()
                ->route('receptionist.appointments.index')
                ->with('info', "تم تسجيل هذا الحجز مسبقاً بنجاح.");
        }

        // 3. Atomic Database Transaction with Row Locking (Prevents Race Conditions / Double-Booking)
        try {
            $appointment = DB::transaction(function () use ($validated, $dentist, $patient, $appointmentDate, $startTimeStr, $endTimeStr, $startTime, $endTime, $isMysql) {
                // Check Doctor Conflict with pessimistic lock using composite index (dentist_id, appointment_date, start_time)
                $dentistConflict = Appointment::where('dentist_id', $dentist->id)
                    ->when($isMysql, fn($q) => $q->where('appointment_date', $appointmentDate), fn($q) => $q->whereDate('appointment_date', $appointmentDate))
                    ->whereNotIn('status', ['cancelled'])
                    ->where(function ($query) use ($startTimeStr, $endTimeStr) {
                        $query->where('start_time', '<', $endTimeStr)
                              ->where('end_time', '>', $startTimeStr);
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($dentistConflict) {
                    throw ValidationException::withMessages([
                        'start_time' => "تعارض مواعيد: الطبيب {$dentist->name} لديه حجز آخر يتداخل مع التوقيت ({$startTime->format('h:i A')} - {$endTime->format('h:i A')}).",
                    ]);
                }

                // Check Patient Conflict with pessimistic lock
                $patientConflict = Appointment::where('patient_id', $patient->id)
                    ->when($isMysql, fn($q) => $q->where('appointment_date', $appointmentDate), fn($q) => $q->whereDate('appointment_date', $appointmentDate))
                    ->whereNotIn('status', ['cancelled'])
                    ->where(function ($query) use ($startTimeStr, $endTimeStr) {
                        $query->where('start_time', '<', $endTimeStr)
                              ->where('end_time', '>', $startTimeStr);
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($patientConflict) {
                    throw ValidationException::withMessages([
                        'start_time' => "تعارض مواعيد: المريض {$patient->name} لديه موعد آخر في نفس التاريخ والتوقيت.",
                    ]);
                }

                $validated['start_time'] = $startTimeStr;
                $validated['end_time'] = $endTimeStr;

                return Appointment::create($validated);
            });
        } catch (ValidationException $e) {
            if (!empty($idempotencyKey)) {
                Cache::forget('idempotency_apt_' . $idempotencyKey);
            }
            return back()->withInput()->withErrors($e->errors());
        }

        if (!empty($idempotencyKey)) {
            Cache::put('idempotency_apt_' . $idempotencyKey, $appointment->id, 120);
        }

        // Notify Staff and Patients
        NotificationService::notifyStaff($appointment, 'created');

        // Audit Log
        AuditLog::record(
            'create_appointment',
            "تسجيل حجز موعد جديد للمريض [{$patient->name}] مع الطبيب [{$dentist->name}] في [{$appointmentDate}]",
            $appointment
        );

        return redirect()
            ->route('receptionist.appointments.index')
            ->with('success', "تم تسجيل وتأكيد حجز الموعد بنجاح للمريض [{$patient->name}].");
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment): View
    {
        $patients = User::where('role', 'patient')->orderBy('name')->get();
        $doctors = User::where('role', 'dentist')->orderBy('name')->get();
        $services = Service::orderBy('name')->get();

        return view('receptionist.appointments.edit', compact('appointment', 'patients', 'doctors', 'services'));
    }

    /**
     * Update the specified appointment in storage with full validation, state machine integrity,
     * and conflict checks.
     */
    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id'       => ['required', 'exists:users,id'],
            'dentist_id'       => ['required', 'exists:users,id'],
            'service_id'       => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date'],
            'start_time'       => ['required'],
            'status'           => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ]);

        // State Machine Integrity Validation
        $transitionError = $this->validateStatusTransition($appointment->status, $validated['status']);
        if ($transitionError) {
            return back()->withInput()->with('error', $transitionError);
        }

        $dentist = User::where('id', $validated['dentist_id'])->where('role', 'dentist')->firstOrFail();
        $patient = User::where('id', $validated['patient_id'])->where('role', 'patient')->firstOrFail();
        $service = Service::findOrFail($validated['service_id']);

        $durationMinutes = $service->duration_minutes ?: 30;
        $startTime = Carbon::createFromFormat('H:i', substr($validated['start_time'], 0, 5));
        $endTime = (clone $startTime)->addMinutes($durationMinutes);

        $startTimeStr = $startTime->format('H:i:s');
        $endTimeStr = $endTime->format('H:i:s');
        $appointmentDate = $validated['appointment_date'];

        // Working Hours & Off-Days Validation
        $workingHoursError = $this->validateWorkingHoursAndOffDays($appointmentDate, $startTime, $endTime);
        if ($workingHoursError) {
            return back()->withInput()->withErrors($workingHoursError);
        }

        $isMysql = in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb']);

        // Concurrency Transaction with conflict check
        try {
            DB::transaction(function () use ($appointment, $validated, $dentist, $patient, $appointmentDate, $startTimeStr, $endTimeStr, $isMysql) {
                // Conflict check for dentist excluding current appointment
                $dentistConflict = Appointment::where('dentist_id', $dentist->id)
                    ->where('id', '!=', $appointment->id)
                    ->when($isMysql, fn($q) => $q->where('appointment_date', $appointmentDate), fn($q) => $q->whereDate('appointment_date', $appointmentDate))
                    ->whereNotIn('status', ['cancelled'])
                    ->where(function ($query) use ($startTimeStr, $endTimeStr) {
                        $query->where('start_time', '<', $endTimeStr)
                              ->where('end_time', '>', $startTimeStr);
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($dentistConflict) {
                    throw ValidationException::withMessages([
                        'start_time' => "تعارض مواعيد: الطبيب لديه حجز آخر يتداخل مع هذا التوقيت المعدل.",
                    ]);
                }

                // Conflict check for patient excluding current appointment
                $patientConflict = Appointment::where('patient_id', $patient->id)
                    ->where('id', '!=', $appointment->id)
                    ->when($isMysql, fn($q) => $q->where('appointment_date', $appointmentDate), fn($q) => $q->whereDate('appointment_date', $appointmentDate))
                    ->whereNotIn('status', ['cancelled'])
                    ->where(function ($query) use ($startTimeStr, $endTimeStr) {
                        $query->where('start_time', '<', $endTimeStr)
                              ->where('end_time', '>', $startTimeStr);
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($patientConflict) {
                    throw ValidationException::withMessages([
                        'start_time' => "تعارض مواعيد: المريض لديه موعد آخر في نفس التاريخ والتوقيت.",
                    ]);
                }

                $validated['start_time'] = $startTimeStr;
                $validated['end_time'] = $endTimeStr;
                $oldValues = $appointment->only(['patient_id', 'dentist_id', 'service_id', 'appointment_date', 'start_time', 'end_time', 'status', 'notes']);
                $appointment->update($validated);

                AuditLog::record(
                    'update_appointment',
                    "تحديث تفاصيل الموعد الطبي رقم [{$appointment->id}] بواسطة موظف الاستقبال",
                    $appointment,
                    $oldValues,
                    $validated
                );
            });
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        NotificationService::notifyStaff($appointment, 'status_updated');

        return redirect()
            ->route('receptionist.appointments.index')
            ->with('info', 'تم تحديث بيانات الحجز بنجاح.');
    }

    /**
     * Update the status of the specified appointment with state machine rules.
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
        ]);

        $transitionError = $this->validateStatusTransition($appointment->status, $validated['status']);
        if ($transitionError) {
            return back()->with('error', $transitionError);
        }

        $oldStatus = $appointment->status;
        $appointment->update(['status' => $validated['status']]);

        NotificationService::notifyStaff($appointment, 'status_updated');

        AuditLog::record(
            'status_change',
            "تغيير حالة الموعد رقم [{$appointment->id}] للمريض [{$appointment->patient?->name}] من [{$oldStatus}] إلى [{$validated['status']}]",
            $appointment,
            ['status' => $oldStatus],
            ['status' => $validated['status']]
        );

        return back()->with('info', 'تم تحديث حالة الموعد الطبي بنجاح.');
    }

    /**
     * Cancel the specified appointment.
     */
    public function cancel(Appointment $appointment): RedirectResponse
    {
        if ($appointment->status === 'completed') {
            return back()->with('error', 'لا يمكن إلغاء موعد مكتمل بالفعل تم تنفيذه في العيادة.');
        }

        if ($appointment->status === 'cancelled') {
            return back()->with('info', 'هذا الموعد ملغي بالفعل مسبقاً.');
        }

        $appointment->update(['status' => 'cancelled']);

        NotificationService::notifyStaff($appointment, 'cancelled');

        AuditLog::record(
            'cancel',
            "إلغاء الموعد الطبي رقم [{$appointment->id}] للمريض [{$appointment->patient?->name}] بواسطة موظف الاستقبال",
            $appointment
        );

        return back()->with('error', 'تم إلغاء الموعد الطبي.');
    }

    /**
     * Validate business status transitions to protect audit and operational flow.
     */
    protected function validateStatusTransition(string $currentStatus, string $newStatus): ?string
    {
        if ($currentStatus === $newStatus) {
            return null;
        }

        if ($currentStatus === 'completed') {
            return 'لا يمكن تعديل حالة موعد مكتمل بالفعل حفاظاً على سلامة وتوثيق السجلات الطبية والمالية.';
        }

        $allowedTransitions = [
            'pending'   => ['confirmed', 'cancelled', 'no_show'],
            'confirmed' => ['completed', 'cancelled', 'no_show', 'pending'],
            'cancelled' => ['pending', 'confirmed'],
            'no_show'   => ['pending', 'confirmed', 'cancelled'],
        ];

        $allowed = $allowedTransitions[$currentStatus] ?? [];
        if (!in_array($newStatus, $allowed, true)) {
            $statusLabels = [
                'pending'   => 'قيد الانتظار',
                'confirmed' => 'مؤكد',
                'completed' => 'مكتمل',
                'cancelled' => 'ملغي',
                'no_show'   => 'لم يحضر',
            ];
            $currLabel = $statusLabels[$currentStatus] ?? $currentStatus;
            $newLabel = $statusLabels[$newStatus] ?? $newStatus;
            return "لا يمكن تحويل حالة الموعد من [{$currLabel}] إلى [{$newLabel}] مباشرة.";
        }

        return null;
    }

    /**
     * Validate appointment timing against clinic working hours and scheduled off-days.
     */
    protected function validateWorkingHoursAndOffDays(string $appointmentDate, Carbon $startTime, Carbon $endTime): ?array
    {
        $workingHoursRaw = Setting::get('working_hours');
        if (!$workingHoursRaw) {
            return null;
        }

        $workingHours = is_array($workingHoursRaw) ? $workingHoursRaw : json_decode($workingHoursRaw, true);
        if (!is_array($workingHours)) {
            return null;
        }

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
                return [
                    'appointment_date' => "تنبيه: المركز مغلق في يوم {$daySchedule['name']} (عطلة رسمية مجدولة). يرجى اختيار يوم عمل متاح.",
                ];
            }

            $workStart = $daySchedule['start'] ?? '09:00';
            $workEnd = $daySchedule['end'] ?? '21:00';
            $bookingStart = $startTime->format('H:i');
            $bookingEnd = $endTime->format('H:i');

            if ($bookingStart < $workStart || $bookingEnd > $workEnd) {
                return [
                    'start_time' => "تنبيه: وقت الحجز خارج ساعات دوام المركز ليوم {$daySchedule['name']} (من {$workStart} إلى {$workEnd}).",
                ];
            }
        }

        return null;
    }

    /**
     * Display a 100% dynamic monthly calendar view for doctors' schedules.
     */
    public function calendar(Request $request): View
    {
        $selectedDate = $request->query('date');
        $selectedDoctor = $request->query('doctor_id');
        $selectedStatus = $request->query('status');

        $doctors = User::where('role', 'dentist')->orderBy('name')->get();

        $now = now();
        $year = (int) $request->query('year', $selectedDate ? date('Y', strtotime($selectedDate)) : $now->year);
        $month = (int) $request->query('month', $selectedDate ? date('m', strtotime($selectedDate)) : $now->month);

        if ($month < 1 || $month > 12) $month = $now->month;
        if ($year < 2000 || $year > 2100) $year = $now->year;

        $currentMonthDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $prevMonthDate = $currentMonthDate->copy()->subMonth();
        $nextMonthDate = $currentMonthDate->copy()->addMonth();

        $startOfMonth = $currentMonthDate->copy()->startOfMonth()->toDateString();
        $endOfMonth = $currentMonthDate->copy()->endOfMonth()->toDateString();

        $monthAppointmentsCollection = Appointment::with([
                'patient:id,name,phone,email',
                'dentist:id,name',
                'service:id,name,price,duration_minutes',
            ])
            ->whereBetween('appointment_date', [$startOfMonth, $endOfMonth])
            ->when($selectedDoctor, fn($q) => $q->where('dentist_id', $selectedDoctor))
            ->when($selectedStatus, fn($q) => $q->where('status', $selectedStatus))
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get();

        $calendarGrouped = $monthAppointmentsCollection->groupBy(function ($item) {
            return $item->appointment_date instanceof \DateTimeInterface
                ? $item->appointment_date->format('Y-m-d')
                : substr((string) $item->appointment_date, 0, 10);
        });

        // Saturday to Friday RTL calendar grid
        $dayOfWeek = $currentMonthDate->dayOfWeek;
        $blankDaysCount = ($dayOfWeek + 1) % 7;

        $daysInMonth = $currentMonthDate->daysInMonth;
        $calendarDays = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $dayList = $calendarGrouped->get($dateStr, collect());

            $calendarDays[] = [
                'dayNumber'    => $d,
                'date'         => $dateStr,
                'isToday'      => ($dateStr === $now->toDateString()),
                'isSelected'   => ($selectedDate === $dateStr),
                'total'        => $dayList->count(),
                'confirmed'    => $dayList->where('status', 'confirmed')->count(),
                'pending'      => $dayList->where('status', 'pending')->count(),
                'completed'    => $dayList->where('status', 'completed')->count(),
                'cancelled'    => $dayList->where('status', 'cancelled')->count(),
                'appointments' => $dayList->values()->map(fn($a) => [
                    'id'               => $a->id,
                    'patient_name'     => $a->patient?->name ?? 'غير محدد',
                    'patient_phone'    => $a->patient?->phone ?? '',
                    'dentist_name'     => $a->dentist?->name ?? 'غير محدد',
                    'service_name'     => $a->service?->name ?? 'غير محدد',
                    'service_price'    => (float) ($a->service?->price ?? 0),
                    'duration_minutes' => (int) ($a->service?->duration_minutes ?? 30),
                    'start_time'       => date('h:i A', strtotime($a->start_time)),
                    'end_time'         => date('h:i A', strtotime($a->end_time)),
                    'status'           => $a->status,
                    'notes'            => $a->notes,
                    'edit_url'         => route('receptionist.appointments.edit', $a),
                    'status_url'       => route('receptionist.appointments.status', $a),
                    'cancel_url'       => route('receptionist.appointments.cancel', $a),
                ])->toArray(),
            ];
        }

        $monthTotalAppointments = $monthAppointmentsCollection->count();
        $monthConfirmedCount = $monthAppointmentsCollection->where('status', 'confirmed')->count();
        $monthPendingCount = $monthAppointmentsCollection->where('status', 'pending')->count();
        $monthCompletedCount = $monthAppointmentsCollection->where('status', 'completed')->count();
        $monthCancelledCount = $monthAppointmentsCollection->where('status', 'cancelled')->count();

        $monthNameArabic = $currentMonthDate->locale('ar')->translatedFormat('F Y');

        return view('receptionist.appointments.calendar', compact(
            'doctors',
            'selectedDate',
            'selectedDoctor',
            'selectedStatus',
            'year',
            'month',
            'currentMonthDate',
            'prevMonthDate',
            'nextMonthDate',
            'monthNameArabic',
            'blankDaysCount',
            'calendarDays',
            'monthTotalAppointments',
            'monthConfirmedCount',
            'monthPendingCount',
            'monthCompletedCount',
            'monthCancelledCount'
        ));
    }

    /**
     * Remove the specified appointment from storage.
     * Blocked for receptionist to guarantee audit integrity.
     */
    public function destroy(Appointment $appointment)
    {
        abort(403, 'حذف المواعيد محظور لموظف الاستقبال، هذه العملية مخصصة لمدير النظام حصراً.');
    }
}
