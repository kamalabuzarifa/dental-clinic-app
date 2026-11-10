@extends('layouts.admin')

@section('title', 'تسجيل موعد جديد')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- ترويسة الصفحة وزر العودة -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">تسجيل وتأكيد حجز جديد</h1>
            <p class="text-xs text-slate-500 mt-1">تحديد المريض، الطبيب المعالج، الخدمة الطبية، والوقت المتاح للعيادة.</p>
        </div>

        <a href="{{ route('admin.appointments.index') }}" 
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-sm">
            <span>→</span>
            <span>العودة للحجوزات</span>
        </a>
    </div>

    <!-- بطاقة النموذج (Form Card) -->
    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-8 sm:p-10">
        
        <form method="POST" action="{{ route('admin.appointments.store') }}" class="space-y-6">
            @csrf

            <!-- 1. اختيار المريض والطبيب -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="patient_id" class="block text-xs font-bold text-slate-700 mb-2">
                        المريض <span class="text-rose-500">*</span>
                    </label>
                    <select name="patient_id" id="patient_id" required
                            class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('patient_id') border-rose-300 bg-rose-50/30 @enderror">
                        <option value="">-- اختر المريض --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->name }} ({{ $patient->phone ?? $patient->email }})
                            </option>
                        @endforeach
                    </select>

                    @error('patient_id')
                        <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="dentist_id" class="block text-xs font-bold text-slate-700 mb-2">
                        الطبيب الاستشاري المعالج <span class="text-rose-500">*</span>
                    </label>
                    <select name="dentist_id" id="dentist_id" required
                            class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('dentist_id') border-rose-300 bg-rose-50/30 @enderror">
                        <option value="">-- اختر الطبيب --</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('dentist_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('dentist_id')
                        <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 2. الخدمة الطبية والحالة -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="service_id" class="block text-xs font-bold text-slate-700 mb-2">
                        الخدمة المطلوبة <span class="text-rose-500">*</span>
                    </label>
                    <select name="service_id" id="service_id" required
                            class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('service_id') border-rose-300 bg-rose-50/30 @enderror">
                        <option value="">-- اختر الخدمة --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }} ({{ number_format($service->price, 0) }} ر.س - {{ $service->duration_minutes }} دقيقة)
                            </option>
                        @endforeach
                    </select>

                    @error('service_id')
                        <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 mb-2">
                        حالة الحجز الأولية <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('status') border-rose-300 bg-rose-50/30 @enderror">
                        <option value="confirmed" {{ old('status', 'confirmed') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>

                    @error('status')
                        <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 3. تاريخ الموعد والوقت -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="appointment_date" class="block text-xs font-bold text-slate-700 mb-2">
                        تاريخ الموعد <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" 
                           name="appointment_date" 
                           id="appointment_date" 
                           value="{{ old('appointment_date', date('Y-m-d')) }}" 
                           required
                           class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('appointment_date') border-rose-300 bg-rose-50/30 @enderror">

                    @error('appointment_date')
                        <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_time" class="block text-xs font-bold text-slate-700 mb-2">
                        وقت الحضور <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" 
                           name="start_time" 
                           id="start_time" 
                           value="{{ old('start_time', '10:00') }}" 
                           required
                           class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('start_time') border-rose-300 bg-rose-50/30 @enderror">

                    @error('start_time')
                        <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 4. الملاحظات -->
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 mb-2">
                    ملاحظات طبية أو خاصة بالزيارة
                </label>
                <textarea name="notes" id="notes" rows="3" 
                          placeholder="مثال: مريض يعاني من حساسية سابقة، يفضل التخدير الموضعي الخفيف..."
                          class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition">{{ old('notes') }}</textarea>
            </div>

            <!-- أزرار الإرسال والإلغاء -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3 space-x-reverse">
                <a href="{{ route('admin.appointments.index') }}" 
                   class="px-6 py-3 rounded-full text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-8 py-3 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition duration-200 shadow-lg shadow-blue-900/10 hover:-translate-y-1">
                    تأكيد وتثبيت الحجز ✓
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
