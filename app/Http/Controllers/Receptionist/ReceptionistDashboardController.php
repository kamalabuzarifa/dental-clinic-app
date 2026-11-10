<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceptionistDashboardController extends Controller
{
    /**
     * Display the receptionist dashboard with real-time clinic metrics,
     * today's schedule, latest bookings, and quick operational actions.
     */
    public function index(Request $request): View
    {
        $today = Carbon::today()->toDateString();
        $selectedDate = $request->input('date', $today);

        // 1. Core KPIs & Real Statistics
        $stats = [
            'today_total'          => Appointment::whereDate('appointment_date', $today)->count(),
            'today_confirmed'      => Appointment::whereDate('appointment_date', $today)->where('status', 'confirmed')->count(),
            'today_pending'        => Appointment::whereDate('appointment_date', $today)->where('status', 'pending')->count(),
            'today_completed'      => Appointment::whereDate('appointment_date', $today)->where('status', 'completed')->count(),
            'upcoming_total'       => Appointment::whereDate('appointment_date', '>', $today)->whereNotIn('status', ['cancelled'])->count(),
            'total_patients'       => User::where('role', 'patient')->count(),
            'needs_followup_count' => Appointment::where('status', 'pending')->whereDate('appointment_date', '<=', $today)->count(),
        ];

        // 2. Schedule for the selected date
        $dailyAppointments = Appointment::with(['patient', 'dentist', 'service'])
            ->whereDate('appointment_date', $selectedDate)
            ->orderBy('start_time', 'asc')
            ->get();

        // 3. Latest bookings added across clinic
        $latestAppointments = Appointment::with(['patient', 'dentist', 'service'])
            ->latest('created_at')
            ->take(8)
            ->get();

        // 4. Pending / Needs follow-up appointments
        $pendingAppointments = Appointment::with(['patient', 'dentist', 'service'])
            ->where('status', 'pending')
            ->whereDate('appointment_date', '>=', $today)
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(6)
            ->get();

        // 5. Active Doctors and Services for quick reference
        $dentists = User::where('role', 'dentist')->orderBy('name')->get();
        $services = Service::orderBy('name')->get();

        return view('receptionist.dashboard', compact(
            'stats',
            'dailyAppointments',
            'latestAppointments',
            'pendingAppointments',
            'selectedDate',
            'dentists',
            'services',
            'today'
        ));
    }
}
