<?php

namespace App\Http\Controllers\Receptionist;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceptionistDashboardController extends Controller
{
    /**
     * Display receptionist dashboard with daily schedule and all appointments.
     */
    public function index(Request $request): View
    {
        $selectedDate = $request->input('date', now()->toDateString());

        // Appointments for the selected date
        $dailyAppointments = Appointment::with(['patient', 'dentist', 'service'])
            ->whereDate('appointment_date', $selectedDate)
            ->orderBy('start_time', 'asc')
            ->get();

        // Recent / Upcoming general appointments
        $recentAppointments = Appointment::with(['patient', 'dentist', 'service'])
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(20)
            ->get();

        $dentists = User::where('role', 'dentist')->get();
        $services = Service::all();
        $patients = User::where('role', 'patient')->get();

        $stats = [
            'today_total' => Appointment::whereDate('appointment_date', now()->toDateString())->count(),
            'today_confirmed' => Appointment::whereDate('appointment_date', now()->toDateString())->where('status', 'confirmed')->count(),
            'today_completed' => Appointment::whereDate('appointment_date', now()->toDateString())->where('status', 'completed')->count(),
            'total_patients' => User::where('role', 'patient')->count(),
        ];

        return view('receptionist.dashboard', compact(
            'dailyAppointments',
            'recentAppointments',
            'selectedDate',
            'dentists',
            'services',
            'patients',
            'stats'
        ));
    }
}
