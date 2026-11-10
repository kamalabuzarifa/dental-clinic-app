@extends('layouts.receptionist')

@section('title', 'حجز موعد جديد')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- شريط الرجوع -->
    <div class="flex items-center justify-between">
        <a href="{{ route('receptionist.appointments.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>العودة لجدول المواعيد</span>
        </a>

        <a href="{{ route('receptionist.patients.create') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1E40AF] hover:underline">
            <span>+ فتح ملف لمريض جديد</span>
        </a>
    </div>

    <!-- بطاقة النموذج -->
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.03)]">
        
        <div class="pb-6 border-b border-slate-100 mb-6">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#1E40AF]"></span>
                <span class="text-xs font-black text-[#1E40AF] uppercase tracking-wider">حجز جديد</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">تسجيل وتأكيد حجز موعد طبي</h1>
            <p class="text-xs text-slate-500 mt-1">يقوم النظام تلقائياً بفحص أوقات دوام المركز، العطلات، والتحقق من عدم وجود تعارض بالمواعيد.</p>
        </div>

        <form method="POST" action="{{ route('receptionist.appointments.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

            <!-- صف 1: المريض والطبيب -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- اختيار المريض -->
                <div>
                    <label for="patient_id" class="block text-xs font-black text-slate-700 mb-2">المريض *</label>
                    <select id="patient_id" 
                            name="patient_id" 
                            required 
                            class="w-full px-4 py-3 text-xs bg-slate-50 border @error('patient_id') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                        <option value="">-- اختر المريض --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ (old('patient_id', $selectedPatientId) == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->phone ?: $p->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- اختيار الطبيب -->
                <div>
                    <label for="dentist_id" class="block text-xs font-black text-slate-700 mb-2">طبيب الأسنان المعالج *</label>
                    <select id="dentist_id" 
                            name="dentist_id" 
                            required 
                            class="w-full px-4 py-3 text-xs bg-slate-50 border @error('dentist_id') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                        <option value="">-- اختر الطبيب المعالج --</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ (old('dentist_id', $selectedDentistId) == $doc->id) ? 'selected' : '' }}>
                                د. {{ $doc->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('dentist_id')
                        <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- صف 2: الخدمة الطبية وحالة الحجز -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- الخدمة الطبية -->
                <div>
                    <label for="service_id" class="block text-xs font-black text-slate-700 mb-2">الخدمة الطبية المطلوبة *</label>
                    <select id="service_id" 
                            name="service_id" 
                            required 
                            class="w-full px-4 py-3 text-xs bg-slate-50 border @error('service_id') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                        <option value="">-- اختر نوع الخدمة --</option>
                        @foreach($services as $srv)
                            <option value="{{ $srv->id }}" {{ old('service_id') == $srv->id ? 'selected' : '' }}>
                                {{ $srv->name }} ({{ $srv->duration_minutes }} دقيقة - {{ $srv->price }} ر.س)
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- حالة الحجز المبدئية -->
                <div>
                    <label for="status" class="block text-xs font-black text-slate-700 mb-2">حالة الموعد *</label>
                    <select id="status" 
                            name="status" 
                            required 
                            class="w-full px-4 py-3 text-xs bg-slate-50 border @error('status') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                        <option value="confirmed" {{ old('status', 'confirmed') === 'confirmed' ? 'selected' : '' }}>مؤكد (حضور متوقع)</option>
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار (يحتاج تأكيد هاتفياً)</option>
                    </select>
                    @error('status')
                        <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- صف 3: تاريخ الموعد ووقت البدء -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- التاريخ -->
                <div>
                    <label for="appointment_date" class="block text-xs font-black text-slate-700 mb-2">تاريخ الموعد *</label>
                    <input type="date" 
                           id="appointment_date" 
                           name="appointment_date" 
                           value="{{ old('appointment_date', $defaultDate) }}" 
                           min="{{ date('Y-m-d') }}"
                           required 
                           class="w-full px-4 py-3 text-xs bg-slate-50 border @error('appointment_date') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                    @error('appointment_date')
                        <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- وقت البدء -->
                <div>
                    <label for="start_time" class="block text-xs font-black text-slate-700 mb-2">وقت بدء الجلسة *</label>
                    <input type="time" 
                           id="start_time" 
                           name="start_time" 
                           value="{{ old('start_time', '10:00') }}" 
                           required 
                           class="w-full px-4 py-3 text-xs bg-slate-50 border @error('start_time') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                    @error('start_time')
                        <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- ملاحظات إضافية -->
            <div>
                <label for="notes" class="block text-xs font-black text-slate-700 mb-2">ملاحظات الحجز أو شكوى المريض المبدئية (اختياري)</label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3" 
                          placeholder="مثال: فحص دوري، يعاني من ألم في الضرس الأيمن العلوي..." 
                          class="w-full px-4 py-3 text-xs bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- تنبيه أوقات دوام المركز المعتمدة -->
            <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 flex items-start gap-3">
                <svg class="w-5 h-5 text-[#1E40AF] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-xs text-slate-700 leading-relaxed">
                    <span class="font-bold text-[#1E40AF] block mb-0.5">ساعات دوام المركز:</span>
                    السبت إلى الخميس من 09:00 صباحاً حتى 09:00 مساءً. الجمعة عطلة رسمية بالمركز.
                </div>
            </div>

            <!-- أزرار الإجراء -->
            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('receptionist.appointments.index') }}" class="px-5 py-3 rounded-2xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl text-xs font-bold bg-[#1E40AF] text-white shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition">
                    تثبيت وتسجيل الحجز
                </button>
            </div>
        </form>

    </div>

</div>
@endsection
