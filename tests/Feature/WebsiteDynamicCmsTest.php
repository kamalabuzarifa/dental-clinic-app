<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\HomeSection;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Services\WebsiteContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteDynamicCmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularPatient;

    protected function setUp(): void
    {
        parent::setUp();

        WebsiteContentService::clearCache();

        $this->adminUser = User::factory()->create([
            'role'      => 'admin',
            'name'      => 'مدير النظام الفعلي',
            'email'     => 'admin_test@riyadaclinic.com',
            'is_active' => true,
        ]);

        $this->regularPatient = User::factory()->create([
            'role'      => 'patient',
            'name'      => 'المريض أحمد',
            'email'     => 'patient_test@riyadaclinic.com',
            'is_active' => true,
        ]);

        Setting::set('clinic_name', 'مركز ريادة لطب وجراحة الأسنان');
        Setting::set('clinic_name_en', 'RIYADA DENTAL CARE');
        Setting::set('clinic_phone', '0596482371');
        Setting::set('clinic_email', 'infoclini@riyadaclinic.com');
        Setting::set('clinic_address', 'فلسطين - غزة - مفترق السرايا - برج الإتقان');
        Setting::set('maintenance_mode', '0');
    }

    public function test_guest_and_patient_cannot_access_website_cms()
    {
        // Guest
        $response = $this->get(route('admin.website.identity'));
        $response->assertRedirect(route('login'));

        // Patient
        $response = $this->actingAs($this->regularPatient)->get(route('admin.website.identity'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_all_cms_pages()
    {
        $routes = [
            'admin.website.identity',
            'admin.website.sections',
            'admin.website.about',
            'admin.website.services',
            'admin.website.doctors',
            'admin.website.testimonials',
            'admin.website.faqs',
            'admin.website.seo',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($this->adminUser)->get(route($routeName));
            $response->assertOk();
            $response->assertViewIs($routeName);
        }
    }

    public function test_admin_can_update_clinic_identity_and_it_reflects_on_public_site()
    {
        $uniqueName = 'مركز ريادة الدولي ' . rand(100, 999);
        $uniquePhone = '0599' . rand(100000, 999999);

        $response = $this->actingAs($this->adminUser)->post(route('admin.website.identity.update'), [
            'clinic_name'        => $uniqueName,
            'clinic_name_en'     => 'RIYADA CLINIC TEST',
            'clinic_phone'       => $uniquePhone,
            'clinic_email'       => 'test_cms@riyada.ps',
            'clinic_address'     => 'غزة - شارع عمر المختار',
            'clinic_description' => 'وصف تعريفي اختباري محدث ديناميكياً.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check settings in DB
        $this->assertEquals($uniqueName, Setting::get('clinic_name'));
        $this->assertEquals($uniquePhone, Setting::get('clinic_phone'));

        // Visit public homepage
        WebsiteContentService::clearCache();
        $publicResponse = $this->get(route('home'));
        $publicResponse->assertOk();
        $publicResponse->assertSee($uniqueName);
        $publicResponse->assertSee($uniquePhone);
    }

    public function test_admin_can_toggle_home_sections_visibility()
    {
        // Ensure default sections are initialized
        WebsiteContentService::getHomeSections(false);

        $testimonialsSection = HomeSection::where('section_key', 'testimonials')->first();
        $this->assertNotNull($testimonialsSection);

        // Turn testimonials section OFF
        $response = $this->actingAs($this->adminUser)->patch(route('admin.website.sections.toggle', $testimonialsSection->id));
        $response->assertRedirect();
        $this->assertEquals(0, $testimonialsSection->fresh()->is_visible);

        // Turn testimonials section ON
        $response = $this->actingAs($this->adminUser)->patch(route('admin.website.sections.toggle', $testimonialsSection->id));
        $response->assertRedirect();
        $this->assertEquals(1, $testimonialsSection->fresh()->is_visible);
    }

    public function test_admin_can_update_home_section_text_and_order()
    {
        $heroSection = HomeSection::where('section_key', 'hero')->first();
        if (!$heroSection) {
            WebsiteContentService::getHomeSections(false);
            $heroSection = HomeSection::where('section_key', 'hero')->first();
        }
        $this->assertNotNull($heroSection);

        $newTitle = 'عنوان هيرو اختباري ديناميكي ' . rand(10, 99);

        $response = $this->actingAs($this->adminUser)->put(route('admin.website.sections.update', $heroSection->id), [
            'title'      => $newTitle,
            'subtitle'   => 'وصف هيرو اختباري محدث من لوحة الإدارة',
            'badge'      => 'شارة اختبارية',
            'sort_order' => 1,
            'is_visible' => 1,
        ]);

        $response->assertRedirect();
        $this->assertEquals($newTitle, $heroSection->fresh()->title);

        WebsiteContentService::clearCache();
        $publicResponse = $this->get(route('home'));
        $publicResponse->assertOk();
        $publicResponse->assertSee($newTitle);
    }

    public function test_admin_can_control_service_visibility_on_public_pages()
    {
        $service = Service::create([
            'name'             => 'خدمة تبييض ليزري خاصة ' . rand(10, 99),
            'description'      => 'تفاصيل الخدمة المخفية',
            'duration_minutes' => 45,
            'price'            => 350.00,
            'is_active'        => true,
            'show_on_website'  => true,
        ]);

        // Hide service from website
        $response = $this->actingAs($this->adminUser)->patch(route('admin.website.services.visibility', $service->id), [
            'show_on_website' => 0,
            'is_featured'     => 0,
            'sort_order'      => 99,
        ]);
        $response->assertRedirect();
        $this->assertFalse((bool) $service->fresh()->show_on_website);

        // Guest visiting /services should NOT see this service (flush admin session & logout)
        auth()->logout();
        $this->flushSession();
        WebsiteContentService::clearCache();
        $servicesPage = $this->get(route('services.index'));
        $servicesPage->assertOk();
        $servicesPage->assertDontSee($service->name);

        // Guest visiting the direct service detail page receives 404
        $detailResponse = $this->get(route('services.show', $service->id));
        $detailResponse->assertNotFound();

        // But authenticated admin can preview it
        $adminPreview = $this->actingAs($this->adminUser)->get(route('services.show', $service->id));
        $adminPreview->assertOk();
    }

    public function test_admin_can_control_doctor_visibility_on_public_pages()
    {
        $doctor = User::factory()->create([
            'role'            => 'dentist',
            'name'            => 'د. حاتم المسعودي ' . rand(10, 99),
            'specialty'       => 'استشاري تقويم أسنان',
            'bio'             => 'سيرة مهنية مخصصة للطبيب',
            'is_active'       => true,
            'show_on_website' => true,
        ]);

        // Hide doctor from public website
        $response = $this->actingAs($this->adminUser)->patch(route('admin.website.doctors.visibility', $doctor->id), [
            'show_on_website' => 0,
            'specialty'       => 'استشاري جراحة',
            'bio'             => 'سيرة جديدة محدثة',
            'sort_order'      => 5,
        ]);
        $response->assertRedirect();
        $this->assertFalse((bool) $doctor->fresh()->show_on_website);

        // Guest visiting /doctors should NOT see this doctor (flush admin session & logout)
        auth()->logout();
        $this->flushSession();
        WebsiteContentService::clearCache();
        $doctorsPage = $this->get(route('doctors.index'));
        $doctorsPage->assertOk();
        $doctorsPage->assertDontSee($doctor->name);

        // Direct show page should be 404 for guests
        $detailResponse = $this->get(route('doctors.show', $doctor->id));
        $detailResponse->assertNotFound();

        // Admin can still view preview
        $adminPreview = $this->actingAs($this->adminUser)->get(route('doctors.show', $doctor->id));
        $adminPreview->assertOk();
    }

    public function test_admin_can_crud_testimonials_and_reflects_on_homepage()
    {
        $patientName = 'المريض خالد النجار ' . rand(100, 999);
        $reviewText = 'تجربة ممتازة جداً ونتائج مبهرة بدون أي ألم ' . rand(100, 999);

        // 1. Create Testimonial
        $createResponse = $this->actingAs($this->adminUser)->post(route('admin.website.testimonials.store'), [
            'patient_name'       => $patientName,
            'rating'             => 5,
            'review'             => $reviewText,
            'treatment_received' => 'زراعة فورية',
            'status'             => 'published',
            'sort_order'         => 1,
        ]);
        $createResponse->assertRedirect();

        $testimonial = Testimonial::where('patient_name', $patientName)->first();
        $this->assertNotNull($testimonial);

        // Check homepage contains testimonial
        WebsiteContentService::clearCache();
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertOk();
        $homeResponse->assertSee($patientName);
        $homeResponse->assertSee($reviewText);

        // 2. Update Testimonial
        $updatedText = 'تحديث: الخدمة كانت أكثر من رائعة ' . rand(100, 999);
        $updateResponse = $this->actingAs($this->adminUser)->put(route('admin.website.testimonials.update', $testimonial->id), [
            'patient_name'       => $patientName,
            'rating'             => 5,
            'review'             => $updatedText,
            'treatment_received' => 'زراعة فورية',
            'status'             => 'published',
            'sort_order'         => 1,
        ]);
        $updateResponse->assertRedirect();
        $this->assertEquals($updatedText, $testimonial->fresh()->review);

        // 3. Delete Testimonial
        $deleteResponse = $this->actingAs($this->adminUser)->delete(route('admin.website.testimonials.destroy', $testimonial->id));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('testimonials', ['id' => $testimonial->id]);
    }

    public function test_admin_can_crud_faqs_and_reflects_on_homepage()
    {
        $question = 'هل جلسة علاج العصب مؤلمة في مركز ريادة؟ ' . rand(100, 999);
        $answer = 'لا، يتم استخدام التخدير الموضعي الرقمي الحديث وتقنيات العصب الميكانيكية لراحة تامة.';

        // 1. Store FAQ
        $storeResponse = $this->actingAs($this->adminUser)->post(route('admin.website.faqs.store'), [
            'question'   => $question,
            'answer'     => $answer,
            'category'   => 'treatments',
            'status'     => 'published',
            'sort_order' => 1,
        ]);
        $storeResponse->assertRedirect();

        $faq = Faq::where('question', $question)->first();
        $this->assertNotNull($faq);

        // Check homepage displays FAQ
        WebsiteContentService::clearCache();
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertOk();
        $homeResponse->assertSee($question);
        $homeResponse->assertSee($answer);

        // 2. Delete FAQ
        $delResponse = $this->actingAs($this->adminUser)->delete(route('admin.website.faqs.destroy', $faq->id));
        $delResponse->assertRedirect();
        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }

    public function test_admin_can_update_per_page_seo_and_it_renders_in_html()
    {
        $customTitle = 'عيادات ريادة لطب وجراحة الأسنان المتخصصة ' . rand(100, 999);
        $customDesc = 'وصف مخصص لمحركات البحث جوجل معتمد من الإدارة.';
        $customKeywords = 'أسنان, زراعة, غزة, ريادة';

        $response = $this->actingAs($this->adminUser)->post(route('admin.website.seo.update'), [
            'seo_about_title'       => $customTitle,
            'seo_about_description' => $customDesc,
            'seo_about_keywords'    => $customKeywords,
        ]);
        $response->assertRedirect();

        // Check /about reflects the new SEO title and meta description
        WebsiteContentService::clearCache();
        $aboutResponse = $this->get(route('about'));
        $aboutResponse->assertOk();
        $aboutResponse->assertSee('<title>' . $customTitle . '</title>', false);
        $aboutResponse->assertSee($customDesc);
    }
}
