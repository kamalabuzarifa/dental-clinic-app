<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Faq;
use App\Models\HomeSection;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\ImageUploadService;
use App\Services\WebsiteContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteManagementController extends Controller
{
    /**
     * Common data for website management tabs.
     */
    protected function getCommonData(): array
    {
        return [
            'settings'         => WebsiteContentService::getClinicSettings(),
            'workingHoursText' => WebsiteContentService::getWorkingHoursText(),
        ];
    }

    /**
     * 1. Clinic Identity & Branding (General Website Settings).
     */
    public function identity(): View
    {
        return view('admin.website.identity', $this->getCommonData());
    }

    /**
     * Update Clinic Identity & Branding.
     */
    public function updateIdentity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'clinic_name'        => ['required', 'string', 'max:150'],
            'clinic_name_en'     => ['nullable', 'string', 'max:150'],
            'clinic_phone'       => ['required', 'string', 'max:30'],
            'clinic_whatsapp'    => ['nullable', 'string', 'max:30'],
            'clinic_email'       => ['required', 'email', 'max:100'],
            'clinic_address'     => ['required', 'string', 'max:255'],
            'clinic_description' => ['nullable', 'string', 'max:1000'],
            'clinic_map_iframe'  => ['nullable', 'string', 'max:2000'],
            'announcement_bar'   => ['nullable', 'string', 'max:255'],
            'maintenance_mode'   => ['nullable', 'in:0,1'],
            'social_twitter'     => ['nullable', 'string', 'max:255'],
            'social_instagram'   => ['nullable', 'string', 'max:255'],
            'social_facebook'    => ['nullable', 'string', 'max:255'],
            'social_whatsapp'    => ['nullable', 'string', 'max:255'],
            // Image uploads
            'clinic_logo'        => ['nullable', 'image', 'mimes:jpeg,png,webp,svg', 'max:2048'],
            'clinic_favicon'     => ['nullable', 'image', 'mimes:jpeg,png,webp,ico,svg', 'max:1024'],
            'clinic_hero_image'  => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:3072'],
        ], [
            'clinic_name.required'    => 'اسم المركز باللغة العربية مطلوب.',
            'clinic_phone.required'   => 'رقم هاتف المركز مطلوب.',
            'clinic_email.required'   => 'البريد الإلكتروني الرسمي مطلوب.',
            'clinic_address.required' => 'عنوان ومقر المركز مطلوب.',
            'clinic_logo.image'       => 'شعار المركز يجب أن يكون ملف صورة صالح.',
            'clinic_logo.max'         => 'حجم الشعار يجب ألا يتجاوز 2 ميجابايت.',
        ]);

        $settings = WebsiteContentService::getClinicSettings();

        // Handle File Uploads via ImageUploadService
        if ($request->hasFile('clinic_logo')) {
            $logoPath = ImageUploadService::upload($request->file('clinic_logo'), 'branding', $settings['clinic_logo'] ?? null);
            Setting::set('clinic_logo', $logoPath);
        }

        if ($request->hasFile('clinic_favicon')) {
            $faviconPath = ImageUploadService::upload($request->file('clinic_favicon'), 'branding', $settings['clinic_favicon'] ?? null);
            Setting::set('clinic_favicon', $faviconPath);
        }

        if ($request->hasFile('clinic_hero_image')) {
            $heroPath = ImageUploadService::upload($request->file('clinic_hero_image'), 'hero', $settings['clinic_hero_image'] ?? null);
            Setting::set('clinic_hero_image', $heroPath);
        }

        // Save Text Fields
        $fields = [
            'clinic_name', 'clinic_name_en', 'clinic_phone', 'clinic_whatsapp',
            'clinic_email', 'clinic_address', 'clinic_description', 'clinic_map_iframe',
            'announcement_bar', 'social_twitter', 'social_instagram', 'social_facebook', 'social_whatsapp',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field));
            }
        }

        Setting::set('maintenance_mode', $request->input('maintenance_mode', '0'));

        WebsiteContentService::clearCache();
        AuditLog::record('website_identity_update', 'تحديث هوية وبيانات المركز العامة في الموقع الإلكتروني');

        return back()->with('success', 'تم حفظ وتحديث بيانات وهوية المركز في الموقع الإلكتروني بنجاح.');
    }

    /**
     * 2. Home Sections Management (Order, Show/Hide, Edit Titles).
     */
    public function sections(): View
    {
        $sections = WebsiteContentService::getHomeSections(false);
        return view('admin.website.sections', array_merge($this->getCommonData(), compact('sections')));
    }

    /**
     * Update a Home Section's content & settings.
     */
    public function updateSection(Request $request, HomeSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'subtitle'   => ['nullable', 'string', 'max:1000'],
            'badge'      => ['nullable', 'string', 'max:100'],
            'is_visible' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $validated['is_visible'] = $request->boolean('is_visible');

        $section->update($validated);
        WebsiteContentService::clearCache();
        AuditLog::record('home_section_update', "تحديث قسم [{$section->title}] في الصفحة الرئيسية", $section);

        return back()->with('success', "تم تحديث إعدادات قسم [{$section->title}] بنجاح.");
    }

    /**
     * Toggle section visibility (Show / Hide).
     */
    public function toggleSection(HomeSection $section): RedirectResponse
    {
        $section->update(['is_visible' => !$section->is_visible]);
        WebsiteContentService::clearCache();

        $status = $section->is_visible ? 'إظهار' : 'إخفاء';
        AuditLog::record('home_section_toggle', "تغيير حالة قسم [{$section->title}] إلى {$status}", $section);

        return back()->with('info', "تم {$status} قسم [{$section->title}] في الصفحة الرئيسية.");
    }

    /**
     * 3. About Us Page Content Management.
     */
    public function about(): View
    {
        return view('admin.website.about', $this->getCommonData());
    }

    /**
     * Update About Us Page Content.
     */
    public function updateAbout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'about_story_p1' => ['required', 'string', 'max:2000'],
            'about_story_p2' => ['nullable', 'string', 'max:2000'],
            'about_vision'   => ['required', 'string', 'max:1500'],
            'about_mission'  => ['required', 'string', 'max:1500'],
            'about_values'   => ['required', 'string', 'max:1500'],
        ]);

        foreach ($validated as $key => $val) {
            Setting::set($key, $val);
        }

        WebsiteContentService::clearCache();
        AuditLog::record('about_content_update', 'تحديث محتوى صفحة من نحن والرؤية والرسالة في الموقع');

        return back()->with('success', 'تم حفظ وتحديث محتوى صفحة "من نحن" بنجاح.');
    }

    /**
     * 4. Website Services Display & Ordering Management.
     */
    public function services(): View
    {
        $services = Service::ordered()->get();
        return view('admin.website.services', array_merge($this->getCommonData(), compact('services')));
    }

    /**
     * Update a Service's visibility, featured status, and ordering on website.
     */
    public function updateServiceVisibility(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'show_on_website' => ['nullable', 'boolean'],
            'is_featured'     => ['nullable', 'boolean'],
            'sort_order'      => ['required', 'integer', 'min:0'],
        ]);

        $service->update([
            'show_on_website' => $request->boolean('show_on_website'),
            'is_featured'     => $request->boolean('is_featured'),
            'sort_order'      => (int) $validated['sort_order'],
        ]);

        WebsiteContentService::clearCache();
        AuditLog::record('service_website_toggle', "تحديث حالة عرض الخدمة [{$service->name}] على الموقع الإلكتروني", $service);

        return back()->with('success', "تم تحديث إعدادات عرض خدمة [{$service->name}] على الموقع بنجاح.");
    }

    /**
     * 5. Website Doctors Display & Ordering Management.
     */
    public function doctors(): View
    {
        $doctors = User::where('role', 'dentist')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.website.doctors', array_merge($this->getCommonData(), compact('doctors')));
    }

    /**
     * Update Doctor's public profile (specialty, bio, visibility, ordering).
     */
    public function updateDoctorVisibility(Request $request, User $doctor): RedirectResponse
    {
        if ($doctor->role !== 'dentist') {
            abort(403);
        }

        $validated = $request->validate([
            'show_on_website' => ['nullable', 'boolean'],
            'specialty'       => ['nullable', 'string', 'max:150'],
            'bio'             => ['nullable', 'string', 'max:1500'],
            'sort_order'      => ['required', 'integer', 'min:0'],
        ]);

        $doctor->update([
            'show_on_website' => $request->boolean('show_on_website'),
            'specialty'       => $validated['specialty'] ?? null,
            'bio'             => $validated['bio'] ?? null,
            'sort_order'      => (int) $validated['sort_order'],
        ]);

        WebsiteContentService::clearCache();
        AuditLog::record('doctor_website_toggle', "تحديث بيانات وعرض الطبيب [{$doctor->name}] على الموقع الإلكتروني", $doctor);

        return back()->with('success', "تم تحديث إعدادات عرض الطبيب [{$doctor->name}] على الموقع بنجاح.");
    }

    /**
     * 6. Real Patient Testimonials / Reviews Management.
     */
    public function testimonials(): View
    {
        $testimonials = Testimonial::ordered()->get();
        return view('admin.website.testimonials', array_merge($this->getCommonData(), compact('testimonials')));
    }

    /**
     * Store a new patient testimonial.
     */
    public function storeTestimonial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_name'       => ['required', 'string', 'max:100'],
            'rating'             => ['required', 'integer', 'min:1', 'max:5'],
            'review'             => ['required', 'string', 'max:1000'],
            'treatment_received' => ['nullable', 'string', 'max:100'],
            'status'             => ['required', 'in:published,draft'],
            'sort_order'         => ['required', 'integer', 'min:0'],
        ], [
            'patient_name.required' => 'اسم صاحب التقييم مطلوب.',
            'review.required'       => 'نص التقييم والتجربة مطلوب.',
        ]);

        $testimonial = Testimonial::create($validated);
        WebsiteContentService::clearCache();
        AuditLog::record('testimonial_create', "إضافة تقييم جديد للمريض [{$testimonial->patient_name}]", $testimonial);

        return back()->with('success', 'تمت إضافة تقييم المريض بنجاح.');
    }

    /**
     * Update an existing testimonial.
     */
    public function updateTestimonial(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $validated = $request->validate([
            'patient_name'       => ['required', 'string', 'max:100'],
            'rating'             => ['required', 'integer', 'min:1', 'max:5'],
            'review'             => ['required', 'string', 'max:1000'],
            'treatment_received' => ['nullable', 'string', 'max:100'],
            'status'             => ['required', 'in:published,draft'],
            'sort_order'         => ['required', 'integer', 'min:0'],
        ]);

        $testimonial->update($validated);
        WebsiteContentService::clearCache();
        AuditLog::record('testimonial_update', "تحديث تقييم المريض [{$testimonial->patient_name}]", $testimonial);

        return back()->with('success', 'تم تحديث التقييم بنجاح.');
    }

    /**
     * Delete a testimonial.
     */
    public function destroyTestimonial(Testimonial $testimonial): RedirectResponse
    {
        AuditLog::record('testimonial_delete', "حذف تقييم المريض [{$testimonial->patient_name}]", $testimonial);
        $testimonial->delete();
        WebsiteContentService::clearCache();

        return back()->with('error', 'تم حذف التقييم بنجاح.');
    }

    /**
     * 7. Website FAQs Management.
     */
    public function faqs(): View
    {
        $faqs = Faq::ordered()->get();
        return view('admin.website.faqs', array_merge($this->getCommonData(), compact('faqs')));
    }

    /**
     * Store a new FAQ.
     */
    public function storeFaq(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question'   => ['required', 'string', 'max:255'],
            'answer'     => ['required', 'string', 'max:2000'],
            'category'   => ['required', 'string', 'max:50'],
            'status'     => ['required', 'in:published,draft'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ], [
            'question.required' => 'نص السؤال مطلوب.',
            'answer.required'   => 'نص الإجابة مطلوب.',
        ]);

        $faq = Faq::create($validated);
        WebsiteContentService::clearCache();
        AuditLog::record('faq_create', "إضافة سؤال شائع جديد: [{$faq->question}]", $faq);

        return back()->with('success', 'تمت إضافة السؤال الشائع بنجاح.');
    }

    /**
     * Update an existing FAQ.
     */
    public function updateFaq(Request $request, Faq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'question'   => ['required', 'string', 'max:255'],
            'answer'     => ['required', 'string', 'max:2000'],
            'category'   => ['required', 'string', 'max:50'],
            'status'     => ['required', 'in:published,draft'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $faq->update($validated);
        WebsiteContentService::clearCache();
        AuditLog::record('faq_update', "تحديث السؤال الشائع: [{$faq->question}]", $faq);

        return back()->with('success', 'تم تحديث السؤال الشائع بنجاح.');
    }

    /**
     * Delete an FAQ.
     */
    public function destroyFaq(Faq $faq): RedirectResponse
    {
        AuditLog::record('faq_delete', "حذف السؤال الشائع: [{$faq->question}]", $faq);
        $faq->delete();
        WebsiteContentService::clearCache();

        return back()->with('error', 'تم حذف السؤال الشائع بنجاح.');
    }

    /**
     * 8. Independent SEO Management for all public pages.
     */
    public function seo(): View
    {
        $pages = [
            'home'     => 'الرئيسية (/)',
            'about'    => 'من نحن (/about)',
            'services' => 'الخدمات الطبية (/services)',
            'doctors'  => 'فريق الأطباء (/doctors)',
            'booking'  => 'حجز موعد (/appointments/book)',
            'contact'  => 'تواصل معنا (/contact)',
        ];

        $seoData = [];
        foreach (array_keys($pages) as $pageKey) {
            $seoData[$pageKey] = WebsiteContentService::getSeoForPage($pageKey);
        }

        return view('admin.website.seo', array_merge($this->getCommonData(), compact('pages', 'seoData')));
    }

    /**
     * Update per-page SEO settings.
     */
    public function updateSeo(Request $request): RedirectResponse
    {
        $pages = ['home', 'about', 'services', 'doctors', 'booking', 'contact'];

        foreach ($pages as $pageKey) {
            if ($request->has("seo_{$pageKey}_title")) {
                Setting::set("seo_{$pageKey}_title", $request->input("seo_{$pageKey}_title"));
            }
            if ($request->has("seo_{$pageKey}_description")) {
                Setting::set("seo_{$pageKey}_description", $request->input("seo_{$pageKey}_description"));
            }
            if ($request->has("seo_{$pageKey}_keywords")) {
                Setting::set("seo_{$pageKey}_keywords", $request->input("seo_{$pageKey}_keywords"));
            }
        }

        WebsiteContentService::clearCache();
        AuditLog::record('seo_settings_update', 'تحديث إعدادات وتحسين محركات البحث SEO لصفحات الموقع العام');

        return back()->with('success', 'تم حفظ وتحديث إعدادات SEO لجميع صفحات الموقع بنجاح.');
    }
}
