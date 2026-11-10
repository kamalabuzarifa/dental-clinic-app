@extends('layouts.admin')

@section('title', 'الموقع والواجهة العامة - إعدادات النظام')
@section('page_title', 'الموقع والواجهة العامة')

@section('content')
<script>
    function websiteSettingsHub() {
        return {
            maintenanceMode: {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'true' : 'false' }}
        };
    }
</script>

<div class="space-y-6" x-data="websiteSettingsHub()">
    <!-- شريط التبويبات العلوي -->
    @include('admin.settings.partials.tabs')

    <form method="POST" action="{{ route('admin.settings.website.update') }}">
        @csrf

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
                <span class="block">يرجى تصحيح الأخطاء التالية قبل المتابعة:</span>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
            <!-- رأس الصندوق -->
            <div class="flex items-center space-x-3.5 space-x-reverse pb-5 border-b border-slate-100">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                    🌐
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900">إعدادات الموقع العام وصفحة الهبوط</h2>
                    <p class="text-xs text-slate-400 mt-0.5">وضع الصيانة، شريط الإعلانات الترويجي، وقنوات التواصل الاجتماعي</p>
                </div>
            </div>

            <div class="space-y-5">
                <!-- مفتاح وضع الصيانة -->
                <div class="flex items-center justify-between p-5 rounded-2xl bg-rose-50/70 border border-rose-200 transition">
                    <div class="space-y-1 pr-2">
                        <span class="block text-xs font-black text-rose-950">تفعيل وضع الصيانة (إيقاف الحجز والواجهة مؤقتاً)</span>
                        <span class="text-[11px] text-rose-700">عند التفعيل، تظهر رسالة اعتذار رسمية للزوار في الصفحة الرئيسية ويتوقف الحجز الإلكتروني</span>
                    </div>
                    <input type="hidden" name="maintenance_mode" :value="maintenanceMode ? '1' : '0'">
                    <button type="button"
                            @click="maintenanceMode = !maintenanceMode"
                            :class="maintenanceMode ? 'bg-rose-600' : 'bg-slate-200'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                        <span :class="maintenanceMode ? '-translate-x-5' : 'translate-x-0'"
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                </div>

                <!-- شريط الإعلان -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">رسالة الإعلان العلوي في صفحة الهبوط (Announcement Bar):</label>
                    <input type="text"
                           name="announcement_bar"
                           value="{{ old('announcement_bar', $settings['announcement_bar'] ?? 'خصم 20% على جلسات تبييض الأسنان بالليزر وفينير الابتسامة الماسية لفترة محدودة!') }}"
                           placeholder="اكتب إعلاناً ترويجياً أو رسالة ترحيب..."
                           class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition text-slate-800">
                </div>

                <!-- روابط التواصل الاجتماعي -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">منصة X (تويتر سابقاً):</label>
                        <input type="text"
                               name="social_twitter"
                               value="{{ old('social_twitter', $settings['social_twitter'] ?? 'https://x.com/riyadaclinic') }}"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">حساب إنستغرام:</label>
                        <input type="text"
                               name="social_instagram"
                               value="{{ old('social_instagram', $settings['social_instagram'] ?? 'https://instagram.com/riyadaclinic') }}"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">صفحة فيسبوك:</label>
                        <input type="text"
                               name="social_facebook"
                               value="{{ old('social_facebook', $settings['social_facebook'] ?? 'https://facebook.com/riyadaclinic') }}"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">رابط واتساب المباشر:</label>
                        <input type="text"
                               name="social_whatsapp"
                               value="{{ old('social_whatsapp', $settings['social_whatsapp'] ?? 'https://wa.me/966501234567') }}"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none text-slate-800">
                    </div>
                </div>
            </div>
        </div>

        <!-- شريط الحفظ الثابت (Sticky Save Footer) -->
        <div class="sticky bottom-6 z-20 bg-white/95 backdrop-blur-md rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.12)] flex items-center justify-between gap-4 mt-8">
            <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-medium">
                <svg class="w-4 h-4 text-[#87A96B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>يتم تطبيق التحديثات على واجهة العيادة وصفحة الهبوط العامة فور الحفظ.</span>
            </div>

            <div class="flex items-center space-x-3 space-x-reverse w-full sm:w-auto justify-end">
                <button type="submit"
                        class="px-8 py-3 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition duration-200 shadow-lg shadow-blue-900/20 hover:-translate-y-0.5">
                    حفظ إعدادات الموقع العام ✓
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
