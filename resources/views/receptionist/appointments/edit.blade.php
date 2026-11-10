@extends('layouts.receptionist')

@section('title', 'تعديل الموعد الطبي رقم #' . $appointment->id)

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

        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#1E40AF]">
            موعد رقم #{{ $appointment->id }}
        </span>
    </div>

    <!-- بطاقة النموذج -->
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.03)]">
        
        <div class="pb-6 border-b border-slate-100 mb-6">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#1E40AF]"></span>
                <span class="text-xs font-black text-[#1E40AF] uppercase tracking-wider">تعديل الموعد</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">تعديل تفاصيل حجز المريض: {{ $appointment->patient?->name }}</h1>
            <p class="text-xs text-slate-500 mt-1">إعادة جدولة الموعد، تغيير الطبيب أو نوع الخدمة، وتحديث الحالة.</p>
        </div>

        <form method="POST" action="{{ route('receptionist.appointments.update', $appointment->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- صف 1: المريض والطبيب -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- المريض -->
                <div>
                    <label for="patient_id" class="block text-xs font-black text-slate-700 mb-2">المريض *</label>
                    <select id="patient_id" 
                            name="patient_id" 
                            required 
                            class="w-full px-4 py-3 text-xs bg-slate-50 border @error('patient_id') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ (old('patient_id', $appointment->patient_id) == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->phone ?: $p->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- الطبيب المعالج -->
                <div>
                    <label for="dentist_id" class="block text-xs font-black text-slate-700 mb-2">طبيب الأسنان المعالج *</label>
                    <select id="dentist_id" 
                            name="dentist_id" 
                            required 
                            class="w-full px-4 py-3 text-xs bg-slate-50 border @error('dentist_id') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ (old('dentist_id', $appointment->dentist_id) == $doc->id) ? 'selected' : '' }}>
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
                        @foreach($services as $srv)
                            <option value="{{ $srv->id }}" {{ (old('service_id', $appointment->service_id) == $srv->id) ? 'selected' : '' }}>
                                {{ $srv->name }} ({{ $srv->duration_minutes }} دقيقة - {{ $srv->price }} ر.س)
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- حالة الحجز -->
                <div>
                    <label for="status" class="block text-xs font-black text-slate-700 mb-2">حالة الحجز *</label>
                    <select id="status" 
                            name="status" 
                            required 
                            class="w-full px-4 py-3 text-xs bg-slate-50 border @error('status') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                        <option value="confirmed" {{ old('status', $appointment->status) === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                        <option value="pending" {{ old('status', $appointment->status) === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="completed" {{ old('status', $appointment->status) === 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ old('status', $appointment->status) === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        <option value="no_show" {{ old('status', $appointment->status) === 'no_show' ? 'selected' : '' }}>لم يحضر</option>
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
                           value="{{ old('appointment_date', $appointment->appointment_date ? (is_string($appointment->appointment_date) ? substr($appointment->appointment_date, 0, 10) : $appointment->appointment_date->format('Y-m-d')) : '') }}" 
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
                           value="{{ old('start_time', substr($appointment->start_time, 0, 5)) }}" 
                           required 
                           class="w-full px-4 py-3 text-xs bg-slate-50 border @error('start_time') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                    @error('start_time')
                        <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- ملاحظات -->
            <div>
                <label for="notes" class="block text-xs font-black text-slate-700 mb-2">ملاحظات الحجز (اختياري)</label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3" 
                          class="w-full px-4 py-3 text-xs bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">{{ old('notes', $appointment->notes) }}</textarea>
                @error('notes')
                    <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- أزرار الإجراء -->
            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('receptionist.appointments.index') }}" class="px-5 py-3 rounded-2xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl text-xs font-bold bg-[#1E40AF] text-white shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition">
                    حفظ التحديثات
                </button>
            </div>
        </form>

    </div>

</div>
@endsection
