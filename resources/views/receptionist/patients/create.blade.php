@extends('layouts.receptionist')

@section('title', 'فتح ملف مريض جديد')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- شريط الرجوع -->
    <div class="flex items-center justify-between">
        <a href="{{ route('receptionist.patients.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>العودة لقائمة المرضى</span>
        </a>
    </div>

    <!-- بطاقة النموذج -->
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.03)]">
        
        <div class="pb-6 border-b border-slate-100 mb-6">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#1E40AF]"></span>
                <span class="text-xs font-black text-[#1E40AF] uppercase tracking-wider">سجل جديد</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">فتح ملف طبي لمريض جديد</h1>
            <p class="text-xs text-slate-500 mt-1">أدخل بيانات المريض الأساسية للتواصل والتذكير بالمواعيد.</p>
        </div>

        <form method="POST" action="{{ route('receptionist.patients.store') }}" class="space-y-5">
            @csrf

            <!-- الاسم الرباعي -->
            <div>
                <label for="name" class="block text-xs font-black text-slate-700 mb-2">اسم المريض الرباعي *</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       placeholder="مثال: خالد عبدالله الحربي"
                       class="w-full px-4 py-3 text-xs bg-slate-50 border @error('name') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                @error('name')
                    <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- رقم الهاتف -->
            <div>
                <label for="phone" class="block text-xs font-black text-slate-700 mb-2">رقم الهاتف الجوال *</label>
                <input type="text" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone') }}" 
                       required 
                       placeholder="مثال: 0501234567"
                       dir="ltr"
                       class="w-full px-4 py-3 text-xs bg-slate-50 border @error('phone') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium text-right">
                @error('phone')
                    <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- البريد الإلكتروني -->
            <div>
                <label for="email" class="block text-xs font-black text-slate-700 mb-2">البريد الإلكتروني *</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       placeholder="patient@example.com"
                       dir="ltr"
                       class="w-full px-4 py-3 text-xs bg-slate-50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium text-right">
                @error('email')
                    <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- كلمة المرور للحساب (اختيارية) -->
            <div>
                <label for="password" class="block text-xs font-black text-slate-700 mb-2">
                    كلمة المرور للحساب (اختيارية)
                    <span class="text-[11px] font-normal text-slate-400 mr-1">- في حال تركها فارغة ستكون الافتراضية: patient123</span>
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="••••••••"
                       class="w-full px-4 py-3 text-xs bg-slate-50 border @error('password') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                @error('password')
                    <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- أزرار الإجراء -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('receptionist.patients.index') }}" class="px-5 py-3 rounded-2xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl text-xs font-bold bg-[#1E40AF] text-white shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition">
                    حفظ وفتح الملف الطبي
                </button>
            </div>
        </form>

    </div>

</div>
@endsection
