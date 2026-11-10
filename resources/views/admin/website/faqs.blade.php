@extends('layouts.admin')

@section('title', 'إدارة الأسئلة الشائعة - إدارة الموقع الإلكتروني')
@section('page_title', 'الأسئلة الشائعة')

@section('content')
<div class="space-y-6">
    <!-- شريط التبويبات العلوي -->
    @include('admin.website.partials.tabs')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- النموذج: إضافة سؤال وجواب جديد -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 space-y-5 sticky top-24">
                <div class="flex items-center space-x-3 space-x-reverse pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg shadow-xs flex-shrink-0">
                        ❓
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">إضافة سؤال شائع جديد</h2>
                        <p class="text-[11px] text-slate-400">إجابات واضحة لاستفسارات الزوار والمرضى</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.website.faqs.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">نص السؤال: <span class="text-rose-500">*</span></label>
                        <input type="text" name="question" required placeholder="مثلاً: كم تستغرق جلسة تبييض الأسنان بالليزر؟" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">التصنيف:</label>
                            <select name="category" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                <option value="general" selected>عام واستفسارات</option>
                                <option value="booking">الحجوزات والمواعيد</option>
                                <option value="treatments">العلاجات والتقنيات</option>
                                <option value="insurance">الأسعار والضمان</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">حالة النشر:</label>
                            <select name="status" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                <option value="published" selected>منشور بالواجهة</option>
                                <option value="pending">مسودة</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">الإجابة التفصيلية: <span class="text-rose-500">*</span></label>
                        <textarea name="answer" rows="5" required placeholder="اكتب الإجابة الطبية والإدارية المباشرة..." class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">ترتيب العرض (اختياري):</label>
                        <input type="number" name="sort_order" value="0" min="0" max="99" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#1E40AF] hover:bg-blue-700 text-white rounded-xl text-xs font-black shadow-md shadow-blue-900/10 transition flex items-center justify-center gap-2">
                        <span>+</span>
                        <span>إضافة السؤال وتحديث الموقع</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- القائمة: الأسئلة المحفوظة -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-black text-slate-900">بنك الأسئلة الشائعة ({{ $faqs->count() }})</h3>
                    <span class="text-xs text-slate-400">تظهر في قسم الأسئلة الشائعة التفاعلي (Accordion) في الموقع</span>
                </div>

                <div class="space-y-3">
                    @forelse($faqs as $item)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-blue-100 text-[#1E40AF] flex items-center justify-center font-black text-xs">؟</span>
                                        <h4 class="text-xs font-black text-slate-900">{{ $item->question }}</h4>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1.5 pr-8">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">
                                            {{ $item->category }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $item->status === 'published' ? 'منشور' : 'مسودة' }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">ترتيب: {{ $item->sort_order }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="document.getElementById('edit-faq-{{ $item->id }}').classList.remove('hidden')" class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                                        تعديل
                                    </button>
                                    <form method="POST" action="{{ route('admin.website.faqs.destroy', $item->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال نهائياً؟')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-100 rounded-lg text-xs font-bold transition">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <p class="text-xs text-slate-600 leading-relaxed bg-white p-3 rounded-xl border border-slate-100 pr-4">
                                {{ $item->answer }}
                            </p>

                            <!-- Modal التعديل -->
                            <div id="edit-faq-{{ $item->id }}" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
                                <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4" dir="rtl">
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                        <h4 class="text-sm font-black text-slate-900">تعديل السؤال الشائع</h4>
                                        <button type="button" onclick="document.getElementById('edit-faq-{{ $item->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
                                    </div>

                                    <form method="POST" action="{{ route('admin.website.faqs.update', $item->id) }}" class="space-y-4">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">نص السؤال:</label>
                                            <input type="text" name="question" value="{{ $item->question }}" required class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">التصنيف:</label>
                                                <select name="category" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                                    <option value="general" {{ $item->category === 'general' ? 'selected' : '' }}>عام واستفسارات</option>
                                                    <option value="booking" {{ $item->category === 'booking' ? 'selected' : '' }}>الحجوزات والمواعيد</option>
                                                    <option value="treatments" {{ $item->category === 'treatments' ? 'selected' : '' }}>العلاجات والتقنيات</option>
                                                    <option value="insurance" {{ $item->category === 'insurance' ? 'selected' : '' }}>الأسعار والضمان</option>
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
                                            <label class="block text-xs font-bold text-slate-700 mb-1">الإجابة:</label>
                                            <textarea name="answer" rows="4" required class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">{{ $item->answer }}</textarea>
                                        </div>

                                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                            <button type="button" onclick="document.getElementById('edit-faq-{{ $item->id }}').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100">إلغاء</button>
                                            <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-[#1E40AF] text-white hover:bg-blue-700 shadow-md">تحديث السؤال</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 font-bold">لا توجد أسئلة شائعة مسجلة بعد. استخدم النموذج المجاور لإضافة أول سؤال.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
