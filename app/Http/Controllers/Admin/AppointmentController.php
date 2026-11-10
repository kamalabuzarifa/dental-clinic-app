<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments with both Table and 100% dynamic Calendar view.
     */
    public function index(Request $request)
    {
        $selectedDate = $request->query('date');
        $selectedDoctor = $request->query('doctor_id');
        $selectedStatus = $request->query('status');
        $currentTab = $request->query('tab', 'table');

        $doctors = User::where('role', 'dentist')->orderBy('name')->get();

        // 1. Paginated appointments for the Table list (with real relationships)
        $appointments = Appointment::with(['patient', 'dentist', 'service'])
            ->when($selectedDate, fn($q) => $q->whereDate('appointment_date', $selectedDate))
            ->when($selectedDoctor, fn($q) => $q->where('dentist_id', $selectedDoctor))
            ->when($selectedStatus, fn($q) => $q->where('status', $selectedStatus))
            ->latest('appointment_date')
            ->latest('start_time')
            ->paginate(15)
            ->withQueryString();

        // 2. Dynamic Calendar Period Calculation
        $now = now();
        $year = (int) $request->query('year', $selectedDate ? date('Y', strtotime($selectedDate)) : $now->year);
        $month = (int) $request->query('month', $selectedDate ? date('m', strtotime($selectedDate)) : $now->month);

        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }
        if ($year < 2000 || $year > 2100) {
            $year = $now->year;
        }

        $currentMonthDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $prevMonthDate = $currentMonthDate->copy()->subMonth();
        $nextMonthDate = $currentMonthDate->copy()->addMonth();

        $startOfMonth = $currentMonthDate->copy()->startOfMonth()->toDateString();
        $endOfMonth = $currentMonthDate->copy()->endOfMonth()->toDateString();

        // 3. Retrieve REAL appointments from database for the month (no mock/fake data)
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

        // 4. Build Calendar Days Matrix (Saturday to Friday in Arabic/RTL week)
        $dayOfWeek = $currentMonthDate->dayOfWeek; // Sunday = 0, Saturday = 6
        $blankDaysCount = ($dayOfWeek + 1) % 7; // Saturday = 0 blank days

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
                    'edit_url'         => route('admin.appointments.edit', $a),
                    'destroy_url'      => route('admin.appointments.destroy', $a),
                    'status_url'       => route('admin.appointments.status', $a),
                ])->toArray(),
            ];
        }

        // 5. Month Summary Counters (all strictly from real DB collection)
        $monthTotalAppointments = $monthAppointmentsCollection->count();
        $monthConfirmedCount = $monthAppointmentsCollection->where('status', 'confirmed')->count();
        $monthPendingCount = $monthAppointmentsCollection->where('status', 'pending')->count();
        $monthCompletedCount = $monthAppointmentsCollection->where('status', 'completed')->count();
        $monthCancelledCount = $monthAppointmentsCollection->where('status', 'cancelled')->count();

        $monthNameArabic = $currentMonthDate->locale('ar')->translatedFormat('F Y');

        return view('admin.appointments.index', compact(
            'appointments',
            'doctors',
            'selectedDate',
            'selectedDoctor',
            'selectedStatus',
            'currentTab',
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
     * Show the form for creating a new appointment.
     */
    public function create(Request $request)
    {
        $defaultDate = $request->query('date', date('Y-m-d'));
        $patients = User::where('role', 'patient')->orderBy('name')->get();
        $doctors = User::where('role', 'dentist')->orderBy('name')->get();
        $services = Service::orderBy('name')->get();

        return view('admin.appointments.create', compact('patients', 'doctors', 'services', 'defaultDate'));
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'       => ['required', 'exists:users,id'],
            'dentist_id'       => ['required', 'exists:users,id'],
            'service_id'       => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date'],
            'start_time'       => ['required', 'date_format:H:i'],
            'status'           => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ], [
            'patient_id.required'       => 'يرجى اختيار المريض.',
            'dentist_id.required'       => 'يرجى اختيار الطبيب المعالج.',
            'service_id.required'       => 'يرجى اختيار الخدمة الطبية.',
            'appointment_date.required' => 'يرجى تحديد تاريخ الموعد.',
            'start_time.required'       => 'يرجى تحديد وقت بدء الجلسة.',
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $startTime = strtotime($validated['start_time']);
        $endTime = date('H:i:s', $startTime + ($service->duration_minutes * 60));
        $validated['end_time'] = $endTime;

        // Check for conflicting appointment
        $exists = Appointment::where('dentist_id', $validated['dentist_id'])
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('start_time', $validated['start_time'])
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['start_time' => 'هذا الموعد محجوز مسبقاً لدى الطبيب المختار.']);
        }

        $newAppointment = Appointment::create($validated);
        \App\Services\NotificationService::notifyStaff($newAppointment, 'created');
        \App\Models\AuditLog::record('create', "إنشاء وتثبيت موعد طبي جديد للمريض [{$newAppointment->patient?->name}] مع الطبيب [{$newAppointment->dentist?->name}]", $newAppointment);

        return redirect()
            ->route('admin.appointments.index')
            ->with('success', 'تم تسجيل وتثبيت حجز الموعد بنجاح.');
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        $patients = User::where('role', 'patient')->orderBy('name')->get();
        $doctors = User::where('role', 'dentist')->orderBy('name')->get();
        $services = Service::orderBy('name')->get();

        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors', 'services'));
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, Appointment $appointment)
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

        $service = Service::findOrFail($validated['service_id']);
        $startTime = strtotime($validated['start_time']);
        $endTime = date('H:i:s', $startTime + ($service->duration_minutes * 60));
        $validated['end_time'] = $endTime;

        $oldValues = $appointment->only(['patient_id', 'dentist_id', 'service_id', 'appointment_date', 'start_time', 'end_time', 'status', 'notes']);
        $appointment->update($validated);
        \App\Models\AuditLog::record('update_appointment', "تحديث تفاصيل الموعد الطبي رقم [{$appointment->id}]", $appointment, $oldValues, $validated);

        return redirect()
            ->route('admin.appointments.index')
            ->with('info', 'تم تحديث بيانات الحجز بنجاح.');
    }

    /**
     * Update status specifically.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
        ]);

        $oldStatus = $appointment->status;
        $appointment->update(['status' => $validated['status']]);

        \App\Services\NotificationService::notifyStaff($appointment, 'status_updated');
        \App\Models\AuditLog::record('status_change', "تحديث حالة الموعد الطبي رقم [{$appointment->id}] من [{$oldStatus}] إلى [{$validated['status']}]", $appointment, ['status' => $oldStatus], ['status' => $validated['status']]);

        return back()->with('info', 'تم تحديث حالة الموعد بنجاح.');
    }

    /**
     * Cancel the appointment.
     */
    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);

        \App\Services\NotificationService::notifyStaff($appointment, 'cancelled');
        \App\Models\AuditLog::record('cancel', "إلغاء الموعد الطبي رقم [{$appointment->id}]", $appointment);

        return back()->with('error', 'تم إلغاء الموعد.');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        \App\Models\AuditLog::record('delete', "حذف سجل الموعد الطبي رقم [{$appointment->id}] للمريض [{$appointment->patient?->name}]", $appointment);
        $appointment->delete();

        return redirect()
            ->route('admin.appointments.index')
            ->with('error', 'تم حذف سجل الحجز بنجاح.');
    }
}
