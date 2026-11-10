@extends('layouts.public')

@section('title', $service->name . ' | ' . ($settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان'))
@section('meta_description', 'تعرف على تفاصيل خدمة ' . $service->name . ' في مركز ريادة لطب الأسنان، خطوات العلاج، الأسعار المعتمدة، ومدة الجلسة مع إمكانية الحجز المباشر المؤكد.')
@section('canonical', route('services.show', $service->id))

@section('content')

    <!-- Breadcrumb & Header -->
    <section class="bg-gradient-to-b from-blue-50/50 to-white border-b border-slate-100 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 space-x-reverse text-xs font-semibold text-slate-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-blue-700 transition">الرئيسية</a>
                <span>/</span>
                <a href="{{ route('services.index') }}" class="hover:text-blue-700 transition">الخدمات الطبية</a>
                <span>/</span>
                <span class="text-blue-700 font-bold">{{ $service->name }}</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="inline-block text-[11px] font-black uppercase tracking-wider text-[#557143] bg-[#87A96B]/15 px-3 py-1 rounded-full border border-[#87A96B]/30 mb-2">
                        تفاصيل الإجراء الطبي
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-black text-slate-900 leading-tight">
                        {{ $service->name }}
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 rounded-2xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        مدة الجلسة: <strong class="text-blue-700">{{ $service->duration_minutes }} دقيقة</strong>
                    </span>
                    <span class="px-4 py-2 rounded-2xl bg-blue-50 border border-blue-200 text-xs font-bold text-blue-900 shadow-xs">
                        السعر: <strong class="text-blue-700 font-black text-sm">{{ number_format($service->price, 0) }} ر.س</strong>
                    </span>
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
                    
                    <!-- Description & Clinical Overview -->
                    <div class="p-8 bg-white rounded-3xl border border-slate-100 shadow-xs space-y-4">
                        <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                            الوصف الطبي للإجراء
                        </h2>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ $service->description ?? 'تعد هذه الخدمة من الركائز الأساسية للرعاية السنية في مركز ريادة لطب الأسنان، حيث نحرص على استخدام أرقى المواد الحيوية المتوافقة وتقنيات العلاج المجهري المريحة لضمان نجاح المعالجة بنسبة فائقة ودون أي قلق.' }}
                        </p>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            يتم فحص وتوثيق الحالة عبر أجهزة التصوير الرقمي ثلاثي الأبعاد قبل البدء، ويقوم الطبيب المعالج بشرح كافة خطوات العلاج للمريض لتحقيق أعلى مستويات الرضا والراحة النفسية.
                        </p>
                    </div>

                    <!-- Steps of Treatment -->
                    <div class="p-8 bg-white rounded-3xl border border-slate-100 shadow-xs">
                        <h2 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-[#87A96B]"></span>
                            مراحل وخطوات الجلسة العلاجية
                        </h2>

                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-black text-xs flex items-center justify-center flex-shrink-0">1</div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 mb-1">الفحص والتشخيص السريري الدقيق</h3>
                                    <p class="text-xs text-slate-500 leading-relaxed">فحص شامل للسن والأنسجة المحيطة باستخدام الكاميرا الفموية الرقمية لتقييم الاحتياج بدقة.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-black text-xs flex items-center justify-center flex-shrink-0">2</div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 mb-1">التهيئة والتخدير الموضعي غير المؤلم</h3>
                                    <p class="text-xs text-slate-500 leading-relaxed">تطبيق تخدير موضعي لطيف يضمن عدم شعور المريض بأي وخز أو ألم أثناء تنفيذ المعالجة.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-black text-xs flex items-center justify-center flex-shrink-0">3</div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 mb-1">تنفيذ الإجراء الطبي بأعلى المعايير</h3>
                                    <p class="text-xs text-slate-500 leading-relaxed">إتمام الخدمة باستخدام أدوات فردية معقمة وخامات أصلية تضمن المتانة والجمال الطبيعي للسن.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-[#87A96B]/20 text-[#557143] font-black text-xs flex items-center justify-center flex-shrink-0">4</div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 mb-1">التقييم النهائي وإرشادات العناية</h3>
                                    <p class="text-xs text-slate-500 leading-relaxed">التأكد من راحة الإطباق وتقديم خطة نصائح للعناية الوقائية والمتابعة الدورية.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Post Treatment Care Instructions -->
                    <div class="p-8 bg-blue-50/60 rounded-3xl border border-blue-100">
                        <h2 class="text-base font-black text-blue-950 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            إرشادات العناية بعد الجلسة
                        </h2>
                        <ul class="space-y-2 text-xs text-blue-900/80 list-disc list-inside leading-relaxed">
                            <li>تجنب تناول المشروبات الساخنة جداً أو المثلجة خلال الساعات الأولى بعد الجلسة.</li>
                            <li>الحفاظ على تنظيف الأسنان بفرشاة ناعمة ومعجون مدعم بالفلورايد لضمان استدامة النتائج.</li>
                            <li>التواصل مع العيادة فوراً عند وجود أي استفسار أو ملاحظة عبر أرقام خدمة المرضى المباشرة.</li>
                        </ul>
                    </div>

                </div>

                <!-- Sticky Sidebar Column (Col 4) -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Direct Booking Card -->
                    <div class="p-8 bg-white rounded-3xl border border-slate-100 shadow-md sticky top-28 space-y-6">
                        <div>
                            <span class="text-xs font-bold text-slate-400 block mb-1">تكلفة الخدمة التقديرية</span>
                            <div class="text-3xl font-black text-slate-900 mb-1">
                                {{ number_format($service->price, 0) }} <span class="text-base font-bold text-blue-700">ر.س</span>
                            </div>
                            <span class="text-[11px] text-slate-400 block">شاملة الفحص السريري والتعقيم الكامل</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-slate-100 space-y-2.5 text-xs text-slate-600">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">مدة الجلسة:</span>
                                <span>{{ $service->duration_minutes }} دقيقة</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">التأكيد:</span>
                                <span class="text-emerald-700 font-bold">فوري بالدقيقة</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">الضمان:</span>
                                <span class="text-blue-700 font-bold">معتمد سريرياً</span>
                            </div>
                        </div>

                        <a href="{{ route('appointments.book', ['service' => $service->id]) }}" 
                           class="w-full inline-flex items-center justify-center py-4 rounded-full text-sm font-black text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-lg shadow-blue-700/25 transition">
                            احجز هذه الخدمة الآن ←
                        </a>

                        <div class="text-center pt-2">
                            <span class="text-[11px] text-slate-400 block mb-1">للاستفسار السريع عبر واتساب:</span>
                            @if(!empty($settings['social_whatsapp']))
                                <a href="{{ $settings['social_whatsapp'] }}" target="_blank" class="text-xs font-bold text-emerald-600 hover:underline">
                                    تحدث مع خدمة العملاء
                                </a>
                            @else
                                <a href="tel:{{ $settings['clinic_phone'] ?? '0596482371' }}" class="text-xs font-bold text-blue-700 hover:underline">
                                    اتصل بالمركز: {{ $settings['clinic_phone'] ?? '0596482371' }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Other Services List -->
                    @if(isset($relatedServices) && count($relatedServices) > 0)
                        <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-xs">
                            <h3 class="text-xs font-black text-slate-900 mb-4 pb-2 border-b border-slate-100">خدمات أخرى قد تهمك:</h3>
                            <ul class="space-y-3 text-xs">
                                @foreach($relatedServices as $rel)
                                    <li>
                                        <a href="{{ route('services.show', $rel->id) }}" class="flex items-center justify-between text-slate-700 hover:text-blue-700 transition">
                                            <span class="font-bold">{{ $rel->name }}</span>
                                            <span class="text-blue-700 font-bold text-[11px]">{{ number_format($rel->price, 0) }} ر.س</span>
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
