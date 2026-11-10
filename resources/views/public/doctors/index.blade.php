@extends('layouts.public')

@section('title', $seo['title'] ?? ('فريق الأطباء والاستشاريين | ' . ($settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان')))
@section('meta_description', $seo['description'] ?? 'تعرف على الكادر الطبي المتميز في مركز ريادة لطب وجراحة وتجميل الأسنان: نخبة من الأطباء والاستشاريين ذوي الخبرة السريرية الواسعة في التقويم، الزراعة، وتجميل الأسنان.')
@section('meta_keywords', $seo['keywords'] ?? 'أطباء أسنان, استشاري تقويم, جراحة وجه وفكين, دكتور أسنان غزة')
@section('canonical', route('doctors.index'))

@section('content')

    <!-- Breadcrumb & Header -->
    <section class="bg-gradient-to-b from-blue-50/50 to-white border-b border-slate-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 space-x-reverse text-xs font-semibold text-slate-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-blue-700 transition">الرئيسية</a>
                <span>/</span>
                <span class="text-blue-700 font-bold">فريق الأطباء</span>
            </nav>

            <div class="max-w-3xl">
                <span class="inline-block text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-3.5 py-1 rounded-full border border-[#87A96B]/30 mb-3">
                    الكفاءات السريرية
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 leading-tight mb-4">
                    أطباء واستشاريون متمرسون لخدمتك وعائلتك
                </h1>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                    يضم مركز ريادة نخبة من أطباء الأسنان الحاصلين على أعلى الشهادات التخصصية، والذين يجمعون بين المعرفة الأكاديمية العميقة والمهارة السريرية الدقيقة لتوفير أفضل تجربة علاجية ممكنة.
                </p>
            </div>
        </div>
    </section>

    <!-- Doctors Grid Section -->
    <section class="py-16 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($doctors as $doctor)
                    <div class="p-8 bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] hover:-translate-y-1.5 transition duration-300 text-center flex flex-col justify-between group">
                        
                        <div>
                            <!-- Avatar -->
                            <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-blue-700 to-blue-600 text-white font-black text-3xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-700/20 border-4 border-blue-50 group-hover:scale-105 transition overflow-hidden">
                                @if($doctor->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($doctor->avatar))
                                    <img src="{{ asset('storage/' . $doctor->avatar) }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ mb_substr($doctor->name, 0, 1) }}
                                @endif
                            </div>

                            <h2 class="text-lg font-black text-slate-900 mb-1 group-hover:text-blue-700 transition">
                                <a href="{{ route('doctors.show', $doctor->id) }}">{{ $doctor->name }}</a>
                            </h2>

                            <p class="text-xs text-[#87A96B] font-bold mb-3">{{ $doctor->specialty ?? 'طبيب واستشاري معتمد في طب وجراحة الأسنان' }}</p>

                            <div class="flex items-center justify-center space-x-1 space-x-reverse text-amber-400 text-xs mb-6">
                                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                <span class="text-slate-400 text-[11px] mr-1">(تقييم معتمد)</span>
                            </div>

                            <p class="text-xs text-slate-500 mb-6 leading-relaxed line-clamp-2">
                                {{ $doctor->bio ?? 'خبرة سريرية متقدمة في التشخيص الدقيق، تركيبات الأسنان، وعلاجات الجذور بأحدث المعايير الطبية.' }}
                            </p>
                        </div>

                        <div class="pt-5 border-t border-slate-100 flex items-center gap-2">
                            <a href="{{ route('doctors.show', $doctor->id) }}" 
                               class="flex-1 py-2.5 rounded-full text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition">
                                الملف التعريفي
                            </a>
                            <a href="{{ route('appointments.book', ['doctor' => $doctor->id]) }}" 
                               class="flex-1 py-2.5 rounded-full text-xs font-black text-white bg-blue-700 hover:bg-blue-800 transition shadow-xs">
                                احجز موعداً ←
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-16 text-slate-400 font-bold">
                        جاري تحديث كادر الأطباء والاستشاريين...
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    <!-- Standards Section -->
    <section class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-right">
                <div class="p-6 rounded-2xl bg-blue-50/50 border border-blue-100">
                    <h3 class="text-sm font-black text-slate-900 mb-2">اعتماد سريري وأكاديمي</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">جميع أطبائنا مرخصون ومعتمدون رسمياً وحاصلون على تدريبات متقدمة في أحدث علوم طب الأسنان.</p>
                </div>
                <div class="p-6 rounded-2xl bg-[#87A96B]/10 border border-[#87A96B]/30">
                    <h3 class="text-sm font-black text-slate-900 mb-2">التواصل الشفاف والإنساني</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">نحرص على الاستماع الكامل لشكوى المريض وشرح البدائل العلاجية بوضوح تام قبل البدء.</p>
                </div>
                <div class="p-6 rounded-2xl bg-blue-50/50 border border-blue-100">
                    <h3 class="text-sm font-black text-slate-900 mb-2">التعليم والتدريب المستمر</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">مواكبة دورية لأحدث المؤتمرات الطبية وورش العمل الدولية لتطبيق أحدث بروتوكولات العلاج.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA -->
    <section class="py-14 bg-gradient-to-r from-blue-900 to-blue-800 text-white text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h3 class="text-2xl font-black mb-3">احجز استشارتك مع الطبيب المناسب لجدولك اليومي</h3>
            <p class="text-blue-100 text-xs sm:text-sm mb-6 leading-relaxed">
                حدد الطبيب والوقت المتاح عبر نظام الحجز الإلكتروني المؤكد فورياً.
            </p>
            <a href="{{ route('appointments.book') }}" class="px-8 py-3.5 text-xs font-black text-blue-900 bg-white rounded-full shadow-lg hover:bg-blue-50 transition">
                الانتقال لصفحة الحجز الإلكتروني
            </a>
        </div>
    </section>

@endsection
