<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the dynamic landing page.
     */
    public function index()
    {
        try {
            $settings = Setting::getAll();
        } catch (\Throwable $e) {
            $settings = [];
        }

        try {
            $services = Service::latest()->take(6)->get();
        } catch (\Throwable $e) {
            $services = collect();
        }

        try {
            $dentists = User::where('role', 'dentist')->take(6)->get();
        } catch (\Throwable $e) {
            $dentists = collect();
        }

        try {
            $stats = [
                'patients_count' => User::where('role', 'patient')->count(),
                'dentists_count' => User::where('role', 'dentist')->count(),
                'services_count' => Service::count(),
                'completed_appointments' => Appointment::where('status', 'completed')->count(),
            ];
        } catch (\Throwable $e) {
            $stats = [
                'patients_count' => 0,
                'dentists_count' => 0,
                'services_count' => 0,
                'completed_appointments' => 0,
            ];
        }

        // Generate human-friendly working hours summary if available
        $workingHoursText = 'السبت - الخميس (09:00 ص - 09:00 م)';
        if (!empty($settings['working_hours'])) {
            try {
                $days = json_decode($settings['working_hours'], true);
                if (is_array($days) && count($days) > 0) {
                    $openDays = array_filter($days, fn($d) => empty($d['is_off']));
                    if (!empty($openDays)) {
                        $first = reset($openDays);
                        $last = end($openDays);
                        $start = $first['start'] ?? '09:00';
                        $end = $first['end'] ?? '21:00';
                        $workingHoursText = ($first['name'] ?? 'السبت') . ' - ' . ($last['name'] ?? 'الخميس') . ' (' . $start . ' - ' . $end . ')';
                    }
                }
            } catch (\Throwable $e) {
                // fallback remains default
            }
        }

        return view('welcome', compact('settings', 'services', 'dentists', 'stats', 'workingHoursText'));
    }
}
