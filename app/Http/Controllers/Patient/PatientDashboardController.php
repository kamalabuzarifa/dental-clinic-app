<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PatientDashboardController extends Controller
{
    /**
     * Display patient's dashboard with past and upcoming appointments.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Upcoming appointments
        $upcomingAppointments = $user->patientAppointments()
            ->with(['dentist', 'service'])
            ->upcoming()
            ->get();

        // Past appointments
        $pastAppointments = $user->patientAppointments()
            ->with(['dentist', 'service'])
            ->past()
            ->take(10)
            ->get();

        // Quick statistics
        $stats = [
            'upcoming_count' => $upcomingAppointments->where('status', '!=', 'cancelled')->count(),
            'completed_count' => $user->patientAppointments()->where('status', 'completed')->count(),
            'total_count' => $user->patientAppointments()->count(),
        ];

        return view('patient.dashboard', compact('upcomingAppointments', 'pastAppointments', 'stats'));
    }
}
