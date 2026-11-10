<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Dentist\DentistDashboardController;
use App\Http\Controllers\Patient\PatientDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Receptionist\ReceptionistDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Dental Clinic Booking System
|--------------------------------------------------------------------------
*/

// Public Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Central Authenticated Dashboard Route (Redirects based on user role)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| 1. Patient Routes Group
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:patient'])->prefix('patient')->as('patient.')->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
});

/*
|--------------------------------------------------------------------------
| 2. Dentist Routes Group
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:dentist'])->prefix('dentist')->as('dentist.')->group(function () {
    Route::get('/dashboard', [DentistDashboardController::class, 'index'])->name('dashboard');
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
});

/*
|--------------------------------------------------------------------------
| 3. Receptionist Routes Group
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:receptionist'])->prefix('receptionist')->as('receptionist.')->group(function () {
    Route::get('/dashboard', [ReceptionistDashboardController::class, 'index'])->name('dashboard');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
});

/*
|--------------------------------------------------------------------------
| 4. Admin Routes Group (Full CRUD Resource Routes)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // CRUD Resources (without unneeded show routes)
    Route::resource('doctors', DoctorController::class)->except(['show']);
    Route::resource('patients', PatientController::class)->except(['show']);
    Route::resource('services', ServiceController::class)->except(['show']);
    Route::resource('appointments', AdminAppointmentController::class)->except(['show']);
    
    // Status & Cancel quick actions
    Route::patch('/appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::patch('/appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('appointments.cancel');
    
    // Reports & Settings
    Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports.index');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');

    // Dedicated Settings Sub-Pages
    Route::prefix('settings')->as('settings.')->group(function () {
        Route::get('/clinic', [SettingController::class, 'clinic'])->name('clinic');
        Route::post('/clinic', [SettingController::class, 'updateClinic'])->name('clinic.update');

        Route::get('/booking', [SettingController::class, 'booking'])->name('booking');
        Route::post('/booking', [SettingController::class, 'updateBooking'])->name('booking.update');

        Route::get('/working-hours', [SettingController::class, 'workingHours'])->name('working-hours');
        Route::post('/working-hours', [SettingController::class, 'updateWorkingHours'])->name('working-hours.update');

        Route::get('/notifications', [SettingController::class, 'notifications'])->name('notifications');
        Route::post('/notifications', [SettingController::class, 'updateNotifications'])->name('notifications.update');
        Route::post('/notifications/test-email', [SettingController::class, 'testEmailConnection'])->name('notifications.test-email');
        Route::post('/notifications/test-sms', [SettingController::class, 'testSmsConnection'])->name('notifications.test-sms');

        Route::get('/website', [SettingController::class, 'website'])->name('website');
        Route::post('/website', [SettingController::class, 'updateWebsite'])->name('website.update');

        Route::get('/roles', [SettingController::class, 'roles'])->name('roles');
        Route::post('/roles', [SettingController::class, 'updateRolePermissions'])->name('roles.update');

        Route::get('/users', [SettingController::class, 'users'])->name('users');
        Route::post('/users/{user}/role', [SettingController::class, 'updateUserRole'])->name('users.role');
        Route::post('/users/{user}/toggle-status', [SettingController::class, 'toggleUserStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [SettingController::class, 'resetUserPassword'])->name('users.reset-password');

        Route::get('/security', [SettingController::class, 'security'])->name('security');
        Route::post('/security', [SettingController::class, 'updateSecurity'])->name('security.update');
        Route::post('/security/clear-cache', [SettingController::class, 'clearCache'])->name('security.clear-cache');
        Route::post('/security/clear-logs', [SettingController::class, 'clearLogs'])->name('security.clear-logs');
    });

    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Live Search (AJAX)
    Route::get('/search', [AdminDashboardController::class, 'search'])->name('search');

    // Notifications AJAX Actions
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| Profile Routes (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
