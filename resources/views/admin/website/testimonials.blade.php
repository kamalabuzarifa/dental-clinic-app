@extends('layouts.admin')

@section('title', 'آراء وتقييمات المرضى - إدارة الموقع الإلكتروني')
@section('page_title', 'آراء وتقييمات المرضى')

@section('content')
<div class="space-y-6">
    <!-- شريط التبويبات العلوي -->
    @include('admin.website.partials.tabs')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- النموذج: إضافة رأي / تقييم حقيقي جديد -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 space-y-5 sticky top-24">
                <div class="flex items-center space-x-3 space-x-reverse pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shadow-xs flex-shrink-0">
                        ⭐
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">إضافة تقييم مريض حقيقي</h2>
                        <p class="text-[11px] text-slate-400">إدراج شهادات المراجعين الموثقة في الواجهة</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.website.testimonials.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">اسم المريض / المراجع: <span class="text-rose-500">*</span></label>
                        <input type="text" name="patient_name" required placeholder="مثلاً: م. أحمد الخالدي" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">نوع العلاج المستلم:</label>
                        <input type="text" name="treatment_received" placeholder="مثلاً: زراعة أسنان فورية، تقويم شفاف" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">التقييم (النجوم):</label>
                            <select name="rating" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                <option value="5" selected>⭐⭐⭐⭐⭐ (5 نجوم)</option>
                                <option value="4">⭐⭐⭐⭐ (4 نجوم)</option>
                                <option value="3">⭐⭐⭐ (3 نجوم)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">حالة النشر:</label>
                            <select name="status" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                <option value="published" selected>منشور بالواجهة</option>
                                <option value="pending">مسودة مؤقتة</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">نص الرأي والشهادة: <span class="text-rose-500">*</span></label>
                        <textarea name="review" rows="4" required placeholder="اكتب تعليق وتجربة المريض مع الطاقم والعيادة..." class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">ترتيب العرض (اختياري):</label>
                        <input type="number" name="sort_order" value="0" min="0" max="99" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#1E40AF] hover:bg-blue-700 text-white rounded-xl text-xs font-black shadow-md shadow-blue-900/10 transition flex items-center justify-center gap-2">
                        <span>+</span>
                        <span>إضافة ونشر التقييم فوراً</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- القائمة: التقييمات المحفوظة -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-black text-slate-900">سجل التقييمات وآراء المرضى ({{ $testimonials->count() }})</h3>
                    <span class="text-xs text-slate-400">تظهر التقييمات المنشورة في قسم آراء المرضى بالصفحة الرئيسية</span>
                </div>

                <div class="space-y-3">
                    @forelse($testimonials as $item)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-xs font-black text-slate-900">{{ $item->patient_name }}</h4>
                                        @if($item->treatment_received)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-[#1E40AF]">
                                                {{ $item->treatment_received }}
                                            </span>
                                        @endif
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                            {{ $item->status === 'published' ? 'منشور' : 'مسودة' }}
                                        </span>
                                    </div>
                                    <div class="text-amber-500 text-xs mt-1">
                                        {{ str_repeat('⭐', $item->rating) }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="document.getElementById('edit-testimonial-{{ $item->id }}').classList.remove('hidden')" class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                                        تعديل
                                    </button>
                                    <form method="POST" action="{{ route('admin.website.testimonials.destroy', $item->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم نهائياً؟')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-100 rounded-lg text-xs font-bold transition">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <p class="text-xs text-slate-600 leading-relaxed bg-white p-3 rounded-xl border border-slate-100">
                                "{{ $item->review }}"
                            </p>

                            <!-- Modal التعديل -->
                            <div id="edit-testimonial-{{ $item->id }}" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
                                <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4" dir="rtl">
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                        <h4 class="text-sm font-black text-slate-900">تعديل تقييم: {{ $item->patient_name }}</h4>
                                        <button type="button" onclick="document.getElementById('edit-testimonial-{{ $item->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                                    </div>

                                    <form method="POST" action="{{ route('admin.website.testimonials.update', $item->id) }}" class="space-y-4">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">اسم المريض:</label>
                                            <input type="text" name="patient_name" value="{{ $item->patient_name }}" required class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">نوع العلاج:</label>
                                            <input type="text" name="treatment_received" value="{{ $item->treatment_received }}" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">التقييم:</label>
                                                <select name="rating" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                                    <option value="5" {{ $item->rating == 5 ? 'selected' : '' }}>5 نجوم</option>
                                                    <option value="4" {{ $item->rating == 4 ? 'selected' : '' }}>4 نجوم</option>
                                                    <option value="3" {{ $item->rating == 3 ? 'selected' : '' }}>3 نجوم</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">الحالة:</label>
                                                <select name="status" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                                    <option value="published" {{ $item->status === 'published' ? 'selected' : '' }}>منشور</option>
                                                    <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>مسودة</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">نص الرأي:</label>
                                            <textarea name="review" rows="3" required class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">{{ $item->review }}</textarea>
                                        </div>

                                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                            <button type="button" onclick="document.getElementById('edit-testimonial-{{ $item->id }}').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100">إلغاء</button>
                                            <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-[#1E40AF] text-white hover:bg-blue-700 shadow-md">تحديث التقييم</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 font-bold">لا توجد تقييمات مسجلة بعد. يمكنك إضافة أول تقييم عبر النموذج المجاور.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
