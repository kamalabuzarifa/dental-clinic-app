@extends('layouts.admin')

@section('title', 'أقسام الصفحة الرئيسية - إدارة الموقع الإلكتروني')
@section('page_title', 'أقسام الصفحة الرئيسية')

@section('content')
<div class="space-y-6">
    <!-- شريط التبويبات العلوي -->
    @include('admin.website.partials.tabs')

    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between pb-5 border-b border-slate-100">
            <div class="flex items-center space-x-3.5 space-x-reverse">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                    📑
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900">التحكم في أقسام الصفحة الرئيسية (Landing Page Sections)</h2>
                    <p class="text-xs text-slate-400 mt-0.5">ترتيب الأقسام، إظهار أو إخفاء أي قسم بضغطة زر، وتعديل العناوين والشارات الترويجية</p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            @foreach($sections as $section)
                <div class="p-5 rounded-2xl border transition duration-200 {{ $section->is_visible ? 'bg-slate-50/70 border-slate-200' : 'bg-slate-100/50 border-dashed border-slate-300 opacity-75' }}">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        
                        <!-- معلومات القسم والحالة -->
                        <div class="flex items-start gap-4">
                            <span class="w-9 h-9 rounded-xl bg-blue-100 text-[#1E40AF] flex items-center justify-center font-black text-xs flex-shrink-0 mt-0.5 shadow-xs">
                                {{ $section->sort_order }}
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-black text-slate-900">{{ $section->title }}</h3>
                                    @if($section->badge)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#87A96B]/15 text-[#557143]">
                                            {{ $section->badge }}
                                        </span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $section->is_visible ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $section->is_visible ? 'ظاهر بالموقع' : 'مخفي' }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $section->subtitle ?: 'قسم معتمد بالصفحة الرئيسية' }}</p>
                                <span class="text-[10px] font-mono text-slate-400">مفتاح القسم: {{ $section->section_key }}</span>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات والتبديل السريع -->
                        <div class="flex items-center gap-2 self-end lg:self-auto">
                            <!-- زر التبديل السريع للإظهار/الإخفاء -->
                            <form method="POST" action="{{ route('admin.website.sections.toggle', $section->id) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $section->is_visible ? 'bg-amber-100 text-amber-800 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' }}"
                                        title="{{ $section->is_visible ? 'إخفاء من الواجهة العامة' : 'إظهار في الواجهة العامة' }}">
                                    <span>{{ $section->is_visible ? 'إخفاء القسم ✕' : 'إظهار القسم ✓' }}</span>
                                </button>
                            </form>

                            <!-- زر فتح نموذج التعديل التفاعلي -->
                            <button type="button" 
                                    onclick="document.getElementById('edit-modal-{{ $section->id }}').classList.remove('hidden')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#1E40AF] text-white hover:bg-blue-700 transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span>تعديل النصوص والترتيب</span>
                            </button>
                        </div>

                    </div>

                    <!-- Modal نافذة التعديل للقسم -->
                    <div id="edit-modal-{{ $section->id }}" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
                        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4" dir="rtl">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <h4 class="text-sm font-black text-slate-900">تعديل بيانات القسم: {{ $section->title }}</h4>
                                <button type="button" onclick="document.getElementById('edit-modal-{{ $section->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-base font-bold">✕</button>
                            </div>

                            <form method="POST" action="{{ route('admin.website.sections.update', $section->id) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">عنوان القسم الرئيسي:</label>
                                    <input type="text" name="title" value="{{ $section->title }}" required class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">العنوان الفرعي أو التوضيحي:</label>
                                    <input type="text" name="subtitle" value="{{ $section->subtitle }}" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">الشارة الترويجية (Badge):</label>
                                        <input type="text" name="badge" value="{{ $section->badge }}" placeholder="مثلاً: مميزاتنا، أطباؤنا" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">ترتيب الظهور في الصفحة:</label>
                                        <input type="number" name="sort_order" value="{{ $section->sort_order }}" min="1" max="99" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-2">
                                    <input type="checkbox" id="vis-{{ $section->id }}" name="is_visible" value="1" {{ $section->is_visible ? 'checked' : '' }} class="rounded text-[#1E40AF] focus:ring-[#1E40AF]">
                                    <label for="vis-{{ $section->id }}" class="text-xs font-bold text-slate-700">تفعيل ظهور هذا القسم في الصفحة الرئيسية</label>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                                    <button type="button" onclick="document.getElementById('edit-modal-{{ $section->id }}').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100">إلغاء</button>
                                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-[#1E40AF] text-white hover:bg-blue-700 shadow-md">حفظ التعديلات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
