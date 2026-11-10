@extends('layouts.admin')

@section('title', 'إضافة خدمة طبية جديدة')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- ترويسة الصفحة وزر العودة -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">إضافة خدمة سنية جديدة</h1>
            <p class="text-xs text-slate-500 mt-1">أدخل بيانات الإجراء العلاجي والتكلفة والمدة التقديرية للجلسة السريرية.</p>
        </div>

        <a href="{{ route('admin.services.index') }}" 
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-sm">
            <span>→</span>
            <span>العودة للخدمات</span>
        </a>
    </div>

    <!-- بطاقة النموذج (Form Card) -->
    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-8 sm:p-10">
        
        <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-6">
            @csrf

            <!-- 1. اسم الخدمة -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 mb-2">
                    اسم الخدمة الطبية أو التجميلية <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}" 
                       placeholder="مثال: تركيب ابتسامة هوليوود (Veneers)" 
                       required
                       class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('name') border-rose-300 bg-rose-50/30 @enderror">
                
                @error('name')
                    <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. السعر والمدة -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- السعر -->
                <div>
                    <label for="price" class="block text-xs font-bold text-slate-700 mb-2">
                        السعر الرسمي (ر.س) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" 
                               step="0.01" 
                               name="price" 
                               id="price" 
                               value="{{ old('price') }}" 
                               placeholder="500" 
                               required
                               class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 pl-12 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('price') border-rose-300 bg-rose-50/30 @enderror">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-xs font-bold text-[#87A96B]">
                            ر.س
                        </span>
                    </div>

                    @error('price')
                        <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- مدة الجلسة -->
                <div>
                    <label for="duration_minutes" class="block text-xs font-bold text-slate-700 mb-2">
                        المدة التقديرية للجلسة (بالدقائق) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" 
                               name="duration_minutes" 
                               id="duration_minutes" 
                               value="{{ old('duration_minutes', 45) }}" 
                               min="5" 
                               max="480"
                               required
                               class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 pl-14 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('duration_minutes') border-rose-300 bg-rose-50/30 @enderror">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-xs font-bold text-slate-400">
                            دقيقة
                        </span>
                    </div>

                    @error('duration_minutes')
                        <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 3. الوصف التفصيلي للخدمة -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 mb-2">
                    وصف موجز للإجراء العلاجي (اختياري)
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4" 
                          placeholder="اكتب شرحاً مختصراً عن خطوات الجلسة والتقنيات الطبية المستخدمة لتعريف المريض بها..."
                          class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition @error('description') border-rose-300 bg-rose-50/30 @enderror">{{ old('description') }}</textarea>

                @error('description')
                    <p class="text-[11px] font-bold text-rose-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- أزرار الإرسال والإلغاء -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end space-x-3 space-x-reverse">
                <a href="{{ route('admin.services.index') }}" 
                   class="px-6 py-3 rounded-full text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-8 py-3 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition duration-200 shadow-lg shadow-blue-900/10 hover:-translate-y-1">
                    حفظ ونشر الخدمة الطبية ✓
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
