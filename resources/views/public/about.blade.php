@extends('layouts.public')

@section('title', $seo['title'] ?? ('من نحن ورؤيتنا | ' . ($settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان')))
@section('meta_description', $seo['description'] ?? 'تعرف على قصة مركز ريادة لطب وجراحة الأسنان، رؤيتنا، رسالتنا، والتزامنا بأعلى معايير الرعاية السنية المتقدمة والتقنيات الرقمية غير المؤلمة.')
@section('meta_keywords', $seo['keywords'] ?? 'عن المركز, رؤية المركز, أطباء أسنان معتمدون, تعقيم جراحي')
@section('canonical', route('about'))

@section('content')

    <!-- Breadcrumb & Page Header -->
    <section class="bg-gradient-to-b from-blue-50/50 to-white border-b border-slate-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center space-x-2 space-x-reverse text-xs font-semibold text-slate-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-blue-700 transition">الرئيسية</a>
                <span>/</span>
                <span class="text-blue-700 font-bold">من نحن</span>
            </nav>

            <div class="max-w-3xl">
                <span class="inline-block text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-3.5 py-1 rounded-full border border-[#87A96B]/30 mb-3">
                    عن مركز ريادة
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 leading-tight mb-4">
                    قصتنا، رؤيتنا، وشغفنا برسم أجمل الابتسامات
                </h1>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                    منذ انطلاقتنا، وضعنا راحة المريض والدقة الطبية في صميم أولوياتنا، لتقديم تجربة علاجية وسريرية تضاهي أرقى المراكز العالمية.
                </p>
            </div>
        </div>
    </section>

    <!-- Main About Story Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Text Story -->
                <div class="lg:col-span-7 space-y-6">
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">
                        ريادة طبية بمعايير عالمية في قلب غزة
                    </h2>

                    <p class="text-sm text-slate-600 leading-relaxed">
                        {{ $settings['about_story_p1'] ?? ('يقع مركز ريادة لطب وجراحة وتجميل الأسنان في موقع استراتيجي ومتميز (' . ($settings['clinic_address'] ?? 'فلسطين - غزة - مفترق السرايا - برج الإتقان') . '). انطلق المركز بهدف إحداث نقلة نوعية في مفهوم الرعاية السنية، من خلال كسر حاجز القلق والخوف التقليدي من زيارة طبيب الأسنان.') }}
                    </p>

                    <p class="text-sm text-slate-600 leading-relaxed">
                        {{ $settings['about_story_p2'] ?? 'نؤمن بأن كل مريض يستحق رعاية فردية مخصصة تستند إلى الفحص الدقيق والتشخيص الرقمي ثلاثي الأبعاد. لذلك جهزنا عياداتنا بأحدث ما توصلت إليه تكنولوجيا طب الأسنان الحديث، بدءاً من أجهزة الأشعة البانورامية الرقمية، وتقنيات علاج العصب الميكانيكي المتطور، وحتى أنظمة تبييض الأسنان بالليزر الخالي من الحساسية.' }}
                    </p>

                    <!-- Trust Points -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                        <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">✓</div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 mb-1">تعقيم جراحي سويسري</h4>
                                <p class="text-[11px] text-slate-500">بروتوكولات تعقيم صارمة تضمن أعلى درجات السلامة الحيوية لكل مريض.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#87A96B]/10 border border-[#87A96B]/30 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#87A96B] text-white flex items-center justify-center font-bold text-sm flex-shrink-0">✓</div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 mb-1">احترام الوقت بالدقيقة</h4>
                                <p class="text-[11px] text-slate-500">نظام جدولة إلكتروني يضمن دخولك الفوري للعيادة دون إهدار للوقت.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visual Card -->
                <div class="lg:col-span-5">
                    <div class="relative">
                        <div class="absolute -inset-2 rounded-3xl bg-gradient-to-r from-blue-600/20 to-[#87A96B]/20 blur-xl"></div>
                        <div class="relative bg-white rounded-3xl p-6 border border-slate-100 shadow-xl overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?auto=format&fit=crop&w=800&q=80" 
                                 alt="كادر مركز ريادة لطب وجراحة الأسنان" 
                                 class="w-full h-72 object-cover rounded-2xl mb-5 shadow-xs">
                            <div class="p-2 text-center">
                                <span class="text-sm font-black text-slate-900 block mb-1">الرعاية الرحيمة والمهارة السريرية</span>
                                <span class="text-xs text-[#87A96B] font-bold">طاقم طبي واستشاري مؤهل بأعلى الدرجات الأكاديمية</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Vision, Mission & Values -->
    <section class="py-16 bg-[#F8FAFC] border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-3.5 py-1 rounded-full border border-[#87A96B]/30">الركائز الأساسية</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-3 mb-3">الرؤية والرسالة والأهداف</h2>
                <p class="text-slate-500 text-xs sm:text-sm">المبادئ الثابتة التي توجه كل قرار طبي وعلاجي نتخذه في مركز ريادة.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- الرؤية -->
                <div class="p-8 bg-white border border-slate-100 rounded-3xl shadow-xs text-right flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-black mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mb-3">رؤيتنا (Our Vision)</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $settings['about_vision'] ?? 'أن نكون المركز المرجعي الرائد والأكثر ثقة لطب وتجميل وجراحة الأسنان، ونموذجاً يحتذى به في تسخير الذكاء الرقمي والتقنيات غير المؤلمة لإسعاد المريض وإعادة بناء ابتسامته.' }}
                        </p>
                    </div>
                    <div class="pt-5 mt-5 border-t border-slate-100 text-[11px] font-bold text-blue-700">
                        الريادة الطبية المتقدمة
                    </div>
                </div>

                <!-- الرسالة -->
                <div class="p-8 bg-white border border-slate-100 rounded-3xl shadow-xs text-right flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#87A96B]/20 text-[#557143] flex items-center justify-center text-xl font-black mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mb-3">رسالتنا (Our Mission)</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $settings['about_mission'] ?? 'تقديم خدمات وقائية وتشخيصية وعلاجية شاملة بأعلى معايير الجودة العالمية، وتهيئة بيئة علاجية مريحة تحترم وقت المريض وتمنحه الرعاية الآمنة والمستدامة في كل خطوة.' }}
                        </p>
                    </div>
                    <div class="pt-5 mt-5 border-t border-slate-100 text-[11px] font-bold text-[#87A96B]">
                        الرعاية الشاملة والإنسانية
                    </div>
                </div>

                <!-- القيم والأهداف -->
                <div class="p-8 bg-white border border-slate-100 rounded-3xl shadow-xs text-right flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-black mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mb-3">أهدافنا وقيمنا (Values)</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $settings['about_values'] ?? 'النزاهة الطبية المطلقة، الشفافية في خطط العلاج والأسعار، التعقيم الحاسم بنسبة 100%، والتطوير المستمر للكفاءات السريرية وأحدث الأجهزة الطبية.' }}
                        </p>
                    </div>
                    <div class="pt-5 mt-5 border-t border-slate-100 text-[11px] font-bold text-slate-800">
                        النزاهة والأمانة السريرية
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Clinic Facilities & Tech -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-3.5 py-1 rounded-full border border-[#87A96B]/30">التجهيزات والتكنولوجيا</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-3 mb-2">أحدث بنية تحتية طبية لخدمتك</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-right">
                <div class="p-6 rounded-2xl bg-[#F8FAFC] border border-slate-100">
                    <div class="text-blue-700 font-black text-xl mb-2">01</div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">أشعة ثلاثية الأبعاد 3D CBCT</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">رؤية تشريحية دقيقة للفكين والأسنان لتخطيط عمليات الزراعة والتقويم بدقة متناهية.</p>
                </div>

                <div class="p-6 rounded-2xl bg-[#F8FAFC] border border-slate-100">
                    <div class="text-blue-700 font-black text-xl mb-2">02</div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">ميكروسكوب حشو العصب</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">تكبير بصري عالي الدقة يضمن تنظيف وحشو أدق قنوات الجذور دون ألم وبنسبة نجاح قصوى.</p>
                </div>

                <div class="p-6 rounded-2xl bg-[#F8FAFC] border border-slate-100">
                    <div class="text-blue-700 font-black text-xl mb-2">03</div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">تبييض الأسنان بالليزر البارد</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">جلسة تبييض سريعة وآمنة تماماً على طبقة المينا، بدون ألم أو تحسس لاحق.</p>
                </div>

                <div class="p-6 rounded-2xl bg-[#F8FAFC] border border-slate-100">
                    <div class="text-blue-700 font-black text-xl mb-2">04</div>
                    <h4 class="text-sm font-bold text-slate-900 mb-1">غرف تعقيم بنظام Class B</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">أجهزة أوتوكلاف رقمية متطورة تضمن تعقيم وتغليف الأدوات بطريقة فردية لكل مريض.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA -->
    <section class="py-14 bg-gradient-to-r from-blue-900 to-blue-800 text-white text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h3 class="text-2xl sm:text-3xl font-black mb-4">هل ترغب في استشارة طبية دقيقة لأسنانك؟</h3>
            <p class="text-blue-100 text-xs sm:text-sm mb-6 leading-relaxed">
                يسعد طاقمنا الطبي باستقبالك والإجابة عن جميع استفساراتك ووضع خطة علاجية مخصصة تناسب احتياجاتك.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('appointments.book') }}" class="px-7 py-3 text-xs font-black text-blue-900 bg-white rounded-full shadow-lg hover:bg-blue-50 transition">
                    احجز موعدك الآن
                </a>
                <a href="{{ route('contact') }}" class="px-7 py-3 text-xs font-bold text-white bg-blue-800 border border-white/20 rounded-full hover:bg-blue-700 transition">
                    تواصل مع المركز
                </a>
            </div>
        </div>
    </section>

@endsection
