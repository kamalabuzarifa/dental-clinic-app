<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\WebsiteManagementController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Dentist\DentistDashboardController;
use App\Http\Controllers\Patient\PatientDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Receptionist\ReceptionistAppointmentController;
use App\Http\Controllers\Receptionist\ReceptionistDashboardController;
use App\Http\Controllers\Receptionist\ReceptionistNotificationController;
use App\Http\Controllers\Receptionist\ReceptionistPatientController;
use App\Http\Controllers\Receptionist\ReceptionistProfileController;
use App\Http\Controllers\Receptionist\ReceptionistSearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Dental Clinic Booking System
|--------------------------------------------------------------------------
*/

// Public Multi-Page Website Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services.index');
Route::get('/services/{service}', [HomeController::class, 'serviceDetail'])->name('services.show');
Route::get('/doctors', [HomeController::class, 'doctors'])->name('doctors.index');
Route::get('/doctors/{doctor}', [HomeController::class, 'doctorDetail'])->name('doctors.show');
Route::get('/appointments/book', [HomeController::class, 'bookAppointment'])->name('appointments.book');
Route::post('/appointments/book', [HomeController::class, 'storeAppointment'])->name('appointments.book.store');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

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
    Route::get('/search', [ReceptionistSearchController::class, 'search'])->name('search');

    // Patients Management
    Route::resource('patients', ReceptionistPatientController::class);

    // Appointments Management
    Route::get('/appointments/calendar', [ReceptionistAppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::resource('appointments', ReceptionistAppointmentController::class)->except(['show']);
    Route::patch('/appointments/{appointment}/status', [ReceptionistAppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::patch('/appointments/{appointment}/cancel', [ReceptionistAppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Notifications Center
    Route::get('/notifications', [ReceptionistNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [ReceptionistNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/read', [ReceptionistNotificationController::class, 'markAsRead'])->name('notifications.read');

    // Personal Profile
    Route::get('/profile', [ReceptionistProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ReceptionistProfileController::class, 'update'])->name('profile.update');
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

    // 5. Website Management (CMS)
    Route::prefix('website')->as('website.')->group(function () {
        Route::get('/', [WebsiteManagementController::class, 'identity'])->name('index');
        Route::get('/identity', [WebsiteManagementController::class, 'identity'])->name('identity');
        Route::post('/identity', [WebsiteManagementController::class, 'updateIdentity'])->name('identity.update');

        Route::get('/sections', [WebsiteManagementController::class, 'sections'])->name('sections');
        Route::match(['post', 'put'], '/sections/{section}', [WebsiteManagementController::class, 'updateSection'])->name('sections.update');
        Route::match(['post', 'patch'], '/sections/{section}/toggle', [WebsiteManagementController::class, 'toggleSection'])->name('sections.toggle');

        Route::get('/about', [WebsiteManagementController::class, 'about'])->name('about');
        Route::post('/about', [WebsiteManagementController::class, 'updateAbout'])->name('about.update');

        Route::get('/services', [WebsiteManagementController::class, 'services'])->name('services');
        Route::match(['post', 'patch'], '/services/{service}', [WebsiteManagementController::class, 'updateServiceVisibility'])->name('services.visibility');

        Route::get('/doctors', [WebsiteManagementController::class, 'doctors'])->name('doctors');
        Route::match(['post', 'patch'], '/doctors/{doctor}', [WebsiteManagementController::class, 'updateDoctorVisibility'])->name('doctors.visibility');

        Route::get('/testimonials', [WebsiteManagementController::class, 'testimonials'])->name('testimonials');
        Route::post('/testimonials', [WebsiteManagementController::class, 'storeTestimonial'])->name('testimonials.store');
        Route::put('/testimonials/{testimonial}', [WebsiteManagementController::class, 'updateTestimonial'])->name('testimonials.update');
        Route::delete('/testimonials/{testimonial}', [WebsiteManagementController::class, 'destroyTestimonial'])->name('testimonials.destroy');

        Route::get('/faqs', [WebsiteManagementController::class, 'faqs'])->name('faqs');
        Route::post('/faqs', [WebsiteManagementController::class, 'storeFaq'])->name('faqs.store');
        Route::put('/faqs/{faq}', [WebsiteManagementController::class, 'updateFaq'])->name('faqs.update');
        Route::delete('/faqs/{faq}', [WebsiteManagementController::class, 'destroyFaq'])->name('faqs.destroy');

        Route::get('/seo', [WebsiteManagementController::class, 'seo'])->name('seo');
        Route::post('/seo', [WebsiteManagementController::class, 'updateSeo'])->name('seo.update');
    });

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
