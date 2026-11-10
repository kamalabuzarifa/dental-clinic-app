<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard with real-time clinic statistics and management overviews.
     * Uses Eager Loading to prevent N+1 queries and includes graceful error handling.
     */
    public function index(Request $request): View
    {
        $today = now()->toDateString();
        $sevenDaysAgo = now()->subDays(6)->toDateString();

        try {
            // ---- Stats Cards (all from real DB) ----
            $stats = [
                'total_patients'          => User::where('role', 'patient')->count(),
                'total_dentists'          => User::where('role', 'dentist')->count(),
                'today_appointments'      => Appointment::whereDate('appointment_date', $today)->count(),
                'pending_appointments'    => Appointment::where('status', 'pending')->count(),
                'confirmed_appointments'  => Appointment::where('status', 'confirmed')->count(),
                'completed_appointments'  => Appointment::where('status', 'completed')->count(),
                'cancelled_appointments'  => Appointment::where('status', 'cancelled')->count(),
                'total_appointments'      => Appointment::count(),
                'total_services'          => Service::count(),
            ];

            // ---- Today's Schedule ----
            $todayAppointments = Appointment::with([
                    'patient:id,name,phone',
                    'dentist:id,name',
                    'service:id,name,duration_minutes',
                ])
                ->whereDate('appointment_date', $today)
                ->orderBy('start_time')
                ->get();

            // ---- Pending appointments needing confirmation (upcoming only) ----
            $pendingAlerts = Appointment::with([
                    'patient:id,name',
                    'dentist:id,name',
                    'service:id,name',
                ])
                ->where('status', 'pending')
                ->where('appointment_date', '>=', $today)
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->take(5)
                ->get();

            // ---- Recent Appointments (last 10, ordered by creation) ----
            $recentAppointments = Appointment::with([
                    'patient:id,name,phone',
                    'dentist:id,name',
                    'service:id,name,duration_minutes',
                ])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            // ---- Last 7 Days Daily Counts for mini chart ----
            $dailyStats = Appointment::whereBetween('appointment_date', [$sevenDaysAgo, $today])
                ->selectRaw('appointment_date, COUNT(*) as total,
                             SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_count,
                             SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_count,
                             SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count')
                ->groupBy('appointment_date')
                ->orderBy('appointment_date')
                ->get()
                ->keyBy(function ($item) {
                    return $item->appointment_date instanceof \DateTimeInterface
                        ? $item->appointment_date->format('Y-m-d')
                        : substr((string) $item->appointment_date, 0, 10);
                });

            // Build a complete 7-day array (fill in zero-data days)
            $weeklyData = [];
            for ($i = 6; $i >= 0; $i--) {
                $carbonDate = now()->subDays($i);
                $date = $carbonDate->toDateString();
                $dayLabel = $carbonDate->locale('ar')->isoFormat('ddd D/M');
                $row = $dailyStats->get($date);
                $weeklyData[] = [
                    'date'      => $date,
                    'label'     => $dayLabel,
                    'total'     => $row ? (int) $row->total : 0,
                    'completed' => $row ? (int) $row->completed_count : 0,
                    'cancelled' => $row ? (int) $row->cancelled_count : 0,
                    'pending'   => $row ? (int) $row->pending_count : 0,
                    'is_today'  => $date === $today,
                ];
            }

            // ---- Top Services by appointment count ----
            $topServices = Service::withCount('appointments')
                ->orderByDesc('appointments_count')
                ->take(5)
                ->get();

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Admin dashboard query failure: ' . $e->getMessage());

            $stats = [
                'total_patients'          => 0,
                'total_dentists'          => 0,
                'today_appointments'      => 0,
                'pending_appointments'    => 0,
                'confirmed_appointments'  => 0,
                'completed_appointments'  => 0,
                'cancelled_appointments'  => 0,
                'total_appointments'      => 0,
                'total_services'          => 0,
            ];
            $todayAppointments = collect();
            $pendingAlerts = collect();
            $recentAppointments = collect();
            $weeklyData = [];
            for ($i = 6; $i >= 0; $i--) {
                $carbonDate = now()->subDays($i);
                $weeklyData[] = [
                    'date'      => $carbonDate->toDateString(),
                    'label'     => $carbonDate->locale('ar')->isoFormat('ddd D/M'),
                    'total'     => 0,
                    'completed' => 0,
                    'cancelled' => 0,
                    'pending'   => 0,
                    'is_today'  => $carbonDate->toDateString() === $today,
                ];
            }
            $topServices = collect();
        }

        return view('admin.dashboard', compact(
            'stats',
            'todayAppointments',
            'pendingAlerts',
            'recentAppointments',
            'weeklyData',
            'topServices'
        ));
    }

    /**
     * Doctors Management.
     */
    public function doctors(): View
    {
        $doctors = User::where('role', 'dentist')
            ->withCount('dentistAppointments')
            ->latest()
            ->get();

        return view('admin.doctors.index', compact('doctors'));
    }

    /**
     * Patients Management.
     */
    public function patients(Request $request): View
    {
        $search = $request->input('search');

        $patients = User::where('role', 'patient')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->withCount('patientAppointments')
            ->with(['patientAppointments' => function ($q) {
                $q->latest('appointment_date')->limit(1);
            }])
            ->latest()
            ->paginate(15);

        return view('admin.patients.index', compact('patients', 'search'));
    }

    /**
     * Appointments Management.
     */
    public function appointments(Request $request): View
    {
        $selectedDate = $request->input('date');
        $selectedDoctor = $request->input('doctor_id');
        $selectedStatus = $request->input('status');

        $query = Appointment::with(['patient', 'dentist', 'service']);

        if ($selectedDate) {
            $query->whereDate('appointment_date', $selectedDate);
        }
        if ($selectedDoctor) {
            $query->where('dentist_id', $selectedDoctor);
        }
        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
                              ->orderBy('start_time', 'asc')
                              ->paginate(20);

        $doctors = User::where('role', 'dentist')->get();
        $services = Service::all();

        return view('admin.appointments.index', compact('appointments', 'doctors', 'services', 'selectedDate', 'selectedDoctor', 'selectedStatus'));
    }

    /**
     * Services Management.
     */
    public function services(): View
    {
        $services = Service::withCount('appointments')->latest()->get();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Reports and Analytics.
     */
    public function reports(): View
    {
        $totalPatients = User::where('role', 'patient')->count();
        $totalAppointments = Appointment::count();
        $completedAppointments = Appointment::where('status', 'completed')->count();
        
        // Estimated revenue calculation
        $estimatedRevenue = Appointment::where('status', 'completed')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->sum('services.price');

        $topServices = Service::withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->take(5)
            ->get();

        $doctorsPerformance = User::where('role', 'dentist')
            ->withCount('dentistAppointments')
            ->get();

        return view('admin.reports.index', compact('totalPatients', 'totalAppointments', 'completedAppointments', 'estimatedRevenue', 'topServices', 'doctorsPerformance'));
    }

    /**
     * Clinic Settings.
     */
    public function settings(): View
    {
        return view('admin.settings.index');
    }

    /**
     * Live search endpoint for Admin topbar search.
     * Searches patients, dentists, and services by name/email/phone.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['patients' => [], 'dentists' => [], 'services' => []]);
        }

        $patients = User::where('role', 'patient')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
            })
            ->take(5)
            ->get(['id', 'name', 'email', 'phone']);

        $dentists = User::where('role', 'dentist')
            ->where('name', 'like', "%{$q}%")
            ->take(3)
            ->get(['id', 'name', 'email']);

        $services = Service::where('name', 'like', "%{$q}%")
            ->take(5)
            ->get(['id', 'name', 'price']);

        return response()->json(compact('patients', 'dentists', 'services'));
    }
}
