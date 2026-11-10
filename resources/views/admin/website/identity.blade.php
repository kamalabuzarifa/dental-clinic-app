@extends('layouts.admin')

@section('title', 'هوية المركز والشعار - إدارة الموقع الإلكتروني')
@section('page_title', 'هوية المركز والشعار')

@section('content')
<div class="space-y-6">
    <!-- شريط التبويبات العلوي -->
    @include('admin.website.partials.tabs')

    <form method="POST" action="{{ route('admin.website.identity.update') }}" enctype="multipart/form-data">
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

        <div class="space-y-6">
            <!-- 1. الأسماء والشعارات -->
            <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
                <div class="flex items-center space-x-3.5 space-x-reverse pb-5 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                        🏥
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900">الهوية البصرية والاسم الرسمي</h2>
                        <p class="text-xs text-slate-400 mt-0.5">تحديد الاسم العربي والإنجليزي، الشعار، الأيقونة المفضلة (Favicon)، وصورة الواجهة الرئيسية</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">اسم المركز الرسمي (بالعربية): <span class="text-rose-500">*</span></label>
                        <input type="text" 
                               name="clinic_name" 
                               value="{{ old('clinic_name', $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان') }}" 
                               required
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">الاسم التجاري (بالإنجليزية):</label>
                        <input type="text" 
                               name="clinic_name_en" 
                               value="{{ old('clinic_name_en', $settings['clinic_name_en'] ?? 'RIYADA DENTAL CARE') }}" 
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition text-slate-800">
                    </div>
                </div>

                <!-- رفع الصور: الشعار، Favicon، صورة الهيرو -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                    <!-- الشعار الرسمي -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <label class="block text-xs font-bold text-slate-800">الشعار الرسمي (Logo):</label>
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 flex items-center justify-center p-1 overflow-hidden shadow-inner flex-shrink-0">
                                @if(!empty($settings['clinic_logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['clinic_logo']))
                                    <img src="{{ asset('storage/' . $settings['clinic_logo']) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                                @else
                                    <span class="text-xs text-slate-400 font-bold">لا يوجد</span>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <input type="file" name="clinic_logo" accept="image/*" class="text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#1E40AF] file:text-white hover:file:bg-blue-700 cursor-pointer">
                                <p class="text-[10px] text-slate-400">PNG / SVG شفاف بحد أقصى 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- الأيقونة المفضلة Favicon -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <label class="block text-xs font-bold text-slate-800">أيقونة المتصفح (Favicon):</label>
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 flex items-center justify-center p-1 overflow-hidden shadow-inner flex-shrink-0">
                                @if(!empty($settings['clinic_favicon']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['clinic_favicon']))
                                    <img src="{{ asset('storage/' . $settings['clinic_favicon']) }}" alt="Favicon" class="w-8 h-8 object-contain">
                                @else
                                    <span class="text-xs text-slate-400 font-bold">لا يوجد</span>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <input type="file" name="clinic_favicon" accept="image/x-icon,image/png,image/svg+xml" class="text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#1E40AF] file:text-white hover:file:bg-blue-700 cursor-pointer">
                                <p class="text-[10px] text-slate-400">ICO / PNG مربعة 32x32 أو 64x64</p>
                            </div>
                        </div>
                    </div>

                    <!-- صورة الغلاف الترحيبية Hero Image -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                        <label class="block text-xs font-bold text-slate-800">صورة الواجهة الرئيسية (Hero Cover):</label>
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16 rounded-xl bg-white border border-slate-200 flex items-center justify-center p-1 overflow-hidden shadow-inner flex-shrink-0">
                                @if(!empty($settings['clinic_hero_image']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['clinic_hero_image']))
                                    <img src="{{ asset('storage/' . $settings['clinic_hero_image']) }}" alt="Hero" class="max-w-full max-h-full object-cover rounded-lg">
                                @else
                                    <span class="text-xs text-slate-400 font-bold">افتراضية</span>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <input type="file" name="clinic_hero_image" accept="image/*" class="text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#1E40AF] file:text-white hover:file:bg-blue-700 cursor-pointer">
                                <p class="text-[10px] text-slate-400">JPG / WebP عالية الجودة بحد أقصى 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الوصف المختصر للمركز والشريط الترويجي -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">الوصف التعريفي المقتضب (يظهر في الترويسة والتذييل):</label>
                        <textarea name="clinic_description" rows="3" class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">{{ old('clinic_description', $settings['clinic_description'] ?? 'مركز رائد ومتخصص في تقديم أرقى علاجات وجراحات وتجميل الأسنان بأحدث التقنيات الرقمية المتقدمة.') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">شريط الإعلان الترويجي العلوي (Announcement Bar):</label>
                        <textarea name="announcement_bar" rows="3" class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">{{ old('announcement_bar', $settings['announcement_bar'] ?? 'مركز ريادة لطب وجراحة وتجميل الأسنان: أحدث التقنيات الرقمية المريحة للأسرة') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 2. معلومات التواصل والعنوان والموقع الجغرافي -->
            <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
                <div class="flex items-center space-x-3.5 space-x-reverse pb-5 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                        📍
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900">بيانات الاتصال والعنوان الجغرافي</h2>
                        <p class="text-xs text-slate-400 mt-0.5">أرقام الهواتف، البريد الإلكتروني، العنوان التفصيلي، وكود خريطة جوجل التفاعلية</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">رقم الهاتف الرئيسي للاتصال: <span class="text-rose-500">*</span></label>
                        <input type="text" 
                               name="clinic_phone" 
                               value="{{ old('clinic_phone', $settings['clinic_phone'] ?? '0596482371') }}" 
                               required
                               dir="ltr"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">رقم واتساب المباشر:</label>
                        <input type="text" 
                               name="clinic_whatsapp" 
                               value="{{ old('clinic_whatsapp', $settings['clinic_whatsapp'] ?? '0596482371') }}" 
                               dir="ltr"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">البريد الإلكتروني الرسمي: <span class="text-rose-500">*</span></label>
                        <input type="email" 
                               name="clinic_email" 
                               value="{{ old('clinic_email', $settings['clinic_email'] ?? 'infoclini@riyadaclinic.com') }}" 
                               required
                               dir="ltr"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">العنوان الجغرافي التفصيلي:</label>
                        <input type="text" 
                               name="clinic_address" 
                               value="{{ old('clinic_address', $settings['clinic_address'] ?? 'فلسطين - غزة - مفترق السرايا - برج الإتقان') }}" 
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">رابط تضمين خريطة جوجل (Google Maps Iframe src):</label>
                        <input type="text" 
                               name="clinic_map_iframe" 
                               value="{{ old('clinic_map_iframe', $settings['clinic_map_iframe'] ?? '') }}" 
                               placeholder="https://www.google.com/maps/embed?..."
                               dir="ltr"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">
                    </div>
                </div>

                <!-- حسابات التواصل الاجتماعي -->
                <div class="pt-2">
                    <h3 class="text-xs font-black text-slate-800 mb-3">حسابات وقنوات التواصل الاجتماعي:</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">منصة X (تويتر سابقاً):</label>
                            <input type="text" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? 'https://x.com/riyadaclinic') }}" dir="ltr" class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">حساب إنستغرام:</label>
                            <input type="text" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? 'https://instagram.com/riyadaclinic') }}" dir="ltr" class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">صفحة فيسبوك:</label>
                            <input type="text" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? 'https://facebook.com/riyadaclinic') }}" dir="ltr" class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none text-slate-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">رابط واتساب المباشر:</label>
                            <input type="text" name="social_whatsapp" value="{{ old('social_whatsapp', $settings['social_whatsapp'] ?? 'https://wa.me/970596482371') }}" dir="ltr" class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none text-slate-800">
                        </div>
                    </div>
                </div>
            </div>

            <!-- زر الحفظ الدائم -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-[#1E40AF] to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-black text-xs rounded-2xl shadow-lg shadow-blue-900/20 hover:shadow-xl transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>حفظ بيانات الهوية وتحديث الموقع فوراً</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
