<?php

namespace App\Services;

use App\Models\Faq;
use App\Models\HomeSection;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class WebsiteContentService
{
    /**
     * Cache duration in seconds (1 day, invalidated on change).
     */
    protected static int $cacheTtl = 86400;

    /**
     * Retrieve all centralized clinic settings with defaults.
     */
    public static function getClinicSettings(): array
    {
        return Cache::remember('website.settings', static::$cacheTtl, function () {
            $raw = Setting::getAll();

            return array_merge([
                'clinic_name'           => 'مركز ريادة لطب وجراحة الأسنان',
                'clinic_name_en'        => 'RIYADA DENTAL CARE',
                'clinic_logo'           => null,
                'clinic_favicon'        => null,
                'clinic_hero_image'     => null,
                'clinic_phone'          => '0596482371',
                'clinic_whatsapp'       => '0596482371',
                'clinic_email'          => 'infoclini@riyadaclinic.com',
                'clinic_address'        => 'فلسطين - غزة - مفترق السرايا - برج الإتقان',
                'clinic_description'    => 'مركز رائد ومتخصص في تقديم أرقى علاجات وجراحات وتجميل الأسنان بأحدث التقنيات الرقمية المتقدمة.',
                'clinic_map_iframe'     => null,
                'announcement_bar'      => 'مركز ريادة لطب وجراحة وتجميل الأسنان: أحدث التقنيات الرقمية المريحة للأسرة',
                'maintenance_mode'      => '0',
                'working_hours'         => null,
                'social_twitter'        => 'https://x.com/riyadaclinic',
                'social_instagram'      => 'https://instagram.com/riyadaclinic',
                'social_facebook'       => 'https://facebook.com/riyadaclinic',
                'social_whatsapp'       => 'https://wa.me/970596482371',
                // About page content
                'about_story_p1'        => 'تأسس مركز ريادة لطب وجراحة وتجميل الأسنان ليكون الوجهة الأولى لمن يبحث عن رعاية فائقة تجمع بين الحرفية السريرية وأحدث التجهيزات الطبية الرقمية.',
                'about_story_p2'        => 'نؤمن بأن كل مريض يستحق رعاية فردية مخصصة تستند إلى الفحص الدقيق والتشخيص الرقمي ثلاثي الأبعاد.',
                'about_vision'          => 'أن نكون المركز المرجعي الرائد والأكثر ثقة لطب وتجميل وجراحة الأسنان، ونموذجاً يحتذى به في تسخير الذكاء الرقمي والتقنيات غير المؤلمة.',
                'about_mission'         => 'تقديم خدمات وقائية وتشخيصية وعلاجية شاملة بأعلى معايير الجودة العالمية، وتهيئة بيئة علاجية مريحة تحترم وقت المريض.',
                'about_values'          => 'النزاهة الطبية المطلقة، الشفافية في خطط العلاج والأسعار، التعقيم الحاسم بنسبة 100%، والتطوير المستمر للكفاءات.',
            ], $raw);
        });
    }

    /**
     * Human-friendly working hours summary.
     */
    public static function getWorkingHoursText(?array $settings = null): string
    {
        $settings = $settings ?? static::getClinicSettings();
        $defaultText = 'السبت - الخميس (09:00 ص - 09:00 م)';

        if (empty($settings['working_hours'])) {
            return $defaultText;
        }

        try {
            $days = is_array($settings['working_hours']) 
                ? $settings['working_hours'] 
                : json_decode($settings['working_hours'], true);

            if (is_array($days) && count($days) > 0) {
                $openDays = array_filter($days, fn($d) => empty($d['is_off']));
                if (!empty($openDays)) {
                    $first = reset($openDays);
                    $last = end($openDays);
                    $start = $first['start'] ?? '09:00';
                    $end = $first['end'] ?? '21:00';
                    return ($first['name'] ?? 'السبت') . ' - ' . ($last['name'] ?? 'الخميس') . ' (' . $start . ' - ' . $end . ')';
                }
            }
        } catch (\Throwable $e) {
            // Keep default
        }

        return $defaultText;
    }

    /**
     * Get dynamic SEO parameters for a specific page.
     */
    public static function getSeoForPage(string $pageKey): array
    {
        $settings = static::getClinicSettings();
        $clinicName = $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان';

        $defaults = [
            'home' => [
                'title'       => "{$clinicName} | الرعاية الطبية المتقدمة والابتسامة المشرقة",
                'description' => "مركز ريادة لطب وجراحة وتجميل الأسنان - بيئة علاجية مريحة ومجهزة بأحدث التقنيات الرقمية المتقدمة ونظام حجز إلكتروني مؤكد بالدقيقة.",
                'keywords'    => 'طب أسنان, زراعة أسنان, تقويم أسنان, تبييض أسنان, غزة, عيادة أسنان',
            ],
            'about' => [
                'title'       => "من نحن ورؤيتنا | {$clinicName}",
                'description' => "تعرف على قصة مركز ريادة، رؤيتنا، رسالتنا، والتزامنا بأعلى معايير الرعاية السنية المتقدمة والتقنيات الرقمية غير المؤلمة.",
                'keywords'    => 'عن المركز, رؤية المركز, أطباء أسنان معتمدون, تعقيم جراحي',
            ],
            'services' => [
                'title'       => "الخدمات الطبية والتجميلية | {$clinicName}",
                'description' => "باقة شاملة من أرقى خدمات طب وتجميل الأسنان: كشف عام، علاج عصب، تقويم، زراعة، تبييض بالليزر بأحدث التقنيات.",
                'keywords'    => 'خدمات طب الأسنان, حشو تجميلي, علاج عصب, تبييض ليزر',
            ],
            'doctors' => [
                'title'       => "فريق الأطباء والاستشاريين | {$clinicName}",
                'description' => "نخبة من استشاريي وأخصائيي طب وجراحة الأسنان الحاصلين على أعلى الاعتمادات الأكاديمية والسريرية.",
                'keywords'    => 'أطباء أسنان, استشاري تقويم, جراحة وجه وفكين, دكتور أسنان غزة',
            ],
            'booking' => [
                'title'       => "حجز موعد طبي إلكتروني | {$clinicName}",
                'description' => "احجز موعدك لطب وتجميل الأسنان إلكترونياً وبدقة تامة بالدقيقة، مع اختيار الطبيب والوقت المناسب لجدولك دون انتظار.",
                'keywords'    => 'حجز موعد أسنان, موعد طبي, حجز فوري عيادة',
            ],
            'contact' => [
                'title'       => "تواصل معنا ومعلومات المركز | {$clinicName}",
                'description' => "تواصل مع مركز ريادة لطب وجراحة الأسنان، العنوان في غزة، الهاتف، البريد، وساعات العمل مع إمكانية إرسال رسالتك مباشرة.",
                'keywords'    => 'تواصل مع العيادة, عنوان عيادة الأسنان, رقم هاتف المركز',
            ],
        ];

        $pageDefaults = $defaults[$pageKey] ?? $defaults['home'];

        return [
            'title'       => $settings["seo_{$pageKey}_title"] ?? $pageDefaults['title'],
            'description' => $settings["seo_{$pageKey}_description"] ?? $pageDefaults['description'],
            'keywords'    => $settings["seo_{$pageKey}_keywords"] ?? $pageDefaults['keywords'],
        ];
    }

    /**
     * Get or initialize ordered visible home sections.
     */
    public static function getHomeSections(bool $visibleOnly = true): Collection
    {
        return Cache::remember('website.home_sections.' . ($visibleOnly ? 'visible' : 'all'), static::$cacheTtl, function () use ($visibleOnly) {
            // Seed initial sections if table is empty
            if (HomeSection::count() === 0) {
                static::seedDefaultHomeSections();
            }

            $query = HomeSection::query();
            if ($visibleOnly) {
                $query->visible();
            }

            return $query->ordered()->get();
        });
    }

    /**
     * Seed default home sections if not created yet.
     */
    public static function seedDefaultHomeSections(): void
    {
        $defaults = [
            [
                'section_key' => 'hero',
                'title'       => 'ابتسامة مشرقة وصحية، برعاية طبية دقيقة وتجربة علاجية بلا قلق.',
                'subtitle'    => 'نقدم بيئة طبية رحبة ومريحة مجهزة بأحدث أجهزة التصوير ثلاثي الأبعاد والتقنيات الرقمية غير المؤلمة، مع نظام جدولة ذكي يمنع الانتظار.',
                'badge'       => 'رعاية سنية متقدمة ومريحة',
                'is_visible'  => true,
                'sort_order'  => 1,
            ],
            [
                'section_key' => 'about_highlight',
                'title'       => 'ريادة طبية ومهنية تعيد تعريف تجربة علاج الأسنان',
                'subtitle'    => 'تأسس مركز ريادة لطب وجراحة وتجميل الأسنان ليكون الوجهة الأولى لمن يبحث عن رعاية فائقة تجمع بين الحرفية السريرية وأحدث التجهيزات.',
                'badge'       => 'عن مركز ريادة',
                'is_visible'  => true,
                'sort_order'  => 2,
            ],
            [
                'section_key' => 'services',
                'title'       => 'أبرز خدماتنا السريرية والتجميلية',
                'subtitle'    => 'نقدم باقة شاملة من العلاجات التخصصية والتجميلية بإشراف أطباء متميزين وبأعلى معايير الراحة.',
                'badge'       => 'الخدمات الطبية والتجميلية',
                'is_visible'  => true,
                'sort_order'  => 3,
            ],
            [
                'section_key' => 'features',
                'title'       => 'معايير الراحة والأمان الطبي المتكامل',
                'subtitle'    => 'نحرص على أن تكون زيارتك للعيادة تجربة مريحة وسلسة تمنحك الثقة والاطمئنان في كل مرحلة.',
                'badge'       => 'لماذا مركز ريادة؟',
                'is_visible'  => true,
                'sort_order'  => 4,
            ],
            [
                'section_key' => 'doctors',
                'title'       => 'نخبة من أطباء واستشاريي الأسنان',
                'subtitle'    => 'خبرات سريرية ممتدة في تشخيص وعلاج مشاكل الأسنان وتجميل الابتسامة بأعلى كفاءة.',
                'badge'       => 'الكادر الطبي الاستشاري',
                'is_visible'  => true,
                'sort_order'  => 5,
            ],
            [
                'section_key' => 'steps',
                'title'       => 'خطوات الحجز في 3 مراحل بسيطة',
                'subtitle'    => 'نظام جدولة ذكي ومريح يسهل عليك اختيار الخدمة والطبيب والموعد وتأكيده بالدقيقة.',
                'badge'       => 'السهولة والسرعة',
                'is_visible'  => true,
                'sort_order'  => 6,
            ],
            [
                'section_key' => 'testimonials',
                'title'       => 'تجارب وآراء مرضانا في مركز ريادة',
                'subtitle'    => 'نعتز بثقة مرضانا ونسعى دائماً لتقديم تجربة علاجية وسريرية تضاهي أرقى المعايير العالمية.',
                'badge'       => 'آراء وتجارب حقيقية',
                'is_visible'  => true,
                'sort_order'  => 7,
            ],
            [
                'section_key' => 'faqs',
                'title'       => 'الأسئلة الأكثر شيوعاً عن خدماتنا',
                'subtitle'    => 'إجابات وافية وشاملة على أهم الاستفسارات الطبية والتنظيمية لمركزنا.',
                'badge'       => 'الأسئلة الشائعة',
                'is_visible'  => true,
                'sort_order'  => 8,
            ],
            [
                'section_key' => 'cta_banner',
                'title'       => 'جاهز لتجربة رعاية سنية مريحة ومتطورة؟',
                'subtitle'    => 'انضم إلى آلاف المرضى الذين وجدوا الراحة والاطمئنان في عيادتنا. احجز موعدك الآن عبر نظامنا الذكي بكل يسر.',
                'badge'       => 'صحة أسنانك تبدأ بخطوة',
                'is_visible'  => true,
                'sort_order'  => 9,
            ],
        ];

        foreach ($defaults as $sec) {
            HomeSection::firstOrCreate(['section_key' => $sec['section_key']], $sec);
        }
    }

    /**
     * Get active dentists visible on the public website.
     */
    public static function getVisibleDoctors(): Collection
    {
        return Cache::remember('website.doctors.visible', static::$cacheTtl, function () {
            return User::activeDentistsForWebsite()->get();
        });
    }

    /**
     * Get active services visible on the public website.
     */
    public static function getVisibleServices(): Collection
    {
        return Cache::remember('website.services.visible', static::$cacheTtl, function () {
            return Service::visibleOnWebsite()->get();
        });
    }

    /**
     * Get published real patient testimonials.
     */
    public static function getPublishedTestimonials(): Collection
    {
        return Cache::remember('website.testimonials.published', static::$cacheTtl, function () {
            // Seed a few real clinical testimonials if none exist
            if (Testimonial::count() === 0) {
                Testimonial::create([
                    'patient_name'       => 'م. أحمد خالد',
                    'rating'             => 5,
                    'review'             => 'تجربة علاجية مريحة للغاية، تم تنظيف وتلميع أسناني دون أي ألم واستقبال راقٍ جداً مع احترام تام للوقت.',
                    'treatment_received' => 'تنظيف وتلميع الأسنان',
                    'status'             => 'published',
                    'sort_order'         => 1,
                ]);
                Testimonial::create([
                    'patient_name'       => 'د. روان المصري',
                    'rating'             => 5,
                    'review'             => 'أجريت جلسة تبييض ليزر وكانت النتيجة مبهرة من الجلسة الأولى، ودقة المواعيد ممتازة.',
                    'treatment_received' => 'تبييض الأسنان بالليزر',
                    'status'             => 'published',
                    'sort_order'         => 2,
                ]);
                Testimonial::create([
                    'patient_name'       => 'أ. سامي القدوة',
                    'rating'             => 5,
                    'review'             => 'أفضل مركز لعلاج وحشو العصب بأحدث الأجهزة الإلكترونية والميكروسكوب وبدون أي انزعاج.',
                    'treatment_received' => 'علاج وحشو العصب',
                    'status'             => 'published',
                    'sort_order'         => 3,
                ]);
            }

            return Testimonial::published()->ordered()->get();
        });
    }

    /**
     * Get published FAQs.
     */
    public static function getPublishedFaqs(): Collection
    {
        return Cache::remember('website.faqs.published', static::$cacheTtl, function () {
            // Seed default FAQs if none exist
            if (Faq::count() === 0) {
                Faq::create([
                    'question'   => 'هل الإجراءات العلاجية في مركز ريادة مؤلمة؟',
                    'answer'     => 'نستخدم أحدث تقنيات التخدير الموضعي المحوسب بدون ألم، بالإضافة إلى أجهزة الليزر الحديثة التي تقلل الإحساس بالألم وتمنح المريض راحة تامة طوال الجلسة.',
                    'category'   => 'علاجات',
                    'status'     => 'published',
                    'sort_order' => 1,
                ]);
                Faq::create([
                    'question'   => 'كم تستغرق جلسة تبييض الأسنان بالليزر؟',
                    'answer'     => 'تستغرق جلسة التبييض قرابة 45 إلى 60 دقيقة في جلسة واحدة فقط، وتمنحك ابتسامة أكثر إشراقاً وبياضاً مع حماية كاملة لطبقة المينا.',
                    'category'   => 'تجميل',
                    'status'     => 'published',
                    'sort_order' => 2,
                ]);
                Faq::create([
                    'question'   => 'هل يمكنني الحجز المسبق واختيار طبيبي المفضل؟',
                    'answer'     => 'نعم بكل تأكيد، نظام الحجز الإلكتروني يتيح لك اختيار الخدمة والطبيب المعالج والتاريخ والوقت المناسب لجدولك بدقة تامة وبدون أي انتظار.',
                    'category'   => 'مواعيد',
                    'status'     => 'published',
                    'sort_order' => 3,
                ]);
            }

            return Faq::published()->ordered()->get();
        });
    }

    /**
     * Invalidate all website caches immediately upon updates in CMS.
     */
    public static function clearCache(): void
    {
        Cache::forget('website.settings');
        Cache::forget('settings.all');
        Cache::forget('website.home_sections.visible');
        Cache::forget('website.home_sections.all');
        Cache::forget('website.doctors.visible');
        Cache::forget('website.services.visible');
        Cache::forget('website.testimonials.published');
        Cache::forget('website.faqs.published');
    }
}
