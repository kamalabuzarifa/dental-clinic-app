@extends('layouts.admin')

@section('title', 'الحجوزات والمواعيد - إعدادات النظام')
@section('page_title', 'الحجوزات والمواعيد')

@section('content')
<script>
    function bookingSettingsHub() {
        return {
            preventOverlappingAppointments: {{ ($settings['prevent_overlapping_appointments'] ?? '1') == '1' ? 'true' : 'false' }},
            emergencyImmediateBooking: {{ ($settings['emergency_immediate_booking'] ?? '0') == '1' ? 'true' : 'false' }}
        };
    }
</script>

<div class="space-y-6" x-data="bookingSettingsHub()">
    <!-- شريط التبويبات العلوي -->
    @include('admin.settings.partials.tabs')

    <form method="POST" action="{{ route('admin.settings.booking.update') }}">
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
                    📅
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900">ضوابط وسياسات الحجوزات والمواعيد</h2>
                    <p class="text-xs text-slate-400 mt-0.5">تحديد مهل الإلغاء، الفواصل الزمنية، والحد الأقصى للمواعيد اليومية لكل طبيب</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- مدة الجلسة الافتراضية -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">المدة الافتراضية للجلسة السريرية:</label>
                    <select name="default_session_duration"
                            class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">
                        <option value="15" {{ ($settings['default_session_duration'] ?? '30') == '15' ? 'selected' : '' }}>15 دقيقة (كشف سريع واستشارة)</option>
                        <option value="30" {{ ($settings['default_session_duration'] ?? '30') == '30' ? 'selected' : '' }}>30 دقيقة (جلسة قياسية - تنظيف/حشوات)</option>
                        <option value="45" {{ ($settings['default_session_duration'] ?? '30') == '45' ? 'selected' : '' }}>45 دقيقة (علاج جذور / تبييض ليزر)</option>
                        <option value="60" {{ ($settings['default_session_duration'] ?? '30') == '60' ? 'selected' : '' }}>60 دقيقة (جراحة / زراعة / فينير)</option>
                        <option value="90" {{ ($settings['default_session_duration'] ?? '30') == '90' ? 'selected' : '' }}>90 دقيقة (إجراء جراحي موسع)</option>
                    </select>
                </div>

                <!-- أقل مهلة للحجز المسبق -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">أقل مدة للحجز المسبق (بالساعات):</label>
                    <input type="number"
                           name="min_advance_booking_hours"
                           min="0" max="168"
                           value="{{ old('min_advance_booking_hours', $settings['min_advance_booking_hours'] ?? '2') }}"
                           class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800"
                           placeholder="مثال: 2">
                    <span class="text-[10px] text-slate-400 mt-1 block">الحد الأدنى بين وقت الحجز ووقت الموعد</span>
                </div>

                <!-- الفاصل الزمني بين الحجوزات -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">فاصل التعقيم بين المواعيد (بالدقائق):</label>
                    <input type="number"
                           name="appointment_buffer_minutes"
                           min="0" max="60"
                           value="{{ old('appointment_buffer_minutes', $settings['appointment_buffer_minutes'] ?? '10') }}"
                           class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800"
                           placeholder="مثال: 10">
                    <span class="text-[10px] text-slate-400 mt-1 block">فترة تجهيز العيادة وتعقيم الأدوات</span>
                </div>

                <!-- الحد الأقصى للمواعيد اليومية لكل طبيب -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">الحد الأقصى للمواعيد اليومية لكل طبيب:</label>
                    <input type="number"
                           name="max_daily_appointments_per_dentist"
                           min="1" max="100"
                           value="{{ old('max_daily_appointments_per_dentist', $settings['max_daily_appointments_per_dentist'] ?? '20') }}"
                           class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">
                </div>

                <!-- مهلة إلغاء الموعد -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">مهلة إلغاء الحجز من المريض (بالساعات):</label>
                    <input type="number"
                           name="cancellation_cutoff_hours"
                           min="0" max="72"
                           value="{{ old('cancellation_cutoff_hours', $settings['cancellation_cutoff_hours'] ?? '12') }}"
                           class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">
                    <span class="text-[10px] text-slate-400 mt-1 block">الحد المسموح به للمريض للإلغاء ذاتياً</span>
                </div>

                <!-- مهلة تأكيد الحجز المعلق -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">مهلة تأكيد الحجز قبل الإلغاء (بالساعات):</label>
                    <input type="number"
                           name="confirmation_cutoff_hours"
                           min="0" max="72"
                           value="{{ old('confirmation_cutoff_hours', $settings['confirmation_cutoff_hours'] ?? '24') }}"
                           class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">
                </div>
            </div>

            <!-- مفاتيح قواعد الحجز -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <!-- منع تداخل المواعيد -->
                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 transition hover:bg-white hover:border-[#1E40AF]/20">
                    <div class="space-y-0.5 pr-2">
                        <span class="block text-xs font-bold text-slate-800">منع تداخل المواعيد لنفس الطبيب في نفس الفترة (Overlap Prevention)</span>
                        <span class="text-[11px] text-slate-400">منع الحجز المزدوج للطبيب والاستشاري لضمان دقة الجدول السريري</span>
                    </div>
                    <input type="hidden" name="prevent_overlapping_appointments" :value="preventOverlappingAppointments ? '1' : '0'">
                    <button type="button"
                            @click="preventOverlappingAppointments = !preventOverlappingAppointments"
                            :class="preventOverlappingAppointments ? 'bg-[#87A96B]' : 'bg-slate-200'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                        <span :class="preventOverlappingAppointments ? '-translate-x-5' : 'translate-x-0'"
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                </div>

                <!-- حجز الطوارئ الفوري -->
                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 transition hover:bg-white hover:border-[#1E40AF]/20">
                    <div class="space-y-0.5 pr-2">
                        <span class="block text-xs font-bold text-slate-800">إتاحة حجز طوارئ فوري دون تأكيد مسبق</span>
                        <span class="text-[11px] text-slate-400">تجاوز شرط التأكيد لحالات ألم الأسنان الحاد والحوادث الطارئة</span>
                    </div>
                    <input type="hidden" name="emergency_immediate_booking" :value="emergencyImmediateBooking ? '1' : '0'">
                    <button type="button"
                            @click="emergencyImmediateBooking = !emergencyImmediateBooking"
                            :class="emergencyImmediateBooking ? 'bg-[#87A96B]' : 'bg-slate-200'"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                        <span :class="emergencyImmediateBooking ? '-translate-x-5' : 'translate-x-0'"
                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- شريط الحفظ الثابت (Sticky Save Footer) -->
        <div class="sticky bottom-6 z-20 bg-white/95 backdrop-blur-md rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.12)] flex items-center justify-between gap-4 mt-8">
            <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-medium">
                <svg class="w-4 h-4 text-[#87A96B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>يتم تطبيق القيود الزمنية فوراً على جميع عمليات الحجز الجديدة.</span>
            </div>

            <div class="flex items-center space-x-3 space-x-reverse w-full sm:w-auto justify-end">
                <button type="submit"
                        class="px-8 py-3 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition duration-200 shadow-lg shadow-blue-900/20 hover:-translate-y-0.5">
                    حفظ سياسات الحجوزات والمواعيد ✓
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
