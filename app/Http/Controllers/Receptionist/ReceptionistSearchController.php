<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReceptionistSearchController extends Controller
{
    /**
     * Handle live search queries for the Receptionist topbar.
     * Sanitized to prevent leaking internal/confidential data.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'patients'     => [],
                'appointments' => [],
                'services'     => [],
            ]);
        }

        // Only search real patient records (never admins or dentists)
        $patients = User::where('role', 'patient')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->take(5)
            ->get(['id', 'name', 'phone', 'email']);

        // Safe appointment summaries (no medical notes leaked)
        $appointments = Appointment::with([
                'patient:id,name,phone',
                'dentist:id,name',
                'service:id,name'
            ])
            ->where(function ($q) use ($query) {
                $q->where('id', 'like', "%{$query}%")
                  ->orWhereHas('patient', function ($sub) use ($query) {
                      $sub->where('name', 'like', "%{$query}%")
                          ->orWhere('phone', 'like', "%{$query}%");
                  });
            })
            ->latest('appointment_date')
            ->take(5)
            ->get(['id', 'patient_id', 'dentist_id', 'service_id', 'appointment_date', 'start_time', 'end_time', 'status']);

        $services = Service::where('name', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'name', 'price']);

        return response()->json([
            'patients'     => $patients,
            'appointments' => $appointments,
            'services'     => $services,
        ]);
    }
}
