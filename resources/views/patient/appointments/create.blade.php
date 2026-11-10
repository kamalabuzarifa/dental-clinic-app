<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 space-x-reverse">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-lg shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900">حجز موعد طبي جديد</h1>
                    <p class="text-xs text-slate-500">اختر الخدمة، الطبيب المعالج، والتوقيت الملائم لجدولك اليومي.</p>
                </div>
            </div>
            <a href="{{ route('patient.dashboard') }}" class="inline-flex items-center text-xs font-bold text-slate-600 hover:text-blue-700 bg-white border border-slate-200 px-4 py-2 rounded-full shadow-xs hover:shadow transition">
                ← العودة للوحة التحكم
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.08)] p-8 sm:p-12"
                 x-data="{
                    selectedService: '{{ old('service_id', $services->first()->id ?? '') }}',
                    services: {{ $services->toJson() }},
                    startTime: '{{ old('start_time', '10:00') }}',
                    get currentService() {
                        return this.services.find(s => s.id == this.selectedService) || {};
                    }
                 }">

                <form method="POST" action="{{ route('patient.appointments.store') }}" class="space-y-10">
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
                            @foreach($services as $service)
                                <label class="relative flex flex-col p-5 rounded-2xl border-2 cursor-pointer transition-all duration-200 select-none"
                                       :class="selectedService == {{ $service->id }} ? 'border-blue-600 bg-blue-50/50 shadow-sm' : 'border-slate-100 hover:border-slate-200 bg-white'">
                                    <input type="radio" name="service_id" value="{{ $service->id }}" class="sr-only" x-model="selectedService">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-black text-slate-900 text-sm">{{ $service->name }}</span>
                                        <span class="text-xs font-black text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200/60">
                                            {{ number_format($service->price, 0) }} ر.س
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 line-clamp-2 mb-4 leading-relaxed">{{ $service->description ?? 'جلسة علاجية سنية متكاملة بأحدث الأجهزة.' }}</p>
                                    <div class="mt-auto text-[11px] font-bold text-slate-400 flex items-center gap-1.5">
                                        <span>⏰</span>
                                        مدة الجلسة: <span class="text-slate-800">{{ $service->duration_minutes }} دقيقة</span>
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
                        <select name="dentist_id" id="dentist_id" class="w-full px-5 py-3 transition-all duration-300 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800">
                            <option value="">-- يرجى اختيار الطبيب المعالج --</option>
                            @foreach($dentists as $dentist)
                                <option value="{{ $dentist->id }}" {{ old('dentist_id') == $dentist->id ? 'selected' : '' }}>
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
                                   class="w-full px-5 py-3 transition-all duration-300 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800">
                            @error('appointment_date')
                                <p class="text-rose-600 text-xs mt-2 font-bold">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-slate-400 mt-1.5 font-medium">ساعات عمل العيادة: من السبت إلى الخميس.</p>
                        </div>

                        <!-- Time -->
                        <div>
                            <label for="start_time" class="block text-sm font-black text-slate-900 mb-2">
                                4. وقت بدء الجلسة: <span class="text-blue-700">*</span>
                            </label>
                            <select name="start_time" id="start_time" x-model="startTime" class="w-full px-5 py-3 transition-all duration-300 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800">
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
                                نظام منع التعارض الذكي يفحص التوفر فورياً لضمان توقيتك بالدقيقة.
                            </p>
                        </div>
                    </div>

                    <!-- 4. Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-black text-slate-900 mb-2">
                            5. ملاحظات أو تفاصيل طبية إضافية (اختياري):
                        </label>
                        <textarea name="notes" id="notes" rows="3" placeholder="اكتب أي استفسار أو ملاحظات تود إحاطة الطبيب بها مسبقاً..." class="w-full px-5 py-3 transition-all duration-300 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-semibold text-slate-800">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-rose-600 text-xs mt-2 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit & Summary Bar -->
                    <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500 font-medium">
                            سيتم تأكيد الموعد وإدراجه في جدول العيادة فور الضغط على الزر.
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-full shadow-lg shadow-blue-700/25 hover:-translate-y-0.5 w-full sm:w-auto">
                            تأكيد وحجز الموعد الآن ✓
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>