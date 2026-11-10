@extends('layouts.admin')

@section('title', 'بيانات المركز والشعار - إعدادات النظام')
@section('page_title', 'بيانات المركز والشعار')

@section('content')
<script>
    function clinicSettingsHub() {
        return {
            logoPreview: null,
            previewLogo(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    const self = this;
                    reader.onload = function(e) {
                        self.logoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        };
    }
</script>

<div class="space-y-6" x-data="clinicSettingsHub()">
    <!-- شريط التبويبات العلوي -->
    @include('admin.settings.partials.tabs')

    <!-- نموذج بيانات المركز والشعار -->
    <form method="POST" action="{{ route('admin.settings.clinic.update') }}" enctype="multipart/form-data">
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
                    🏥
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900">المعلومات الأساسية والشعار المؤسسي</h2>
                    <p class="text-xs text-slate-400 mt-0.5">البيانات الرسمية للمركز المعروضة للمرضى، الفواتير، والصفحة الرئيسية</p>
                </div>
            </div>

            <!-- رفع ومعاينة الشعار -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center gap-5">
                <div class="w-24 h-24 rounded-2xl bg-white border border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-sm relative">
                    <template x-if="logoPreview">
                        <img :src="logoPreview" alt="معاينة الشعار" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!logoPreview">
                        @if(!empty($settings['clinic_logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['clinic_logo']))
                            <img src="{{ asset('storage/' . $settings['clinic_logo']) }}" alt="شعار المركز" class="w-full h-full object-cover">
                        @else
                            <div class="text-center p-2">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-gradient-to-tr from-[#1E40AF] to-blue-500 text-white flex items-center justify-center font-bold text-base shadow">
                                    ✦
                                </div>
                                <span class="text-[9px] font-bold text-slate-400 block mt-1">الشعار المعتمد</span>
                            </div>
                        @endif
                    </template>
                </div>

                <div class="flex-1 space-y-2 text-center sm:text-right">
                    <span class="block text-xs font-bold text-slate-800">شعار المركز الطبي (Clinic Logo)</span>
                    <p class="text-[11px] text-slate-500">الصيغ المدعومة: PNG, JPG, SVG, WebP بحد أقصى 2 ميجابايت.</p>
                    
                    <label class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-xs font-bold text-[#1E40AF] bg-blue-50 hover:bg-blue-100 border border-blue-200 cursor-pointer transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>رفع شعار جديد</span>
                        <input type="file" name="clinic_logo" @change="previewLogo($event)" accept="image/*" class="hidden">
                    </label>
                    @error('clinic_logo')
                        <p class="text-[11px] font-bold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- حقول البيانات -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">اسم المركز / العيادة الرسمي:</label>
                    <input type="text"
                           name="clinic_name"
                           value="{{ old('clinic_name', $settings['clinic_name'] ?? 'مركز ريادة الدولي لطب وجراحة الأسنان') }}"
                           required
                           class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">هاتف الاستقبال والتواصل المباشر:</label>
                    <input type="text"
                           name="clinic_phone"
                           value="{{ old('clinic_phone', $settings['clinic_phone'] ?? '0501234567') }}"
                           required
                           class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">البريد الإلكتروني الرسمي للعيادة:</label>
                    <input type="email"
                           name="clinic_email"
                           value="{{ old('clinic_email', $settings['clinic_email'] ?? 'info@riyadaclinic.com') }}"
                           required
                           class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition text-slate-800">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">العنوان الجغرافي والفرع الرئيسي:</label>
                    <input type="text"
                           name="clinic_address"
                           value="{{ old('clinic_address', $settings['clinic_address'] ?? 'الرياض - طريق الملك فهد - برج ريادة الطبي - الدور الرابع') }}"
                           class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition text-slate-800">
                </div>
            </div>
        </div>

        <!-- شريط الحفظ الثابت (Sticky Save Footer) -->
        <div class="sticky bottom-6 z-20 bg-white/95 backdrop-blur-md rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.12)] flex items-center justify-between gap-4 mt-8">
            <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-medium">
                <svg class="w-4 h-4 text-[#87A96B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>يتم حفظ وتطبيق التغييرات مباشرة في قاعدة البيانات MySQL وتظهر فوراً للمرضى والزوار.</span>
            </div>

            <div class="flex items-center space-x-3 space-x-reverse w-full sm:w-auto justify-end">
                <button type="submit"
                        class="px-8 py-3 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition duration-200 shadow-lg shadow-blue-900/20 hover:-translate-y-0.5">
                    حفظ بيانات المركز والشعار ✓
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
