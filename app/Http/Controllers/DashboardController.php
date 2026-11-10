<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Redirect authenticated users to their specific role dashboard.
     */
    public function index(Request $request): RedirectResponse
    {
        $role = $request->user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'dentist' => redirect()->route('dentist.dashboard'),
            'receptionist' => redirect()->route('receptionist.dashboard'),
            default => redirect()->route('patient.dashboard'),
        };
    }
}
