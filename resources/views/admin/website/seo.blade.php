@extends('layouts.admin')

@section('title', 'تهيئة محركات البحث (SEO) - إدارة الموقع الإلكتروني')
@section('page_title', 'تهيئة محركات البحث (SEO)')

@section('content')
<div class="space-y-6">
    <!-- شريط التبويبات العلوي -->
    @include('admin.website.partials.tabs')

    <form method="POST" action="{{ route('admin.website.seo.update') }}">
        @csrf

        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
            <div class="flex items-center space-x-3.5 space-x-reverse pb-5 border-b border-slate-100">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                    🔍
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900">إدارة ميتا تاغ ومحركات البحث لكل صفحة (On-Page SEO)</h2>
                    <p class="text-xs text-slate-400 mt-0.5">تخصيص عنوان المتصفح والوصف والكلمات المفتاحية للظهور المتقدم في نتائج بحث جوجل</p>
                </div>
            </div>

            @php
                $pages = [
                    'home' => ['name' => 'الصفحة الرئيسية', 'url' => '/', 'icon' => '🏠'],
                    'about' => ['name' => 'صفحة من نحن ورؤيتنا', 'url' => '/about', 'icon' => '📖'],
                    'services' => ['name' => 'دليل الخدمات الطبية', 'url' => '/services', 'icon' => '🩺'],
                    'doctors' => ['name' => 'فريق الأطباء والاستشاريين', 'url' => '/doctors', 'icon' => '👨‍⚕️'],
                    'booking' => ['name' => 'بوابة حجز المواعيد', 'url' => '/appointments/book', 'icon' => '📅'],
                    'contact' => ['name' => 'صفحة تواصل معنا', 'url' => '/contact', 'icon' => '📞'],
                ];
            @endphp

            <div class="space-y-6">
                @foreach($pages as $key => $info)
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="text-base">{{ $info['icon'] }}</span>
                                <h3 class="text-xs font-black text-slate-900">{{ $info['name'] }}</h3>
                                <code class="text-[10px] bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-mono">{{ $info['url'] }}</code>
                            </div>
                            <span class="text-[10px] text-slate-400">Page Key: {{ $key }}</span>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">عنوان الصفحة في المتصفح (&lt;title&gt;):</label>
                                <input type="text" 
                                       name="seo_{{ $key }}_title" 
                                       value="{{ old('seo_' . $key . '_title', $settings['seo_' . $key . '_title'] ?? '') }}" 
                                       placeholder="عنوان جذاب يظهر في تبويب المتصفح وجوجل..."
                                       class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-white focus:border-[#1E40AF] outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">الوصف التعريفي (Meta Description):</label>
                                <textarea name="seo_{{ $key }}_description" 
                                          rows="2" 
                                          placeholder="ملخص محتوى الصفحة الموجه لمحركات البحث (150-160 حرف)..."
                                          class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-white focus:border-[#1E40AF] outline-none">{{ old('seo_' . $key . '_description', $settings['seo_' . $key . '_description'] ?? '') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">الكلمات المفتاحية (Meta Keywords):</label>
                                <input type="text" 
                                       name="seo_{{ $key }}_keywords" 
                                       value="{{ old('seo_' . $key . '_keywords', $settings['seo_' . $key . '_keywords'] ?? '') }}" 
                                       placeholder="كلمات مفصولة بفواصل: طب أسنان, زراعة أسنان, غزة..."
                                       class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-white focus:border-[#1E40AF] outline-none">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- زر الحفظ الدائم -->
            <div class="flex items-center justify-end pt-3">
                <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-[#1E40AF] to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-black text-xs rounded-2xl shadow-lg shadow-blue-900/20 hover:shadow-xl transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>حفظ بيانات محركات البحث وتحديث السجلات فوراً</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
