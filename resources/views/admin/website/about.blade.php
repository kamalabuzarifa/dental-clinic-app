@extends('layouts.admin')

@section('title', 'محتوى صفحة من نحن - إدارة الموقع الإلكتروني')
@section('page_title', 'محتوى صفحة من نحن')

@section('content')
<div class="space-y-6">
    <!-- شريط التبويبات العلوي -->
    @include('admin.website.partials.tabs')

    <form method="POST" action="{{ route('admin.website.about.update') }}">
        @csrf

        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
            <div class="flex items-center space-x-3.5 space-x-reverse pb-5 border-b border-slate-100">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                    📖
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900">محتوى صفحة من نحن ورؤيتنا وقيمنا (/about)</h2>
                    <p class="text-xs text-slate-400 mt-0.5">تحديث قصة تأسيس المركز، الرؤية المستقبلية، الرسالة الطبية، والقيم الجوهرية</p>
                </div>
            </div>

            <!-- قصة التأسيس والنشأة -->
            <div class="space-y-4">
                <h3 class="text-xs font-black text-slate-800">قصة المركز ومسيرة التميز:</h3>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">الفقرة الأولى (بداية التأسيس والهدف): <span class="text-rose-500">*</span></label>
                    <textarea name="about_story_p1" rows="3" required class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">{{ old('about_story_p1', $settings['about_story_p1'] ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">الفقرة الثانية (التطوير المستمر والتقنيات الرقمية): <span class="text-rose-500">*</span></label>
                    <textarea name="about_story_p2" rows="3" required class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">{{ old('about_story_p2', $settings['about_story_p2'] ?? '') }}</textarea>
                </div>
            </div>

            <!-- الرؤية والرسالة والقيم -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-3">
                <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 space-y-2">
                    <label class="block text-xs font-black text-[#1E40AF] flex items-center gap-1.5">
                        <span>🎯</span>
                        <span>رؤيتنا المستقبلية:</span>
                    </label>
                    <textarea name="about_vision" rows="4" required class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">{{ old('about_vision', $settings['about_vision'] ?? '') }}</textarea>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100 space-y-2">
                    <label class="block text-xs font-black text-[#557143] flex items-center gap-1.5">
                        <span>📜</span>
                        <span>رسالتنا الطبية:</span>
                    </label>
                    <textarea name="about_mission" rows="4" required class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-white focus:border-[#557143] outline-none transition text-slate-800">{{ old('about_mission', $settings['about_mission'] ?? '') }}</textarea>
                </div>

                <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-100 space-y-2">
                    <label class="block text-xs font-black text-purple-800 flex items-center gap-1.5">
                        <span>💎</span>
                        <span>قيمنا الجوهرية:</span>
                    </label>
                    <textarea name="about_values" rows="4" required class="w-full text-xs font-bold rounded-xl border border-slate-200 p-3 bg-white focus:border-purple-600 outline-none transition text-slate-800">{{ old('about_values', $settings['about_values'] ?? '') }}</textarea>
                </div>
            </div>

            <!-- زر الحفظ -->
            <div class="flex items-center justify-end pt-3">
                <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-[#1E40AF] to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-black text-xs rounded-2xl shadow-lg shadow-blue-900/20 hover:shadow-xl transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>حفظ وتحديث صفحة من نحن فوراً</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
