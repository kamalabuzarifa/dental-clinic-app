@extends('layouts.admin')

@section('title', 'تعديل ملف المريض')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- ترويسة الصفحة وزر العودة -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">تعديل ملف المريض: {{ $patient->name }}</h1>
            <p class="text-xs text-slate-500 mt-1">تحديث بيانات التواصل أو كلمة المرور للمريض.</p>
        </div>

        <a href="{{ route('admin.patients.index') }}" 
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-sm">
            <span>→</span>
            <span>العودة للمرضى</span>
        </a>
    </div>

    <!-- بطاقة النموذج (Form Card) -->
    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-8 sm:p-10">
        
        <form method="POST" action="{{ route('admin.patients.update', $patient) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. اسم المريض -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 mb-2">
                    اسم المريض الرباعي <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name', $patient->name) }}" 
                       required
                       class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('name') border-rose-300 bg-rose-50/30 @enderror">
                
                @error('name')
                    <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. رقم الهاتف والبريد -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 mb-2">
                        رقم الجوال <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="phone" 
                           id="phone" 
                           value="{{ old('phone', $patient->phone) }}" 
                           required
                           class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('phone') border-rose-300 bg-rose-50/30 @enderror">

                    @error('phone')
                        <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-2">
                        البريد الإلكتروني <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', $patient->email) }}" 
                           required
                           class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('email') border-rose-300 bg-rose-50/30 @enderror">

                    @error('email')
                        <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 3. تغيير كلمة المرور (اختياري) -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-2">
                    تغيير كلمة المرور (اتركه فارغاً إذا كنت لا ترغب بالتغيير)
                </label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       placeholder="كلمة مرور جديدة..." 
                       class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('password') border-rose-300 bg-rose-50/30 @enderror">

                @error('password')
                    <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- أزرار الإرسال والإلغاء -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3 space-x-reverse">
                <a href="{{ route('admin.patients.index') }}" 
                   class="px-6 py-3 rounded-full text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-8 py-3 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition duration-200 shadow-lg shadow-blue-900/10 hover:-translate-y-1">
                    تحديث الملف الطبي للمريض ✓
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
