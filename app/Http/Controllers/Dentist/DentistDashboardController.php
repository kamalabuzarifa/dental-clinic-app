<?php

namespace App\Http\Controllers\Dentist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DentistDashboardController extends Controller
{
    /**
     * Display dentist's schedule and appointments.
     */
    public function index(Request $request): View
    {
        $dentist = Auth::user();

        // Today's appointments
        $todayAppointments = $dentist->dentistAppointments()
            ->with(['patient', 'service'])
            ->whereDate('appointment_date', now()->toDateString())
            ->orderBy('start_time', 'asc')
            ->get();

        // Future appointments
        $upcomingAppointments = $dentist->dentistAppointments()
            ->with(['patient', 'service'])
            ->whereDate('appointment_date', '>', now()->toDateString())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(15)
            ->get();

        $stats = [
            'today_count' => $todayAppointments->count(),
            'today_completed' => $todayAppointments->where('status', 'completed')->count(),
            'upcoming_count' => $upcomingAppointments->where('status', '!=', 'cancelled')->count(),
            'total_patients' => $dentist->dentistAppointments()->distinct('patient_id')->count('patient_id'),
        ];

        return view('dentist.dashboard', compact('todayAppointments', 'upcomingAppointments', 'stats'));
    }
}
