@extends('layouts.public')

@section('title', $seo['title'] ?? ('حجز موعد طبي إلكتروني | ' . ($settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان')))
@section('meta_description', $seo['description'] ?? 'احجز موعدك لطب وتجميل وجراحة الأسنان إلكترونياً وبدقة تامة بالدقيقة، مع اختيار الطبيب المعالج والخدمة والتوقيت المناسب لجدولك دون أي انتظار.')
@section('meta_keywords', $seo['keywords'] ?? 'حجز موعد أسنان, موعد طبي, حجز فوري عيادة')
@section('canonical', route('appointments.book'))

@section('content')

    <!-- Breadcrumb & Header -->
    <section class="bg-gradient-to-b from-blue-50/50 to-white border-b border-slate-100 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 space-x-reverse text-xs font-semibold text-slate-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-blue-700 transition">الرئيسية</a>
                <span>/</span>
                <span class="text-blue-700 font-bold">حجز موعد إلكتروني</span>
            </nav>

            <div class="max-w-3xl">
                <span class="inline-block text-[11px] font-black uppercase tracking-wider text-[#557143] bg-[#87A96B]/15 px-3 py-1 rounded-full border border-[#87A96B]/30 mb-2">
                    نظام الجدولة الذكي
                </span>
                <h1 class="text-2xl sm:text-4xl font-black text-slate-900 leading-tight mb-3">
                    حجز موعدك الطبي مؤكد بالدقيقة وبلا انتظار
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                    اختر الإجراء الطبي المطلوب، طبيبك المعالج، والوقت الملائم لجدولك اليومي. يقوم نظامنا بفحص التوفر وتأكيد الحجز فورياً.
                </p>
            </div>
        </div>
    </section>

    <!-- Booking Form Section -->
    <section class="py-14 bg-[#F8FAFC]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(($settings['maintenance_mode'] ?? '0') == '1')
                <!-- Maintenance Mode Warning -->
                <div class="p-8 rounded-3xl bg-amber-50 border border-amber-200 text-center shadow-xs mb-8">
                    <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-4 text-2xl font-black">⚠️</div>
                    <h2 class="text-lg font-black text-amber-900 mb-2">نظام الحجز الإلكتروني في وضع الصيانة والتحديث مؤقتاً</h2>
                    <p class="text-xs text-amber-700 max-w-xl mx-auto leading-relaxed mb-6">
                        نقوم حالياً بتحديث وتطوير جدول المواعيد السريرية لخدمتكم بشكل أفضل. للتسجيل أو الحجز الفوري يرجى الاتصال المباشر بإدارة المركز أو عبر تطبيق واتساب.
                    </p>
                    <div class="flex items-center justify-center gap-4">
                        @if(!empty($settings['social_whatsapp']))
                            <a href="{{ $settings['social_whatsapp'] }}" target="_blank" class="px-6 py-3 rounded-full text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition">
                                تواصل للحجز عبر واتساب
                            </a>
                        @endif
                        <a href="tel:{{ $settings['clinic_phone'] ?? '0596482371' }}" class="px-6 py-3 rounded-full text-xs font-bold text-blue-900 bg-white border border-amber-300 hover:bg-amber-100/50 transition">
                            اتصل بنا: {{ $settings['clinic_phone'] ?? '0596482371' }}
                        </a>
                    </div>
                </div>
            @else

                <!-- Working Hours Alert Info -->
                <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200/70 text-blue-900 text-xs font-semibold flex items-center justify-between mb-8 shadow-xs">
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <svg class="w-5 h-5 text-blue-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>ساعات عمل العيادة الرسمية: <strong>{{ $workingHoursText ?? 'السبت - الخميس (09:00 ص - 09:00 م)' }}</strong> | الجمعة عطلة رسمية.</span>
                    </div>
                    <span class="hidden sm:inline-block text-[11px] text-[#557143] font-bold bg-[#87A96B]/20 px-2.5 py-1 rounded-full">نظام منع التعارض مفعل</span>
                </div>

                <!-- Guest Notice (if not authenticated) -->
                @guest
                    <div class="p-6 rounded-3xl bg-white border border-blue-200 shadow-md mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center space-x-3 space-x-reverse text-right">
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-lg flex-shrink-0">
                                ℹ️
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-black text-slate-900">هل لديك حساب مسجل في العيادة؟</h3>
                                <p class="text-[11px] text-slate-500">لحفظ موعدك الطبي وتأكيده في سجلك الصحي، يرجى تسجيل الدخول أو فتح حساب مريض جديد في خطوة واحدة.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                                تسجيل الدخول
                            </a>
                            <a href="{{ route('register') }}" class="px-4 py-2 text-xs font-bold text-white bg-blue-700 hover:bg-blue-800 rounded-xl transition">
                                حساب جديد
                            </a>
                        </div>
                    </div>
                @endguest

                <!-- The Interactive Form -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.08)] p-6 sm:p-12"
                     x-data="{
                        selectedService: '{{ old('service_id', $preselectedService ?? ($services->first()->id ?? '')) }}',
                        selectedDoctor: '{{ old('dentist_id', $preselectedDoctor ?? '') }}',
                        services: {{ $services->toJson() }},
                        startTime: '{{ old('start_time', '10:00') }}',
                        get currentService() {
                            return this.services.find(s => s.id == this.selectedService) || {};
                        }
                     }">

                    <form method="POST" action="{{ route('appointments.book.store') }}" class="space-y-10">
                        @csrf

                        <!-- 1. Service Selection -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-black text-slate-900">
                                    1. اختر الخدمة الطبية أو التجميلية: <span class="text-blue-700">*</span>
                                </label>
                                <span class="text-xs text-slate-400">أسعار شاملة للفحص والتعقيم</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($services as $srv)
                                    <label class="relative flex flex-col p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 select-none"
                                           :class="selectedService == {{ $srv->id }} ? 'border-blue-600 bg-blue-50/50 shadow-sm' : 'border-slate-100 hover:border-slate-200 bg-white'">
                                        <input type="radio" name="service_id" value="{{ $srv->id }}" class="sr-only" x-model="selectedService">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-black text-slate-900 text-sm">{{ $srv->name }}</span>
                                            <span class="text-xs font-black text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200/60">
                                                {{ number_format($srv->price, 0) }} ر.س
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 line-clamp-2 mb-4 leading-relaxed">{{ $srv->description ?? 'جلسة علاجية سنية متكاملة بأحدث الأجهزة.' }}</p>
                                        <div class="mt-auto text-[11px] font-bold text-slate-400 flex items-center gap-1.5">
                                            <span>⏰</span>
                                            مدة الجلسة: <span class="text-slate-800">{{ $srv->duration_minutes }} دقيقة</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('service_id')
                                <p class="text-rose-600 text-xs mt-2 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 2. Dentist Selection -->
                        <div>
                            <label for="dentist_id" class="block text-sm font-black text-slate-900 mb-2">
                                2. اختر الطبيب المعالج: <span class="text-blue-700">*</span>
                            </label>
                            <select name="dentist_id" 
                                    id="dentist_id" 
                                    x-model="selectedDoctor"
                                    class="w-full px-5 py-3 transition bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800">
                                <option value="">-- يرجى اختيار الطبيب المعالج --</option>
                                @foreach($dentists as $dentist)
                                    <option value="{{ $dentist->id }}" {{ (old('dentist_id', $preselectedDoctor ?? '') == $dentist->id) ? 'selected' : '' }}>
                                        {{ $dentist->name }} (طبيب واستشاري معتمد)
                                    </option>
                                @endforeach
                            </select>
                            @error('dentist_id')
                                <p class="text-rose-600 text-xs mt-2 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 3. Date & Time Selection -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Date -->
                            <div>
                                <label for="appointment_date" class="block text-sm font-black text-slate-900 mb-2">
                                    3. تاريخ الزيارة: <span class="text-blue-700">*</span>
                                </label>
                                <input type="date" 
                                       name="appointment_date" 
                                       id="appointment_date" 
                                       min="{{ date('Y-m-d') }}"
                                       value="{{ old('appointment_date', date('Y-m-d')) }}"
                                       class="w-full px-5 py-3 transition bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800">
                                @error('appointment_date')
                                    <p class="text-rose-600 text-xs mt-2 font-bold">{{ $message }}</p>
                                @enderror
                                <p class="text-[11px] text-slate-400 mt-1.5 font-medium">أيام العمل: من السبت إلى الخميس (الجمعة عطلة).</p>
                            </div>

                            <!-- Time -->
                            <div>
                                <label for="start_time" class="block text-sm font-black text-slate-900 mb-2">
                                    4. توقيت بدء الجلسة: <span class="text-blue-700">*</span>
                                </label>
                                <select name="start_time" id="start_time" x-model="startTime" class="w-full px-5 py-3 transition bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800">
                                    @php
                                        $timeSlots = [
                                            '09:00' => '09:00 صباحاً',
                                            '09:30' => '09:30 صباحاً',
                                            '10:00' => '10:00 صباحاً',
                                            '10:30' => '10:30 صباحاً',
                                            '11:00' => '11:00 صباحاً',
                                            '11:30' => '11:30 صباحاً',
                                            '12:00' => '12:00 ظهراً',
                                            '12:30' => '12:30 ظهراً',
                                            '16:00' => '04:00 عصراً',
                                            '16:30' => '04:30 عصراً',
                                            '17:00' => '05:00 مساءً',
                                            '17:30' => '05:30 مساءً',
                                            '18:00' => '06:00 مساءً',
                                            '18:30' => '06:30 مساءً',
                                            '19:00' => '07:00 مساءً',
                                            '19:30' => '07:30 مساءً',
                                            '20:00' => '08:00 مساءً',
                                            '20:30' => '08:30 مساءً',
                                        ];
                                    @endphp
                                    @foreach($timeSlots as $val => $label)
                                        <option value="{{ $val }}" {{ old('start_time', '10:00') == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('start_time')
                                    <p class="text-rose-600 text-xs mt-2 font-bold">{{ $message }}</p>
                                @enderror
                                <p class="text-[11px] text-[#557143] mt-1.5 font-bold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-[#87A96B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    نظام منع التعارض يفحص الموعد لمنع أي تضارب زمني.
                                </p>
                            </div>
                        </div>

                        <!-- 4. Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-black text-slate-900 mb-2">
                                5. ملاحظات أو تفاصيل طبية إضافية (اختياري):
                            </label>
                            <textarea name="notes" id="notes" rows="3" placeholder="اكتب أي استفسار أو ملاحظات تود إحاطة الطبيب بها مسبقاً..." class="w-full px-5 py-3 transition bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-semibold text-slate-800">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-rose-600 text-xs mt-2 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Live Cost & Duration Summary Bar -->
                        <div class="p-5 rounded-2xl bg-[#F8FAFC] border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <span class="text-xs text-slate-400 block">ملخص الحجز المختار:</span>
                                <h4 class="text-sm font-black text-slate-900" x-text="currentService.name || 'يرجى اختيار خدمة'"></h4>
                                <span class="text-xs text-blue-700 font-bold" x-show="currentService.price">
                                    التكلفة التقديرية: <span x-text="currentService.price"></span> ر.س (<span x-text="currentService.duration_minutes"></span> دقيقة)
                                </span>
                            </div>

                            @auth
                                <button type="submit" class="inline-flex items-center justify-center px-8 py-4 text-sm font-black text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-full shadow-lg shadow-blue-700/25 transition w-full sm:w-auto">
                                    تأكيد وحجز الموعد الآن ✓
                                </button>
                            @else
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <button type="submit" class="inline-flex items-center justify-center px-8 py-4 text-sm font-black text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-full shadow-lg shadow-blue-700/25 transition w-full sm:w-auto">
                                        تسجيل الدخول وتأكيد الحجز ←
                                    </button>
                                </div>
                            @endauth
                        </div>

                    </form>

                </div>

            @endif

        </div>
    </section>

@endsection
