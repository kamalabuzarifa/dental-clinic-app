@extends('layouts.receptionist')

@section('title', 'الملف الشخصي')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- الترويسة -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(30,64,175,0.03)]">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#1E40AF] to-blue-500 text-white font-black text-xl flex items-center justify-center shadow-lg shadow-blue-900/20 flex-shrink-0">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-900">{{ $user->name }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">موظف قسم الاستقبال والحجوزات | {{ $user->email }}</p>
            </div>
        </div>
    </div>

    <!-- نموذج تعديل البيانات وكلمة المرور -->
    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.03)]">
        
        <div class="pb-6 border-b border-slate-100 mb-6">
            <h2 class="text-base font-black text-slate-900">إعدادات الحساب وكلمة المرور</h2>
            <p class="text-xs text-slate-500 mt-1">تحديث الاسم الكريم، رقم الهاتف، وتعيين كلمة مرور جديدة للحساب.</p>
        </div>

        <form method="POST" action="{{ route('receptionist.profile.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <!-- الاسم -->
            <div>
                <label for="name" class="block text-xs font-black text-slate-700 mb-2">الاسم الكامل *</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $user->name) }}" 
                       required 
                       class="w-full px-4 py-3 text-xs bg-slate-50 border @error('name') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                @error('name')
                    <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- البريد الإلكتروني -->
            <div>
                <label for="email" class="block text-xs font-black text-slate-700 mb-2">عنوان البريد الإلكتروني *</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $user->email) }}" 
                       required 
                       dir="ltr"
                       class="w-full px-4 py-3 text-xs bg-slate-50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium text-right">
                @error('email')
                    <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- رقم الهاتف -->
            <div>
                <label for="phone" class="block text-xs font-black text-slate-700 mb-2">رقم الهاتف الجوال</label>
                <input type="text" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone', $user->phone) }}" 
                       placeholder="0501234567"
                       dir="ltr"
                       class="w-full px-4 py-3 text-xs bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium text-right">
                @error('phone')
                    <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-black text-slate-900 mb-1">تغيير كلمة المرور</h3>
                <p class="text-[11px] text-slate-400 mb-4">اترك الحقول فارغة في حال عدم الرغبة في تغيير كلمة المرور الحالية.</p>

                <!-- كلمة المرور الجديدة -->
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-600 mb-2">كلمة المرور الجديدة</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••"
                               class="w-full px-4 py-3 text-xs bg-slate-50 border @error('password') border-rose-400 @else border-slate-200 @enderror rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                        @error('password')
                            <p class="text-[11px] text-rose-600 font-bold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- تأكيد كلمة المرور -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-600 mb-2">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="••••••••"
                               class="w-full px-4 py-3 text-xs bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition font-medium">
                    </div>
                </div>
            </div>

            <!-- زر الحفظ -->
            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-2xl text-xs font-bold bg-[#1E40AF] text-white shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition">
                    حفظ التعديلات
                </button>
            </div>
        </form>

    </div>

</div>
@endsection
