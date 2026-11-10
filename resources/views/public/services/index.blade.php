@extends('layouts.public')

@section('title', $seo['title'] ?? ('الخدمات الطبية والتجميلية | ' . ($settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان')))
@section('meta_description', $seo['description'] ?? 'استكشف باقة الخدمات السريرية والتجميلية المتقدمة في مركز ريادة لطب الأسنان: الكشف العام، تبييض الليزر، حشو العصب، تقويم وزراعة الأسنان، بأسعار واضحة وجودة معتمدة.')
@section('meta_keywords', $seo['keywords'] ?? 'خدمات طب الأسنان, حشو تجميلي, علاج عصب, تبييض ليزر')
@section('canonical', route('services.index'))

@section('content')

    <!-- Breadcrumb & Header -->
    <section class="bg-gradient-to-b from-blue-50/50 to-white border-b border-slate-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 space-x-reverse text-xs font-semibold text-slate-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-blue-700 transition">الرئيسية</a>
                <span>/</span>
                <span class="text-blue-700 font-bold">الخدمات الطبية</span>
            </nav>

            <div class="max-w-3xl">
                <span class="inline-block text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-3.5 py-1 rounded-full border border-[#87A96B]/30 mb-3">
                    الرعاية العلاجية والتجميلية
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 leading-tight mb-4">
                    خدمات طبية وتجميلية متكاملة لصحة وجمال ابتسامتك
                </h1>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                    نقدم رعاية سنية شاملة تجمع بين التشخيص الرقمي الدقيق، أحدث المواد السويسرية والألمانية المعتمدة، وخبرات الأطباء المتخصصين لنتائج علاجية وتجميلية تدوم طويلاً.
                </p>
            </div>
        </div>
    </section>

    <!-- Services Grid Section -->
    <section class="py-16 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($services as $service)
                    <div class="relative p-8 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] hover:-translate-y-1.5 transition duration-300 group flex flex-col justify-between">
                        <!-- Top Accent Line -->
                        <div class="absolute top-0 right-0 left-0 h-1 bg-gradient-to-r from-transparent via-blue-600 to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>

                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-lg group-hover:bg-blue-700 group-hover:text-white transition duration-300 shadow-xs">
                                    <span>{{ $service->icon ?: '🦷' }}</span>
                                </div>
                                <span class="text-xs font-bold text-slate-500 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                                    {{ $service->duration_minutes }} دقيقة
                                </span>
                            </div>

                            <h2 class="text-lg font-black text-slate-900 mb-2 group-hover:text-blue-700 transition">
                                <a href="{{ route('services.show', $service->id) }}">{{ $service->name }}</a>
                            </h2>

                            <p class="text-xs text-slate-500 mb-8 leading-relaxed line-clamp-3">
                                {{ $service->description ?? 'جلسة علاجية متكاملة بأحدث الأجهزة والخامات الطبية المعتمدة لراحة قصوى ونتائج تدوم.' }}
                            </p>
                        </div>

                        <div class="pt-5 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 block">السعر التقديري</span>
                                <span class="text-xl font-black text-slate-900">{{ number_format($service->price, 0) }} <span class="text-xs font-bold text-blue-700">ر.س</span></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('services.show', $service->id) }}" 
                                   class="px-3.5 py-2 rounded-full text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                                    تفاصيل
                                </a>
                                <a href="{{ route('appointments.book', ['service' => $service->id]) }}" 
                                   class="px-4 py-2 rounded-full text-xs font-black bg-blue-50 text-blue-700 hover:bg-blue-700 hover:text-white transition shadow-xs">
                                    احجز الآن ←
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-16 bg-white rounded-3xl border border-slate-100 p-8">
                        <div class="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-4 text-2xl">🩺</div>
                        <h3 class="text-base font-bold text-slate-700 mb-2">جاري تحديث قائمة الخدمات</h3>
                        <p class="text-xs text-slate-400">يرجى التواصل معنا مباشرة للاستفسار عن الخدمات المتاحة.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    <!-- Medical Standards Box -->
    <section class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="p-8 sm:p-12 rounded-3xl bg-blue-50/60 border border-blue-100 flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <h3 class="text-xl font-black text-slate-900 mb-2">جميع خدماتنا مشمولة بضمان الجودة الطبية والتعقيم الصارم</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-2xl">
                        نلتزم بأعلى معايير مكافحة العدوى والتعقيم الفردي واستخدام أجود الخامات المتوافقة حيوياً والمعتمدة من الهيئات الصحية العالمية.
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('appointments.book') }}" class="inline-flex items-center px-7 py-3.5 text-xs font-black text-white bg-blue-700 hover:bg-blue-800 rounded-full shadow-md transition">
                        احجز استشارتك الطبية الآن
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQs Section -->
    <section class="py-16 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-3.5 py-1 rounded-full border border-[#87A96B]/30 inline-block mb-2">الأسئلة الشائعة</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">كل ما تود معرفته عن خدماتنا السنية</h2>
            </div>

            <div class="space-y-4" x-data="{ active: null }">
                <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-xs">
                    <button @click="active = active === 1 ? null : 1" class="w-full flex items-center justify-between text-right font-bold text-sm text-slate-900">
                        <span>هل الإجراءات العلاجية في مركز ريادة مؤلمة؟</span>
                        <span x-text="active === 1 ? '−' : '+'" class="text-blue-700 font-black text-lg"></span>
                    </button>
                    <div x-show="active === 1" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3" style="display: none;">
                        نستخدم أحدث تقنيات التخدير الموضعي المحوسب بدون ألم، بالإضافة إلى أجهزة الليزر الحديثة التي تقلل الإحساس بالألم إلى الصفر تقريباً وتمنح المريض راحة تامة طوال الجلسة.
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-xs">
                    <button @click="active = active === 2 ? null : 2" class="w-full flex items-center justify-between text-right font-bold text-sm text-slate-900">
                        <span>كم تستغرق جلسة تبييض الأسنان بالليزر؟</span>
                        <span x-text="active === 2 ? '−' : '+'" class="text-blue-700 font-black text-lg"></span>
                    </button>
                    <div x-show="active === 2" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3" style="display: none;">
                        تستغرق جلسة التبييض قرابة 45 إلى 60 دقيقة في جلسة واحدة فقط، وتمنحك ابتسامة أكثر إشراقاً وبياضاً بدرجات متعددة مع حماية كاملة لطبقة المينا.
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-white border border-slate-100 shadow-xs">
                    <button @click="active = active === 3 ? null : 3" class="w-full flex items-center justify-between text-right font-bold text-sm text-slate-900">
                        <span>هل يمكنني الحجز المسبق واختيار طبيبي المفضل؟</span>
                        <span x-text="active === 3 ? '−' : '+'" class="text-blue-700 font-black text-lg"></span>
                    </button>
                    <div x-show="active === 3" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3" style="display: none;">
                        نعم بكل تأكيد، نظام الحجز الإلكتروني يتيح لك اختيار الخدمة والطبيب المعالج والتاريخ والوقت المناسب لك بدقة تامة وبدون أي انتظار.
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
