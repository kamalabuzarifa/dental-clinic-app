<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Service;
use App\Models\User;
use App\Services\WebsiteContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Retrieve clinic settings safely through WebsiteContentService.
     */
    protected function getSettings(): array
    {
        return WebsiteContentService::getClinicSettings();
    }

    /**
     * Format working hours into a human-friendly string.
     */
    protected function formatWorkingHours(array $settings): string
    {
        return WebsiteContentService::getWorkingHoursText($settings);
    }

    /**
     * Display the dynamic Home Page (Multi-page root /).
     */
    public function index(): View
    {
        $settings = $this->getSettings();
        $workingHoursText = $this->formatWorkingHours($settings);
        $seo = WebsiteContentService::getSeoForPage('home');

        // Dynamic ordered home sections
        $homeSections = WebsiteContentService::getHomeSections(true);

        // Dynamic visible services (featured first)
        $allVisibleServices = WebsiteContentService::getVisibleServices();
        $services = $allVisibleServices->where('is_featured', true);
        if ($services->isEmpty()) {
            $services = $allVisibleServices->take(6);
        } else {
            $services = $services->take(6);
        }
        $totalServicesCount = $allVisibleServices->count();

        // Dynamic visible doctors
        $dentists = WebsiteContentService::getVisibleDoctors()->take(6);
        $totalDentistsCount = WebsiteContentService::getVisibleDoctors()->count();

        // Published Testimonials
        $testimonials = WebsiteContentService::getPublishedTestimonials();

        // Published FAQs
        $faqs = WebsiteContentService::getPublishedFaqs();

        try {
            $stats = [
                'patients_count'         => User::where('role', 'patient')->count(),
                'dentists_count'         => $totalDentistsCount,
                'services_count'         => $totalServicesCount,
                'completed_appointments' => Appointment::where('status', 'completed')->count(),
            ];
        } catch (\Throwable $e) {
            $stats = [
                'patients_count'         => 0,
                'dentists_count'         => 0,
                'services_count'         => 0,
                'completed_appointments' => 0,
            ];
        }

        return view('public.home', compact(
            'settings',
            'seo',
            'homeSections',
            'services',
            'dentists',
            'testimonials',
            'faqs',
            'stats',
            'totalServicesCount',
            'totalDentistsCount',
            'workingHoursText'
        ));
    }

    /**
     * Display the About Us page (/about).
     */
    public function about(): View
    {
        $settings = $this->getSettings();
        $workingHoursText = $this->formatWorkingHours($settings);
        $seo = WebsiteContentService::getSeoForPage('about');

        try {
            $stats = [
                'patients_count'         => User::where('role', 'patient')->count(),
                'dentists_count'         => WebsiteContentService::getVisibleDoctors()->count(),
                'services_count'         => WebsiteContentService::getVisibleServices()->count(),
                'completed_appointments' => Appointment::where('status', 'completed')->count(),
            ];
        } catch (\Throwable $e) {
            $stats = ['patients_count' => 0, 'dentists_count' => 0, 'services_count' => 0, 'completed_appointments' => 0];
        }

        return view('public.about', compact('settings', 'seo', 'workingHoursText', 'stats'));
    }

    /**
     * Display all Services (/services).
     */
    public function services(): View
    {
        $settings = $this->getSettings();
        $workingHoursText = $this->formatWorkingHours($settings);
        $seo = WebsiteContentService::getSeoForPage('services');

        $services = WebsiteContentService::getVisibleServices();

        return view('public.services.index', compact('settings', 'seo', 'services', 'workingHoursText'));
    }

    /**
     * Display a specific Service details (/services/{service}).
     */
    public function serviceDetail(Service $service): View
    {
        // Enforce visible on website unless previewed by authenticated admin
        if (!$service->show_on_website && !(Auth::check() && Auth::user()->role === 'admin')) {
            abort(404, 'الخدمة المطلوبة غير متوفرة حالياً.');
        }

        $settings = $this->getSettings();
        $workingHoursText = $this->formatWorkingHours($settings);

        $seo = [
            'title'       => "{$service->name} | " . ($settings['clinic_name'] ?? 'مركز ريادة'),
            'description' => $service->description ?? "تعرف على تفاصيل خدمة {$service->name} في مركز ريادة لطب وجراحة الأسنان.",
            'keywords'    => "{$service->name}, خدمات طب الأسنان, مركز ريادة",
        ];

        try {
            $relatedServices = WebsiteContentService::getVisibleServices()
                ->where('id', '!=', $service->id)
                ->take(4);
        } catch (\Throwable $e) {
            $relatedServices = collect();
        }

        return view('public.services.show', compact('settings', 'seo', 'service', 'relatedServices', 'workingHoursText'));
    }

    /**
     * Display all Doctors (/doctors).
     */
    public function doctors(): View
    {
        $settings = $this->getSettings();
        $workingHoursText = $this->formatWorkingHours($settings);
        $seo = WebsiteContentService::getSeoForPage('doctors');

        $doctors = WebsiteContentService::getVisibleDoctors();

        return view('public.doctors.index', compact('settings', 'seo', 'doctors', 'workingHoursText'));
    }

    /**
     * Display a specific Doctor details (/doctors/{doctor}).
     */
    public function doctorDetail(User $doctor): View
    {
        if ($doctor->role !== 'dentist' || (!$doctor->show_on_website && !(Auth::check() && Auth::user()->role === 'admin'))) {
            abort(404, 'الطبيب المطلوب غير متوفر حالياً.');
        }

        $settings = $this->getSettings();
        $workingHoursText = $this->formatWorkingHours($settings);

        $seo = [
            'title'       => "{$doctor->name} - " . ($doctor->specialty ?? 'طبيب أسنان') . " | " . ($settings['clinic_name'] ?? 'مركز ريادة'),
            'description' => $doctor->bio ?? "تعرف على الطبيب {$doctor->name}، استشاري طب الأسنان في مركز ريادة لطب وجراحة الأسنان.",
            'keywords'    => "{$doctor->name}, " . ($doctor->specialty ?? 'طبيب أسنان') . ", مركز ريادة",
        ];

        try {
            $services = WebsiteContentService::getVisibleServices()->take(6);
            $otherDoctors = WebsiteContentService::getVisibleDoctors()
                ->where('id', '!=', $doctor->id)
                ->take(3);
        } catch (\Throwable $e) {
            $services = collect();
            $otherDoctors = collect();
        }

        return view('public.doctors.show', compact('settings', 'seo', 'doctor', 'services', 'otherDoctors', 'workingHoursText'));
    }

    /**
     * Display the Appointment Booking page (/appointments/book).
     */
    public function bookAppointment(Request $request): View
    {
        $settings = $this->getSettings();
        $workingHoursText = $this->formatWorkingHours($settings);
        $seo = WebsiteContentService::getSeoForPage('booking');

        $preselectedService = $request->query('service');
        $preselectedDoctor = $request->query('doctor');

        $services = WebsiteContentService::getVisibleServices();
        $dentists = WebsiteContentService::getVisibleDoctors();

        return view('public.appointments.book', compact(
            'settings',
            'seo',
            'services',
            'dentists',
            'workingHoursText',
            'preselectedService',
            'preselectedDoctor'
        ));
    }

    /**
     * Handle submission from /appointments/book.
     */
    public function storeAppointment(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'يرجى تسجيل الدخول أو فتح حساب مريض جديد لإتمام وتأكيد حجز موعدك الطبي.');
        }

        // Delegate to existing AppointmentController::store to reuse validation & conflict algorithms
        return app(AppointmentController::class)->store($request);
    }

    /**
     * Display Contact Us page (/contact).
     */
    public function contact(): View
    {
        $settings = $this->getSettings();
        $workingHoursText = $this->formatWorkingHours($settings);
        $seo = WebsiteContentService::getSeoForPage('contact');

        return view('public.contact', compact('settings', 'seo', 'workingHoursText'));
    }

    /**
     * Handle Contact Us message submission (/contact).
     */
    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'phone'   => ['required', 'string', 'max:25'],
            'email'   => ['nullable', 'email', 'max:100'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'name.required'    => 'يرجى كتابة الاسم الكامل.',
            'phone.required'   => 'يرجى إدخال رقم الهاتف للتواصل.',
            'subject.required' => 'يرجى اختيار موضوع الرسالة.',
            'message.required' => 'يرجى كتابة نص الرسالة أو الاستفسار.',
        ]);

        try {
            AuditLog::record(
                'contact_message',
                "رسالة استفسار جديدة من: {$validated['name']} ({$validated['phone']}) - موضوع: {$validated['subject']}"
            );
        } catch (\Throwable $e) {
            // Silently continue if audit log fails
        }

        return back()->with('success', 'شكراً لتواصلك معنا! تم استلام رسالتك واستفسارك بنجاح، وسيتواصل معك فريق الاستقبال قريباً.');
    }
}
