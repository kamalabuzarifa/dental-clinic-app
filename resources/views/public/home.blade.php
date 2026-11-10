@extends('layouts.public')

@section('title', $seo['title'] ?? (($settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان') . ' | الرعاية السنية المتقدمة والابتسامة المشرقة'))
@section('meta_description', $seo['description'] ?? 'مركز ريادة لطب وجراحة وتجميل الأسنان - تقنيات رقمية ثلاثية الأبعاد، بيئة علاجية مريحة بدون ألم، ونظام حجز إلكتروني مؤكد بالدقيقة.')
@section('meta_keywords', $seo['keywords'] ?? 'طب أسنان, زراعة أسنان, تقويم أسنان, تبييض أسنان, غزة, عيادة أسنان')
@section('canonical', route('home'))

@section('content')

    @foreach($homeSections as $section)
        @switch($section->section_key)

            {{-- 1. HERO SECTION --}}
            @case('hero')
                <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-white via-blue-50/25 to-[#F8FAFC]">
                    <div class="absolute top-1/4 right-0 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute bottom-10 left-10 w-96 h-96 bg-[#87A96B]/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                            <!-- Text Column -->
                            <div class="lg:col-span-7 text-right">
                                <!-- Medical Trust Pill -->
                                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50/90 border border-blue-200/70 text-slate-800 text-xs font-bold mb-6 shadow-xs">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#87A96B] animate-pulse"></span>
                                    <span class="text-blue-800">{{ $section->badge ?: 'رعاية سنية متقدمة ومريحة' }}</span>
                                    <span class="text-slate-300">|</span>
                                    <span class="text-slate-600 font-medium">نظام حجز إلكتروني مؤكد بالدقيقة</span>
                                </div>

                                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 leading-[1.28] mb-6 tracking-tight">
                                    {{ $section->title }}
                                </h1>

                                <p class="text-sm sm:text-base lg:text-lg text-slate-600 mb-8 max-w-2xl leading-relaxed font-normal">
                                    {{ $section->subtitle ?: ($settings['clinic_description'] ?? 'نقدم بيئة طبية رحبة ومريحة مجهزة بأحدث أجهزة التصوير ثلاثي الأبعاد والتقنيات الرقمية غير المؤلمة.') }}
                                </p>

                                <!-- CTA Buttons -->
                                <div class="flex flex-wrap items-center gap-4">
                                    @if(($settings['maintenance_mode'] ?? '0') == '1')
                                        <div class="inline-flex items-center gap-2 px-6 py-3.5 text-xs font-bold bg-amber-500/10 text-amber-800 border border-amber-300 rounded-full">
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>نظام الحجز الإلكتروني في وضع الصيانة والتحديث مؤقتاً</span>
                                        </div>
                                    @else
                                        <a href="{{ route('appointments.book') }}" 
                                           class="inline-flex items-center justify-center px-8 py-4 text-sm font-black text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full shadow-lg shadow-blue-700/25 hover:from-blue-700 hover:to-blue-800 hover:-translate-y-1">
                                            احجز موعدك الطبي الآن
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    @endif

                                    <a href="{{ route('services.index') }}" 
                                       class="inline-flex items-center justify-center px-7 py-4 text-sm font-bold text-blue-700 transition-all duration-300 bg-white border-2 border-blue-600/20 rounded-full shadow-xs hover:border-blue-600 hover:bg-blue-50/50 hover:-translate-y-1">
                                        استكشف الخدمات الطبية
                                    </a>
                                </div>

                                <!-- Trust Indicators -->
                                <div class="mt-12 pt-8 border-t border-slate-200/80 grid grid-cols-3 gap-6 text-right">
                                    <div>
                                        <div class="text-2xl sm:text-3xl font-black text-blue-700">99.8%</div>
                                        <div class="text-[11px] sm:text-xs text-slate-500 font-medium mt-1">نسبة رضا مرضانا</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl sm:text-3xl font-black text-[#87A96B]">0 دقيقة</div>
                                        <div class="text-[11px] sm:text-xs text-slate-500 font-medium mt-1">انعدام وقت الانتظار</div>
                                    </div>
                                    <div>
                                        <div class="text-2xl sm:text-3xl font-black text-slate-900">+{{ max(100, $stats['completed_appointments'] ?? 0) }}</div>
                                        <div class="text-[11px] sm:text-xs text-slate-500 font-medium mt-1">مواعيد منجزة بنجاح</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Clinic Visual Card -->
                            <div class="lg:col-span-5">
                                <div class="relative">
                                    <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-blue-600/15 to-[#87A96B]/20 blur-xl"></div>

                                    <div class="relative bg-white rounded-3xl p-7 border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.08)]">
                                        <div class="relative h-48 w-full rounded-2xl overflow-hidden mb-6 shadow-xs border border-slate-100">
                                            @if(!empty($settings['clinic_hero_image']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['clinic_hero_image']))
                                                <img src="{{ asset('storage/' . $settings['clinic_hero_image']) }}" 
                                                     alt="{{ $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان' }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=800&q=80" 
                                                     alt="عيادات مركز ريادة لطب وجراحة الأسنان" 
                                                     class="w-full h-full object-cover">
                                            @endif
                                            <div class="absolute inset-0 bg-gradient-to-t from-blue-950/80 via-transparent to-transparent flex items-end p-4">
                                                <div class="text-white">
                                                    <span class="inline-block text-[10px] font-bold bg-[#87A96B] text-white px-2.5 py-0.5 rounded-full mb-1">بيئة طبية آمنة</span>
                                                    <h2 class="text-xs font-black">{{ $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان' }}</h2>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-slate-100 flex items-center justify-between">
                                                <div class="flex items-center space-x-3 space-x-reverse">
                                                    <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-black">1</div>
                                                    <span class="text-xs font-bold text-slate-800">أجهزة تصوير رقمية ثلاثية الأبعاد 3D</span>
                                                </div>
                                                <span class="text-[10px] font-bold text-blue-700">تشخيص دقيق</span>
                                            </div>

                                            <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-slate-100 flex items-center justify-between">
                                                <div class="flex items-center space-x-3 space-x-reverse">
                                                    <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-black">2</div>
                                                    <span class="text-xs font-bold text-slate-800">استشاريون معتمدون في التقويم والزراعة</span>
                                                </div>
                                                <span class="text-[10px] font-bold text-blue-700">خبرة متخصصة</span>
                                            </div>

                                            <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-slate-100 flex items-center justify-between">
                                                <div class="flex items-center space-x-3 space-x-reverse">
                                                    <div class="w-7 h-7 rounded-full bg-[#87A96B]/20 text-[#557143] flex items-center justify-center text-xs font-black">3</div>
                                                    <span class="text-xs font-bold text-slate-800">تعقيم متكامل بأحدث المعايير الدولية</span>
                                                </div>
                                                <span class="text-[10px] font-bold text-[#87A96B]">أمان تام 100%</span>
                                            </div>
                                        </div>

                                        <div class="mt-5 p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-center">
                                            <p class="text-xs text-blue-950 font-bold mb-0.5">جدولة دقيقة تمنع تضارب المواعيد</p>
                                            <p class="text-[11px] text-blue-700/80">{{ $workingHoursText }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                @break

            {{-- 2. QUICK ABOUT HIGHLIGHT --}}
            @case('about_highlight')
                <section class="py-16 bg-white border-y border-slate-100">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                            <div class="lg:col-span-8">
                                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-3.5 py-1 rounded-full border border-[#87A96B]/30 inline-block mb-3">
                                    {{ $section->badge ?: 'عن مركز ريادة' }}
                                </span>
                                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mb-4">{{ $section->title }}</h2>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-3xl">
                                    {{ $settings['about_story_p1'] ?? $section->subtitle }}
                                </p>
                            </div>
                            <div class="lg:col-span-4 flex lg:justify-end">
                                <a href="{{ route('about') }}" 
                                   class="inline-flex items-center px-6 py-3.5 rounded-full text-xs font-black text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition shadow-xs">
                                    تعرف أكثر على رؤيتنا وفريقنا ←
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
                @break

            {{-- 3. FEATURED SERVICES --}}
            @case('services')
                <section class="py-20 bg-[#F8FAFC]">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-14">
                            <div>
                                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-3.5 py-1 rounded-full border border-[#87A96B]/30 inline-block mb-2">
                                    {{ $section->badge ?: 'الخدمات الطبية والتجميلية' }}
                                </span>
                                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $section->title }}</h2>
                                @if($section->subtitle)
                                    <p class="text-xs text-slate-500 mt-1">{{ $section->subtitle }}</p>
                                @endif
                            </div>
                            <a href="{{ route('services.index') }}" class="text-xs font-black text-blue-700 hover:text-blue-800 flex items-center gap-1.5 transition">
                                عرض جميع الخدمات الطبية ({{ $totalServicesCount ?? count($services ?? []) }}) ←
                            </a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            @forelse($services ?? [] as $service)
                                <div class="relative p-7 bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] hover:-translate-y-1.5 transition duration-300 flex flex-col justify-between group">
                                    <div class="absolute top-0 right-0 left-0 h-1 bg-gradient-to-r from-transparent via-blue-600 to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>

                                    <div>
                                        <div class="flex items-center justify-between mb-5">
                                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-lg group-hover:bg-blue-700 group-hover:text-white transition duration-300 shadow-xs">
                                                <span>{{ $service->icon ?: '🦷' }}</span>
                                            </div>
                                            <span class="text-xs font-bold text-slate-500 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                                                {{ $service->duration_minutes }} دقيقة
                                            </span>
                                        </div>

                                        <h3 class="text-base font-black text-slate-900 mb-2 group-hover:text-blue-700 transition">
                                            <a href="{{ route('services.show', $service->id) }}">{{ $service->name }}</a>
                                        </h3>
                                        <p class="text-xs text-slate-500 mb-6 leading-relaxed line-clamp-3">
                                            {{ $service->description ?? 'جلسة علاجية متكاملة بأحدث الأجهزة والخامات المعتمدة.' }}
                                        </p>
                                    </div>

                                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                        <div>
                                            <span class="text-[10px] text-slate-400 block">السعر التقديري</span>
                                            <span class="text-lg font-black text-slate-900">{{ number_format($service->price, 0) }} <span class="text-xs font-bold text-blue-700">₪</span></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('services.show', $service->id) }}" class="px-3 py-1.5 rounded-full text-xs font-bold text-slate-600 hover:text-blue-700 hover:bg-slate-50 transition">
                                                تفاصيل
                                            </a>
                                            <a href="{{ route('appointments.book', ['service' => $service->id]) }}" class="px-4 py-1.5 rounded-full text-xs font-black bg-blue-50 text-blue-700 hover:bg-blue-700 hover:text-white transition">
                                                احجز الآن ←
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-3 text-center py-12 text-slate-400 text-sm">
                                    جاري تحديث قائمة الخدمات الطبية...
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
                @break

            {{-- 4. WHY US / FEATURES --}}
            @case('features')
                <section class="py-20 bg-white border-b border-slate-100">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center max-w-3xl mx-auto mb-14">
                            <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-4 py-1.5 rounded-full border border-[#87A96B]/30">
                                {{ $section->badge ?: 'لماذا مركز ريادة؟' }}
                            </span>
                            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-3 mb-3">{{ $section->title }}</h2>
                            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">{{ $section->subtitle ?: 'نحرص على أن تكون زيارتك للعيادة تجربة مريحة وسلسة تمنحك الثقة والاطمئنان في كل مرحلة.' }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="p-7 bg-[#F8FAFC] border border-slate-100 rounded-3xl text-right">
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg font-bold mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="font-bold text-slate-900 mb-2 text-sm">دقة المواعيد التامة</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">نظامنا الرقمي يمنع تضارب المواعيد لنضمن بدء جلستك في موعدها المحدد دون إهدار لوقتك.</p>
                            </div>

                            <div class="p-7 bg-[#F8FAFC] border border-slate-100 rounded-3xl text-right">
                                <div class="w-12 h-12 rounded-2xl bg-[#87A96B]/20 text-[#557143] flex items-center justify-center text-lg font-bold mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <h3 class="font-bold text-slate-900 mb-2 text-sm">راحة وعناية فائقة</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">بيئة علاجية هادئة واستقبال دافئ يعين المرضى من كافة الأعمار على الشعور بالراحة التامة.</p>
                            </div>

                            <div class="p-7 bg-[#F8FAFC] border border-slate-100 rounded-3xl text-right">
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg font-bold mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                </div>
                                <h3 class="font-bold text-slate-900 mb-2 text-sm">خامات علاجية معتمدة</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">نعتمد على أجود خامات طب وتجميل الأسنان المتوافقة حيوياً لتحقيق متانة تدوم طويلاً.</p>
                            </div>

                            <div class="p-7 bg-[#F8FAFC] border border-slate-100 rounded-3xl text-right">
                                <div class="w-12 h-12 rounded-2xl bg-[#87A96B]/20 text-[#557143] flex items-center justify-center text-lg font-bold mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <h3 class="font-bold text-slate-900 mb-2 text-sm">كادر طبي مؤهل</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">أطباء واستشاريون متمرسون يحرصون على الاستماع لاحتياجاتك وشرح خطة العلاج بوضوح.</p>
                            </div>
                        </div>
                    </div>
                </section>
                @break

            {{-- 5. DOCTORS SECTION --}}
            @case('doctors')
                <section class="py-20 bg-[#F8FAFC]">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-14">
                            <div>
                                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-3.5 py-1 rounded-full border border-[#87A96B]/30 inline-block mb-2">
                                    {{ $section->badge ?: 'الكادر الطبي' }}
                                </span>
                                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $section->title }}</h2>
                                @if($section->subtitle)
                                    <p class="text-xs text-slate-500 mt-1">{{ $section->subtitle }}</p>
                                @endif
                            </div>
                            <a href="{{ route('doctors.index') }}" class="text-xs font-black text-blue-700 hover:text-blue-800 flex items-center gap-1.5 transition">
                                عرض جميع الأطباء ({{ $totalDentistsCount ?? count($dentists ?? []) }}) ←
                            </a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 {{ count($dentists ?? []) >= 3 ? 'lg:grid-cols-3' : 'max-w-4xl mx-auto' }} gap-8">
                            @forelse($dentists ?? [] as $dentist)
                                <div class="p-8 bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] hover:-translate-y-1.5 transition duration-300 text-center flex flex-col justify-between group">
                                    <div>
                                        <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-blue-700 to-blue-600 text-white font-black text-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-700/20 border-4 border-blue-50 overflow-hidden">
                                            @if($dentist->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($dentist->avatar))
                                                <img src="{{ asset('storage/' . $dentist->avatar) }}" alt="{{ $dentist->name }}" class="w-full h-full object-cover">
                                            @else
                                                {{ mb_substr($dentist->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <h3 class="text-base font-black text-slate-900 mb-1 group-hover:text-blue-700 transition">
                                            <a href="{{ route('doctors.show', $dentist->id) }}">{{ $dentist->name }}</a>
                                        </h3>
                                        <p class="text-xs text-[#87A96B] font-bold mb-3">{{ $dentist->specialty ?? 'طبيب واستشاري معتمد في طب الأسنان' }}</p>
                                        
                                        @if($dentist->bio)
                                            <p class="text-[11px] text-slate-500 mb-4 line-clamp-2 px-2">{{ $dentist->bio }}</p>
                                        @endif

                                        <div class="flex items-center justify-center space-x-1 space-x-reverse text-amber-400 text-xs mb-6">
                                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                            <span class="text-slate-400 text-[11px] mr-1">(تقييم معتمد)</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 pt-4 border-t border-slate-100">
                                        <a href="{{ route('doctors.show', $dentist->id) }}" class="flex-1 py-2 rounded-full text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition">
                                            الملف التعريفي
                                        </a>
                                        <a href="{{ route('appointments.book', ['doctor' => $dentist->id]) }}" class="flex-1 py-2 rounded-full text-xs font-black text-white bg-blue-700 hover:bg-blue-800 transition shadow-xs">
                                            احجز موعداً
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-3 text-center py-10 text-slate-400 text-xs">
                                    جاري تحديث بيانات طاقم الأطباء...
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
                @break

            {{-- 6. STEPS SECTION --}}
            @case('steps')
                <section class="py-20 bg-white border-t border-slate-100">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center max-w-3xl mx-auto mb-14">
                            <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-4 py-1.5 rounded-full border border-[#87A96B]/30">
                                {{ $section->badge ?: 'السهولة والسرعة' }}
                            </span>
                            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-3 mb-3">{{ $section->title }}</h2>
                            @if($section->subtitle)
                                <p class="text-xs text-slate-500">{{ $section->subtitle }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                            <div class="p-8 bg-[#F8FAFC] border border-slate-100 rounded-3xl">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-700 to-blue-600 text-white font-black text-base flex items-center justify-center mx-auto mb-5 shadow-md shadow-blue-700/20">01</div>
                                <h3 class="text-sm font-black text-slate-900 mb-2">اختيار الخدمة والطبيب</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">حدد الإجراء المطلوب مثل الفحص العام، التنظيف، أو التقويم مع اختيار طبيبك المفضل.</p>
                            </div>

                            <div class="p-8 bg-[#F8FAFC] border border-slate-100 rounded-3xl">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-700 to-blue-600 text-white font-black text-base flex items-center justify-center mx-auto mb-5 shadow-md shadow-blue-700/20">02</div>
                                <h3 class="text-sm font-black text-slate-900 mb-2">تحديد الوقت المناسب</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">اختر التاريخ والوقت المتاح بدقة وسلاسة بما يتوافق مع جدولك اليومي وساعات دوام العيادة.</p>
                            </div>

                            <div class="p-8 bg-[#F8FAFC] border border-slate-100 rounded-3xl">
                                <div class="w-12 h-12 rounded-full bg-[#87A96B] text-white font-black text-base flex items-center justify-center mx-auto mb-5 shadow-md shadow-[#87A96B]/20">03</div>
                                <h3 class="text-sm font-black text-slate-900 mb-2">تأكيد فوري واستقبال مريح</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">تأكيد مباشر للموعد في نظام العيادة لتبدأ جلستك الطبية في موعدها دون انتظار.</p>
                            </div>
                        </div>
                    </div>
                </section>
                @break

            {{-- 7. REAL PATIENT TESTIMONIALS --}}
            @case('testimonials')
                @if($testimonials->isNotEmpty())
                    <section class="py-20 bg-[#F8FAFC] border-t border-slate-100">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center max-w-3xl mx-auto mb-14">
                                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-4 py-1.5 rounded-full border border-[#87A96B]/30">
                                    {{ $section->badge ?: 'تجارب حقيقية' }}
                                </span>
                                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-3 mb-3">{{ $section->title }}</h2>
                                <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">{{ $section->subtitle ?: 'نفخر بثقة مرضانا ونسعى دائماً لتقديم تجربة علاجية وسريرية تترك أثراً طيباً وابتسامة مشرقة.' }}</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($testimonials as $review)
                                    <div class="p-7 bg-white rounded-3xl border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-10 h-10 rounded-full bg-blue-50 text-[#1E40AF] flex items-center justify-center font-black text-sm">
                                                        {{ mb_substr($review->patient_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h4 class="text-xs font-black text-slate-900">{{ $review->patient_name }}</h4>
                                                        @if($review->treatment_received)
                                                            <span class="text-[10px] text-[#87A96B] font-bold block">{{ $review->treatment_received }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-amber-400 text-xs tracking-tight">
                                                    {{ str_repeat('★', $review->rating) }}
                                                </div>
                                            </div>
                                            <p class="text-xs text-slate-600 leading-relaxed font-normal">
                                                "{{ $review->review }}"
                                            </p>
                                        </div>
                                        <div class="pt-4 mt-4 border-t border-slate-50 flex items-center justify-between text-[10px] text-slate-400">
                                            <span>تجربة مريض موثقة ✓</span>
                                            <span>{{ $review->created_at ? $review->created_at->diffForHumans() : 'مؤخراً' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
                @break

            {{-- 8. FAQS ACCORDION --}}
            @case('faqs')
                @if($faqs->isNotEmpty())
                    <section class="py-20 bg-white border-t border-slate-100" x-data="{ activeFaq: null }">
                        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center max-w-2xl mx-auto mb-14">
                                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-4 py-1.5 rounded-full border border-[#87A96B]/30">
                                    {{ $section->badge ?: 'إجابات واضحة' }}
                                </span>
                                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-3 mb-3">{{ $section->title }}</h2>
                                <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">{{ $section->subtitle ?: 'أبرز الأسئلة المتكررة حول الخدمات، الحجوزات، والتقنيات الطبية في عيادتنا.' }}</p>
                            </div>

                            <div class="space-y-3.5">
                                @foreach($faqs as $faq)
                                    <div class="rounded-2xl border border-slate-200/80 overflow-hidden bg-slate-50/50 transition">
                                        <button type="button" 
                                                @click="activeFaq = (activeFaq === {{ $faq->id }} ? null : {{ $faq->id }})"
                                                class="w-full p-4 sm:p-5 text-right flex items-center justify-between gap-3 font-bold text-xs sm:text-sm text-slate-900 hover:text-[#1E40AF] transition">
                                            <div class="flex items-center gap-3">
                                                <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#1E40AF] flex items-center justify-center font-black text-xs flex-shrink-0">؟</span>
                                                <span>{{ $faq->question }}</span>
                                            </div>
                                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0"
                                                 :class="{ 'rotate-180 text-[#1E40AF]': activeFaq === {{ $faq->id }} }"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <div x-show="activeFaq === {{ $faq->id }}" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 -translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="p-4 sm:p-5 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-white"
                                             style="display: none;">
                                            {{ $faq->answer }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
                @break

            {{-- 9. MASTER CTA BANNER --}}
            @case('cta_banner')
                <section class="py-16 bg-[#F8FAFC]">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 p-8 sm:p-14 text-center text-white shadow-2xl shadow-blue-900/20 border border-blue-600/30">
                            <div class="absolute -top-24 -right-24 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                            <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-[#87A96B]/20 rounded-full blur-3xl"></div>

                            <div class="relative z-10 max-w-2xl mx-auto">
                                <span class="text-xs font-black uppercase tracking-widest bg-white/15 px-4 py-1 rounded-full text-blue-100 mb-4 inline-block">
                                    {{ $section->badge ?: 'صحة أسنانك تبدأ بخطوة' }}
                                </span>
                                <h2 class="text-2xl sm:text-4xl font-black mb-5 leading-tight">
                                    {{ $section->title }}
                                </h2>
                                <p class="text-blue-100 text-xs sm:text-sm mb-8 leading-relaxed font-normal">
                                    {{ $section->subtitle ?: 'انضم إلى آلاف المرضى الذين وجدوا الراحة والاطمئنان في عيادتنا. احجز موعدك الآن عبر نظامنا الذكي بكل يسر.' }}
                                </p>
                                <div class="flex flex-wrap items-center justify-center gap-4">
                                    <a href="{{ route('appointments.book') }}" 
                                       class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-black text-blue-900 transition-all duration-300 bg-white rounded-full shadow-lg hover:bg-blue-50 hover:-translate-y-1">
                                        احجز موعدك الطبي الآن
                                    </a>
                                    <a href="{{ route('contact') }}" 
                                       class="inline-flex items-center justify-center px-7 py-3.5 text-sm font-bold text-white transition-all duration-300 bg-blue-800/80 border border-white/20 rounded-full hover:bg-blue-800 hover:-translate-y-1">
                                        تواصل مع العيادة
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                @break

        @endswitch
    @endforeach

@endsection
