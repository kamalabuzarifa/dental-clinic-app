@extends('layouts.public')

@section('title', $seo['title'] ?? ($doctor->name . ' | ' . ($settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان')))
@section('meta_description', $seo['description'] ?? ('تعرف على السيرة المهنية والخبرات السريرية للطبيب ' . $doctor->name . ' في مركز ريادة لطب الأسنان، واحجز موعدك الطبي المباشر معه.'))
@section('meta_keywords', $seo['keywords'] ?? ($doctor->name . ', ' . ($doctor->specialty ?? 'طبيب أسنان') . ', مركز ريادة'))
@section('canonical', route('doctors.show', $doctor->id))

@section('content')

    <!-- Breadcrumb & Header -->
    <section class="bg-gradient-to-b from-blue-50/50 to-white border-b border-slate-100 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 space-x-reverse text-xs font-semibold text-slate-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-blue-700 transition">الرئيسية</a>
                <span>/</span>
                <a href="{{ route('doctors.index') }}" class="hover:text-blue-700 transition">فريق الأطباء</a>
                <span>/</span>
                <span class="text-blue-700 font-bold">{{ $doctor->name }}</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center space-x-5 space-x-reverse">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-tr from-blue-700 to-blue-600 text-white font-black text-3xl flex items-center justify-center shadow-lg shadow-blue-700/20 border-4 border-white flex-shrink-0 overflow-hidden">
                        @if($doctor->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($doctor->avatar))
                            <img src="{{ asset('storage/' . $doctor->avatar) }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover">
                        @else
                            {{ mb_substr($doctor->name, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <span class="inline-block text-[11px] font-black uppercase tracking-wider text-[#557143] bg-[#87A96B]/15 px-3 py-1 rounded-full border border-[#87A96B]/30 mb-2">
                            {{ $doctor->specialty ?? 'طبيب واستشاري معتمد' }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">
                            {{ $doctor->name }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-1 font-medium">
                            {{ $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة وتجميل الأسنان' }}
                        </p>
                    </div>
                </div>

                <div>
                    <a href="{{ route('appointments.book', ['doctor' => $doctor->id]) }}" 
                       class="inline-flex items-center px-6 py-3.5 rounded-full text-xs font-black text-white bg-blue-700 hover:bg-blue-800 shadow-md shadow-blue-700/20 transition">
                        احجز موعداً مع الطبيب الآن ←
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Details Body: 2 Columns -->
    <section class="py-14 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                <!-- Main Content Column (Col 8) -->
                <div class="lg:col-span-8 space-y-10">
                    
                    <!-- Bio & Overview -->
                    <div class="p-8 bg-white rounded-3xl border border-slate-100 shadow-xs space-y-4">
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                            نبذة عن الطبيب والخبرة السريرية
                        </h2>
                        @if($doctor->bio)
                            <p class="text-sm text-slate-700 leading-relaxed font-semibold bg-blue-50/50 p-4 rounded-2xl border border-blue-100">
                                {{ $doctor->bio }}
                            </p>
                        @endif
                        <p class="text-sm text-slate-600 leading-relaxed">
                            يعد {{ $doctor->name }} من الكفاءات الطبية البارزة في عيادات مركز ريادة، حيث يمتلك سجلاً حافلاً بالخبرات السريرية في تشخيص ومعالجة الحالات السنية المتنوعة بدقة متناهية وبأعلى معايير الأمان والراحة للمريض.
                        </p>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            يحرص الطبيب دائماً على تطبيق أحدث البروتوكولات العلاجية المعتمدة عالمياً واستخدام أجهزة التصوير ثلاثي الأبعاد والتقنيات الرقمية غير المؤلمة، لضمان حصول المريض على أفضل خطة علاجية ملائمة لحالته.
                        </p>
                    </div>

                    <!-- Clinical Specializations -->
                    <div class="p-8 bg-white rounded-3xl border border-slate-100 shadow-xs">
                        <h2 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-[#87A96B]"></span>
                            مجالات الاختصاص والاهتمام الطبي
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-slate-100">
                                <h3 class="text-xs font-bold text-slate-900 mb-1">الفحص والتشخيص الشامل</h3>
                                <p class="text-[11px] text-slate-500 leading-relaxed">تقييم دقيق لصحة الفم والأسنان مع وضع خطة علاجية مرحلية واضحة.</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-slate-100">
                                <h3 class="text-xs font-bold text-slate-900 mb-1">الحشوات التجميلية وترميم الأسنان</h3>
                                <p class="text-[11px] text-slate-500 leading-relaxed">إصلاح وتجميل الأسنان المتضررة بحشوات ضوئية نانوية مطابقة للون الطبيعي.</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-slate-100">
                                <h3 class="text-xs font-bold text-slate-900 mb-1">علاج وحشو العصب المتطور</h3>
                                <p class="text-[11px] text-slate-500 leading-relaxed">تنظيف قنوات الجذور بأحدث الأجهزة الدوارة في جلسات مريحة وسريعة.</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-slate-100">
                                <h3 class="text-xs font-bold text-slate-900 mb-1">تبييض الأسنان والعناية الوقائية</h3>
                                <p class="text-[11px] text-slate-500 leading-relaxed">إزالة التصبغات وإعادة الإشراق لابتسامتك بتقنيات الليزر الآمنة تماماً.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Services Available with this Doctor -->
                    @if(isset($services) && count($services) > 0)
                        <div class="p-8 bg-white rounded-3xl border border-slate-100 shadow-xs">
                            <h2 class="text-lg font-black text-slate-900 mb-5 flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                                الخدمات الطبية المتاحة مع {{ $doctor->name }}
                            </h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($services as $srv)
                                    <div class="p-4 rounded-2xl border border-slate-100 hover:border-blue-200 transition bg-white flex items-center justify-between">
                                        <div>
                                            <a href="{{ route('services.show', $srv->id) }}" class="text-xs font-bold text-slate-900 hover:text-blue-700 block">
                                                {{ $srv->name }}
                                            </a>
                                            <span class="text-[11px] text-slate-400">{{ $srv->duration_minutes }} دقيقة</span>
                                        </div>
                                        <a href="{{ route('appointments.book', ['doctor' => $doctor->id, 'service' => $srv->id]) }}" class="px-3 py-1 rounded-full text-[11px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-700 hover:text-white transition">
                                            احجز الآن
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Sticky Sidebar Column (Col 4) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Direct Booking Card -->
                    <div class="p-8 bg-white rounded-3xl border border-slate-100 shadow-md sticky top-28 space-y-6">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-700 font-black text-2xl flex items-center justify-center mx-auto mb-3 border-2 border-blue-100">
                                {{ mb_substr($doctor->name, 0, 1) }}
                            </div>
                            <h3 class="text-sm font-black text-slate-900">{{ $doctor->name }}</h3>
                            <p class="text-xs text-[#87A96B] font-bold mt-0.5">طبيب واستشاري معتمد</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-slate-100 space-y-2.5 text-xs text-slate-600">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">أيام التواجد:</span>
                                <span>السبت - الخميس</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">ساعات العمل:</span>
                                <span>09:00 ص - 09:00 م</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">نظام المواعيد:</span>
                                <span class="text-emerald-700 font-bold">مؤكد ومجدول مسبقاً</span>
                            </div>
                        </div>

                        <a href="{{ route('appointments.book', ['doctor' => $doctor->id]) }}" 
                           class="w-full inline-flex items-center justify-center py-4 rounded-full text-sm font-black text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-lg shadow-blue-700/25 transition">
                            احجز موعداً مع الطبيب الآن ←
                        </a>

                        <div class="text-center pt-2">
                            <span class="text-[11px] text-slate-400 block mb-1">تحتاج مساعدة أو استفساراً؟</span>
                            <a href="tel:{{ $settings['clinic_phone'] ?? '0596482371' }}" class="text-xs font-bold text-blue-700 hover:underline">
                                اتصل بالاستقبال: {{ $settings['clinic_phone'] ?? '0596482371' }}
                            </a>
                        </div>
                    </div>

                    <!-- Other Doctors in Clinic -->
                    @if(isset($otherDoctors) && count($otherDoctors) > 0)
                        <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-xs">
                            <h3 class="text-xs font-black text-slate-900 mb-4 pb-2 border-b border-slate-100">أطباء آخرون بالمركز:</h3>
                            <ul class="space-y-3 text-xs">
                                @foreach($otherDoctors as $oth)
                                    <li>
                                        <a href="{{ route('doctors.show', $oth->id) }}" class="flex items-center justify-between text-slate-700 hover:text-blue-700 transition">
                                            <span class="font-bold">{{ $oth->name }}</span>
                                            <span class="text-blue-700 font-bold text-[11px]">عرض الملف ←</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </section>

@endsection
