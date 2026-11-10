@extends('layouts.admin')

@section('title', 'إعدادات النظام والمركز')

@section('content')
<script>
    function settingsHub() {
        return {
            activeTab: '{{ request('tab', 'clinic') }}',
            logoPreview: null,
            // Main Toggles
            maintenanceMode: {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'true' : 'false' }},
            whatsappReminder: {{ ($settings['whatsapp_reminder'] ?? '1') == '1' ? 'true' : 'false' }},
            autoCancel24h: {{ ($settings['auto_cancel_24h'] ?? '1') == '1' ? 'true' : 'false' }},
            dentistEmailNotification: {{ ($settings['dentist_email_notification'] ?? '1') == '1' ? 'true' : 'false' }},
            patientConfirmationSms: {{ ($settings['patient_confirmation_sms'] ?? '1') == '1' ? 'true' : 'false' }},
            preventOverlappingAppointments: {{ ($settings['prevent_overlapping_appointments'] ?? '1') == '1' ? 'true' : 'false' }},
            allowDentistEditPrices: {{ ($settings['allow_dentist_edit_prices'] ?? '0') == '1' ? 'true' : 'false' }},
            allowReceptionistDeleteAppointments: {{ ($settings['allow_receptionist_delete_appointments'] ?? '1') == '1' ? 'true' : 'false' }},
            allowPatientCancel12h: {{ ($settings['allow_patient_cancel_12h'] ?? '1') == '1' ? 'true' : 'false' }},
            emergencyImmediateBooking: {{ ($settings['emergency_immediate_booking'] ?? '0') == '1' ? 'true' : 'false' }},
            allowUserRegistration: {{ ($settings['allow_user_registration'] ?? '1') == '1' ? 'true' : 'false' }},
            logFailedLogins: {{ ($settings['log_failed_logins'] ?? '1') == '1' ? 'true' : 'false' }},
            // Working Days
            days: @js($workingHours),
            // Roles Management Active Tab
            activeRole: 'dentist',
            // User Modals
            roleModalUser: null,
            passwordModalUser: null,
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

<div class="space-y-6" x-data="settingsHub()">

    <!-- ======================================================== -->
    <!-- ترويسة الصفحة ومؤشر الحالة العامة                       -->
    <!-- ======================================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">إعدادات النظام والمركز</h1>
            <p class="text-xs text-slate-500 mt-1">إدارة الهوية المؤسسية، ضوابط الحجوزات، مصفوفة الصلاحيات، حسابات المستخدمين، وإعدادات الأمان.</p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto px-4 py-2 bg-white rounded-full border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] text-xs font-semibold text-slate-700">
            <span class="w-2.5 h-2.5 rounded-full {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'bg-rose-500 animate-ping' : 'bg-[#87A96B] animate-pulse' }}"></span>
            <span>{{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'وضع الصيانة مفعّل (الحجز معلّق)' : 'قاعدة البيانات متصلة والنظام نشط' }}</span>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- النموذج الرئيسي لحفظ الإعدادات العامة (التبويبات 1-5 و 8)  -->
    <!-- ======================================================== -->
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" id="settingsForm">
        @csrf
        <input type="hidden" name="active_tab" :value="activeTab">

        <!-- ==================================================== -->
        <!-- تبويب 1: البيانات الأساسية والشعار                    -->
        <!-- ==================================================== -->
        <div x-show="activeTab === 'clinic'" x-transition class="space-y-6">
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
        </div>

        <!-- ==================================================== -->
        <!-- تبويب 2: الحجوزات والمواعيد                           -->
        <!-- ==================================================== -->
        <div x-show="activeTab === 'booking'" x-transition class="space-y-6" style="display: none;">
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
        </div>

        <!-- ==================================================== -->
        <!-- تبويب 3: أوقات العمل الرسمية                          -->
        <!-- ==================================================== -->
        <div x-show="activeTab === 'hours'" x-transition class="space-y-6" style="display: none;">
            <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-100">
                    <div class="flex items-center space-x-3.5 space-x-reverse">
                        <div class="w-12 h-12 rounded-2xl bg-[#87A96B]/15 text-[#87A96B] flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                            ⏰
                        </div>
                        <div>
                            <h2 class="text-base font-black text-slate-900">جدول أوقات العمل والدوام الأسبوعي</h2>
                            <p class="text-xs text-slate-400 mt-0.5">تحديد ساعات الاستقبال اليومية وأيام الإجازات الرسمية للعيادة</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-[#F8FAFC] text-slate-900 font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3.5 rounded-r-xl">اليوم</th>
                                <th class="px-5 py-3.5">حالة اليوم</th>
                                <th class="px-5 py-3.5">وقت بدء الاستقبال</th>
                                <th class="px-5 py-3.5 rounded-l-xl">وقت إغلاق العيادة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="(day, index) in days" :key="index">
                                <tr class="hover:bg-blue-50/20 transition">
                                    <td class="px-5 py-4 font-bold text-slate-900 text-sm">
                                        <span x-text="day.name"></span>
                                        <input type="hidden" :name="'days[' + index + '][name]'" :value="day.name">
                                    </td>
                                    <td class="px-5 py-4">
                                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                            <input type="checkbox"
                                                   x-model="day.is_off"
                                                   class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 w-4 h-4">
                                            <input type="hidden" :name="'days[' + index + '][is_off]'" :value="day.is_off ? '1' : '0'">
                                            <span :class="day.is_off ? 'text-rose-600 font-bold bg-rose-50 px-2.5 py-1 rounded-full' : 'text-slate-600 font-medium'"
                                                  x-text="day.is_off ? 'عطلة أسبوعية مغلقة' : 'يوم عمل متاح'"></span>
                                        </label>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="relative max-w-[160px]">
                                            <input type="time"
                                                   :name="'days[' + index + '][start]'"
                                                   x-model="day.start"
                                                   :disabled="day.is_off"
                                                   class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none disabled:opacity-40 disabled:bg-slate-100 transition">
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="relative max-w-[160px]">
                                            <input type="time"
                                                   :name="'days[' + index + '][end]'"
                                                   x-model="day.end"
                                                   :disabled="day.is_off"
                                                   class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none disabled:opacity-40 disabled:bg-slate-100 transition">
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- تبويب 4: الإشعارات والتذكيرات                          -->
        <!-- ==================================================== -->
        <div x-show="activeTab === 'notifications'" x-transition class="space-y-6" style="display: none;">
            <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
                <!-- رأس الصندوق -->
                <div class="flex items-center space-x-3.5 space-x-reverse pb-5 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                        🔔
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900">قنوات الإشعارات والتنبيهات التلقائية</h2>
                        <p class="text-xs text-slate-400 mt-0.5">التحكم في رسائل واتساب والتنبيهات البريدية للأطباء والمرضى</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- مفتاح واتساب -->
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 transition hover:bg-white hover:border-[#1E40AF]/20">
                        <div class="space-y-0.5 pr-2">
                            <span class="block text-xs font-bold text-slate-800">تفعيل رسائل تذكير الواتساب التلقائية (WhatsApp Reminder)</span>
                            <span class="text-[11px] text-slate-400">إرسال تنبيه للمريض بتفاصيل الموعد واسم الطبيب والعيادة قبل الموعد المحدد</span>
                        </div>
                        <input type="hidden" name="whatsapp_reminder" :value="whatsappReminder ? '1' : '0'">
                        <button type="button"
                                @click="whatsappReminder = !whatsappReminder"
                                :class="whatsappReminder ? 'bg-[#87A96B]' : 'bg-slate-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                            <span :class="whatsappReminder ? '-translate-x-5' : 'translate-x-0'"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>

                    <!-- وقت إرسال رسالة التذكير -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <span class="block text-xs font-bold text-slate-800">وقت إرسال رسالة التذكير قبل الموعد:</span>
                            <span class="text-[11px] text-slate-400">الفترة الزمنية لإرسال إشعار التذكير للمريض</span>
                        </div>
                        <div class="w-48">
                            <select name="sms_reminder_hours_before"
                                    class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-white focus:border-[#1E40AF] outline-none">
                                <option value="2" {{ ($settings['sms_reminder_hours_before'] ?? '24') == '2' ? 'selected' : '' }}>قبل ساعتين</option>
                                <option value="6" {{ ($settings['sms_reminder_hours_before'] ?? '24') == '6' ? 'selected' : '' }}>قبل 6 ساعات</option>
                                <option value="12" {{ ($settings['sms_reminder_hours_before'] ?? '24') == '12' ? 'selected' : '' }}>قبل 12 ساعة</option>
                                <option value="24" {{ ($settings['sms_reminder_hours_before'] ?? '24') == '24' ? 'selected' : '' }}>قبل 24 ساعة (يوم كامل)</option>
                                <option value="48" {{ ($settings['sms_reminder_hours_before'] ?? '24') == '48' ? 'selected' : '' }}>قبل 48 ساعة (يومين)</option>
                            </select>
                        </div>
                    </div>

                    <!-- إلغاء تلقائي بعد 24 ساعة -->
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 transition hover:bg-white hover:border-[#1E40AF]/20">
                        <div class="space-y-0.5 pr-2">
                            <span class="block text-xs font-bold text-slate-800">إلغاء المواعيد المعلقة غير المؤكدة تلقائياً</span>
                            <span class="text-[11px] text-slate-400">إلغاء الحجز المعلق وإتاحة الفترة الزمنية للمرضى الآخرين إذا لم يؤكد المريض حضوره</span>
                        </div>
                        <input type="hidden" name="auto_cancel_24h" :value="autoCancel24h ? '1' : '0'">
                        <button type="button"
                                @click="autoCancel24h = !autoCancel24h"
                                :class="autoCancel24h ? 'bg-[#87A96B]' : 'bg-slate-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                            <span :class="autoCancel24h ? '-translate-x-5' : 'translate-x-0'"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>

                    <!-- إشعار فوري للطبيب -->
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 transition hover:bg-white hover:border-[#1E40AF]/20">
                        <div class="space-y-0.5 pr-2">
                            <span class="block text-xs font-bold text-slate-800">إشعار بريد إلكتروني فوري للطبيب الاستشاري</span>
                            <span class="text-[11px] text-slate-400">إرسال بريد إلكتروني للطبيب فور إضافة موعد جديد أو تعديله في جدول مواعيده</span>
                        </div>
                        <input type="hidden" name="dentist_email_notification" :value="dentistEmailNotification ? '1' : '0'">
                        <button type="button"
                                @click="dentistEmailNotification = !dentistEmailNotification"
                                :class="dentistEmailNotification ? 'bg-[#87A96B]' : 'bg-slate-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                            <span :class="dentistEmailNotification ? '-translate-x-5' : 'translate-x-0'"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>

                    <!-- إشعار تأكيد الموعد للمريض -->
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 transition hover:bg-white hover:border-[#1E40AF]/20">
                        <div class="space-y-0.5 pr-2">
                            <span class="block text-xs font-bold text-slate-800">إرسال رسالة تأكيد الحجز للمريض فور اعتماده</span>
                            <span class="text-[11px] text-slate-400">إشعار المريض برسالة فور قيام الإدارة أو الاستقبال بتأكيد موعده</span>
                        </div>
                        <input type="hidden" name="patient_confirmation_sms" :value="patientConfirmationSms ? '1' : '0'">
                        <button type="button"
                                @click="patientConfirmationSms = !patientConfirmationSms"
                                :class="patientConfirmationSms ? 'bg-[#87A96B]' : 'bg-slate-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                            <span :class="patientConfirmationSms ? '-translate-x-5' : 'translate-x-0'"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- تبويب 5: الموقع والواجهة العامة                       -->
        <!-- ==================================================== -->
        <div x-show="activeTab === 'website'" x-transition class="space-y-6" style="display: none;">
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
        </div>

        <!-- ==================================================== -->
        <!-- تبويب 8: الأمان وسجل النظام                           -->
        <!-- ==================================================== -->
        <div x-show="activeTab === 'security'" x-transition class="space-y-6" style="display: none;">
            <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
                <!-- رأس الصندوق -->
                <div class="flex items-center space-x-3.5 space-x-reverse pb-5 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                        🔒
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900">إعدادات الأمان وحماية الجلسات</h2>
                        <p class="text-xs text-slate-400 mt-0.5">ضوابط مدة الجلسة، الحماية من هجمات التخمين، وتسجيل الحسابات الجديدة</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- مدة الجلسة -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">مدة انتهاء جلسة المستخدم (بالدقائق):</label>
                        <input type="number"
                               name="session_lifetime_minutes"
                               min="15" max="1440"
                               value="{{ old('session_lifetime_minutes', $settings['session_lifetime_minutes'] ?? '120') }}"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">
                        <span class="text-[10px] text-slate-400 mt-1 block">تسجيل خروج تلقائي بعد فترة الخمول (افتراضي: 120 دقيقة)</span>
                    </div>

                    <!-- أقصى محاولات تسجيل الدخول -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">الحد الأقصى لمحاولات الدخول الخاطئة (Rate Limit):</label>
                        <input type="number"
                               name="max_login_attempts"
                               min="3" max="20"
                               value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? '5') }}"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition text-slate-800">
                        <span class="text-[10px] text-slate-400 mt-1 block">قفل المحاولة مؤقتاً عند تجاوز الحد لمنع التخمين</span>
                    </div>
                </div>

                <div class="space-y-4 pt-3 border-t border-slate-100">
                    <!-- مفتاح تسجيل الحسابات الجديدة -->
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 transition hover:bg-white hover:border-[#1E40AF]/20">
                        <div class="space-y-0.5 pr-2">
                            <span class="block text-xs font-bold text-slate-800">السماح بتسجيل الحسابات الجديدة للمرضى ذاتياً (Self Registration)</span>
                            <span class="text-[11px] text-slate-400">إتاحة إنشاء حساب مريض جديد من صفحة تسجيل الدخول، وعند التعطيل يتم إنشاء الحسابات عبر الاستقبال فقط</span>
                        </div>
                        <input type="hidden" name="allow_user_registration" :value="allowUserRegistration ? '1' : '0'">
                        <button type="button"
                                @click="allowUserRegistration = !allowUserRegistration"
                                :class="allowUserRegistration ? 'bg-[#87A96B]' : 'bg-slate-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                            <span :class="allowUserRegistration ? '-translate-x-5' : 'translate-x-0'"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>

                    <!-- تسجيل العمليات ومحاولات الدخول -->
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 transition hover:bg-white hover:border-[#1E40AF]/20">
                        <div class="space-y-0.5 pr-2">
                            <span class="block text-xs font-bold text-slate-800">تسجيل محاولات الدخول الفاشلة والعمليات الحساسة في السجل</span>
                            <span class="text-[11px] text-slate-400">توثيق بيانات عنوان IP والوقت عند حدوث محاولة دخول غير مصرح بها</span>
                        </div>
                        <input type="hidden" name="log_failed_logins" :value="logFailedLogins ? '1' : '0'">
                        <button type="button"
                                @click="logFailedLogins = !logFailedLogins"
                                :class="logFailedLogins ? 'bg-[#87A96B]' : 'bg-slate-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                            <span :class="logFailedLogins ? '-translate-x-5' : 'translate-x-0'"
                                  class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>
                </div>

                <!-- بطاقة بيئة النظام والتشغيل -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 mt-6 space-y-3">
                    <span class="text-xs font-black text-slate-800 block">بيانات بيئة التشغيل والخادم:</span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="bg-white p-3 rounded-xl border border-slate-200">
                            <span class="text-[10px] text-slate-400 block">إصدار PHP:</span>
                            <span class="font-black text-slate-800">{{ $systemInfo['php_version'] }}</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-200">
                            <span class="text-[10px] text-slate-400 block">إصدار Laravel:</span>
                            <span class="font-black text-[#1E40AF]">{{ $systemInfo['laravel_version'] }}</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-200">
                            <span class="text-[10px] text-slate-400 block">محرك البيانات:</span>
                            <span class="font-black text-slate-800 uppercase">{{ $systemInfo['database_driver'] }}</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-200">
                            <span class="text-[10px] text-slate-400 block">حالة التطبيق:</span>
                            <span class="font-black text-[#87A96B]">{{ $systemInfo['app_env'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة الانتقال السريع لسجل النظام والأدوات المتقدمة -->
            <div class="bg-blue-50/70 border border-blue-200 rounded-3xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#1E40AF] flex items-center justify-center font-bold text-lg">
                        📋
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">سجل أحداث وعمليات النظام والجلسات النشطة</h3>
                        <p class="text-xs text-slate-500">يتضمن سجل كامل بالأخطاء والأحداث المسجلة، تفريغ الكاش، ومراقبة الجلسات المباشرة</p>
                    </div>
                </div>
                <a href="{{ route('admin.settings.security') }}" class="px-5 py-2.5 rounded-2xl bg-[#1E40AF] text-white hover:bg-blue-800 text-xs font-bold transition shadow-md shadow-blue-900/20 whitespace-nowrap">
                    فتح سجل النظام الكامل ←
                </a>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- شريط الحفظ الثابت (Sticky Save Footer)                   -->
        <!-- يظهر في التبويبات التابعة لهذا النموذج                   -->
        <!-- ======================================================== -->
        <div x-show="['clinic', 'booking', 'hours', 'notifications', 'website', 'security'].includes(activeTab)"
             class="sticky bottom-6 z-20 bg-white/95 backdrop-blur-md rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.12)] flex items-center justify-between gap-4 mt-8">
            <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-medium">
                <svg class="w-4 h-4 text-[#87A96B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>يتم حفظ وتطبيق التغييرات مباشرة في قاعدة البيانات MySQL وتظهر فوراً.</span>
            </div>

            <div class="flex items-center space-x-3 space-x-reverse w-full sm:w-auto justify-end">
                <a href="{{ route('admin.settings.index') }}"
                   class="px-7 py-3 rounded-full text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition shadow-sm text-center">
                    إلغاء التغييرات
                </a>

                <button type="submit"
                        class="px-8 py-3 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition duration-200 shadow-lg shadow-blue-900/20 hover:-translate-y-0.5">
                    حفظ كافة التغييرات ✓
                </button>
            </div>
        </div>

    </form>

    <!-- ======================================================== -->
    <!-- تبويب 6: إدارة الأدوار ومصفوفة الصلاحيات                  -->
    <!-- ======================================================== -->
    <div x-show="activeTab === 'roles'" x-transition class="space-y-6" style="display: none;">

        <!-- بطاقات الأدوار وإحصائيات المستخدمين -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($rolesDefinition as $roleKey => $roleData)
                <div @click="activeRole = '{{ $roleKey }}'"
                     :class="activeRole === '{{ $roleKey }}' ? 'ring-2 ring-[#1E40AF] border-[#1E40AF]/40 bg-blue-50/20' : 'border-slate-100 bg-white hover:border-slate-200'"
                     class="p-6 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border cursor-pointer transition duration-200 flex flex-col justify-between gap-4">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-sm font-black text-slate-900">{{ $roleData['name'] }}</span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black border {{ $roleData['badge_color'] }}">
                                {{ $userCountsByRole[$roleKey] ?? 0 }} مستخدم
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed">{{ $roleData['description'] }}</p>
                    </div>

                    <div class="pt-3 border-t border-slate-100/80 flex items-center justify-between text-xs">
                        <span class="text-[10px] font-bold text-[#1E40AF]" x-show="activeRole === '{{ $roleKey }}'">
                            ✓ الدور المحدد حالياً
                        </span>
                        <span class="text-[10px] text-slate-400" x-show="activeRole !== '{{ $roleKey }}'">
                            انقر لعرض وتعديل الصلاحيات ←
                        </span>
                        <span class="text-[10px] font-bold text-slate-400">
                            {{ count($rolePermissions[$roleKey] ?? []) }} صلاحية
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- نموذج تعديل مصفوفة الصلاحيات للدور المحدد -->
        <form method="POST" action="{{ route('admin.settings.roles.update') }}">
            @csrf

            <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
                <!-- ترويسة الدور المحدد -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-100">
                    <div class="flex items-center space-x-3.5 space-x-reverse">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                            🛡️
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-black text-slate-900">مصفوفة صلاحيات:</h2>
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-[#1E40AF] text-white"
                                      x-text="activeRole === 'admin' ? 'مدير النظام' : (activeRole === 'dentist' ? 'طبيب الأسنان' : (activeRole === 'receptionist' ? 'موظف الاستقبال' : 'المريض'))">
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">تفعيل أو تعطيل صلاحيات الوصول للوحدات البرمجية لهذا الدور في النظام</p>
                        </div>
                    </div>

                    <div x-show="activeRole === 'admin'" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>صلاحيات مدير النظام الأساسية محمية ولا يمكن إلغاؤها</span>
                    </div>
                </div>

                <!-- قائمة الصلاحيات المقسمة حسب الوحدات -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($permissionModules as $modKey => $module)
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
                            <div class="flex items-center space-x-2 space-x-reverse pb-2 border-b border-slate-200">
                                <span class="text-lg">{{ $module['icon'] }}</span>
                                <span class="text-xs font-black text-slate-900">{{ $module['title'] }}</span>
                            </div>

                            <div class="space-y-2.5">
                                @foreach($module['permissions'] as $permKey => $permLabel)
                                    @php
                                        $isCoreAdmin = ($permKey === 'dashboard.view' || $permKey === 'settings.manage_permissions' || $permKey === 'settings.view' || $permKey === 'settings.edit' || $permKey === 'users.view' || $permKey === 'users.edit' || $permKey === 'users.assign_roles');
                                    @endphp
                                    <div class="flex items-start gap-3 p-2 rounded-xl bg-white border border-slate-100 hover:border-blue-200 transition">
                                        <!-- For each role, keep hidden inputs when inactive -->
                                        @foreach(['admin', 'dentist', 'receptionist', 'patient'] as $rKey)
                                            <div x-show="activeRole === '{{ $rKey }}'" class="pt-0.5 flex-shrink-0">
                                                @if($rKey === 'admin' && $isCoreAdmin)
                                                    <input type="checkbox"
                                                           checked
                                                           disabled
                                                           class="rounded border-slate-300 text-[#1E40AF] w-4 h-4 cursor-not-allowed opacity-80">
                                                    <input type="hidden" name="permissions[admin][]" value="{{ $permKey }}">
                                                @else
                                                    <input type="checkbox"
                                                           name="permissions[{{ $rKey }}][]"
                                                           value="{{ $permKey }}"
                                                           {{ in_array($permKey, $rolePermissions[$rKey] ?? []) ? 'checked' : '' }}
                                                           class="rounded border-slate-300 text-[#1E40AF] focus:ring-[#1E40AF] w-4 h-4 cursor-pointer">
                                                @endif
                                            </div>
                                        @endforeach

                                        <div class="flex-1 min-w-0">
                                            <span class="block text-xs font-bold text-slate-800">{{ $permLabel }}</span>
                                            <code class="text-[10px] text-slate-400 font-mono" dir="ltr">{{ $permKey }}</code>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- زر حفظ الصلاحيات -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">يتم تطبيق تعديل الصلاحيات فوراً على جميع جلسات الموظفين.</span>
                    <button type="submit"
                            class="px-8 py-3 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition duration-200 shadow-lg shadow-blue-900/20">
                        حفظ مصفوفة الصلاحيات ✓
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ======================================================== -->
    <!-- تبويب 7: إدارة حسابات المستخدمين والموظفين                 -->
    <!-- ======================================================== -->
    <div x-show="activeTab === 'users'" x-transition class="space-y-6" style="display: none;">
        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
            <!-- رأس الصندوق مع فلتر البحث -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center space-x-3.5 space-x-reverse">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                        👥
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900">إدارة حسابات المستخدمين وطاقم العمل</h2>
                        <p class="text-xs text-slate-400 mt-0.5">التحكم في أدوار الحسابات، تفعيل وتعطيل المستخدمين، وإعادة تعيين كلمات المرور</p>
                    </div>
                </div>

                <!-- فورم الفلترة والبحث -->
                <form method="GET" action="{{ route('admin.settings.index') }}" class="flex flex-wrap items-center gap-2.5">
                    <input type="hidden" name="tab" value="users">

                    <!-- حقل البحث -->
                    <div class="relative">
                        <input type="text"
                               name="search"
                               value="{{ $search }}"
                               placeholder="بحث بالاسم، البريد، أو الهاتف..."
                               class="text-xs font-bold rounded-2xl border border-slate-200 py-2.5 pr-9 pl-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition w-60">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                    </div>

                    <!-- فلتر الدور -->
                    <select name="role_filter" class="text-xs font-bold rounded-2xl border border-slate-200 py-2.5 px-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                        <option value="">كافة الأدوار</option>
                        <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>مدير النظام</option>
                        <option value="dentist" {{ $roleFilter === 'dentist' ? 'selected' : '' }}>طبيب أسنان</option>
                        <option value="receptionist" {{ $roleFilter === 'receptionist' ? 'selected' : '' }}>موظف استقبال</option>
                        <option value="patient" {{ $roleFilter === 'patient' ? 'selected' : '' }}>مريض</option>
                    </select>

                    <!-- فلتر الحالة -->
                    <select name="status_filter" class="text-xs font-bold rounded-2xl border border-slate-200 py-2.5 px-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                        <option value="">كافة الحالات</option>
                        <option value="1" {{ $statusFilter === '1' ? 'selected' : '' }}>الحسابات النشطة</option>
                        <option value="0" {{ $statusFilter === '0' ? 'selected' : '' }}>الحسابات المعطلة</option>
                    </select>

                    <button type="submit" class="px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition">
                        تصفية
                    </button>
                    @if($search || $roleFilter || $statusFilter !== '')
                        <a href="{{ route('admin.settings.index', ['tab' => 'users']) }}" class="p-2.5 rounded-2xl text-xs font-bold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition" title="إلغاء التصفية">
                            ✕
                        </a>
                    @endif
                </form>
            </div>

            <!-- جدول المستخدمين -->
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#F8FAFC] text-slate-900 font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 rounded-r-xl">المستخدم</th>
                            <th class="px-5 py-3.5">البريد الإلكتروني والهاتف</th>
                            <th class="px-5 py-3.5">الدور الحالي</th>
                            <th class="px-5 py-3.5">حالة الحساب</th>
                            <th class="px-5 py-3.5">تاريخ الإنشاء</th>
                            <th class="px-5 py-3.5 rounded-l-xl text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-blue-50/20 transition">
                                <!-- الاسم -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-blue-50 text-[#1E40AF] flex items-center justify-center font-black text-xs flex-shrink-0">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block text-sm">{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="inline-block text-[10px] font-bold text-[#1E40AF] bg-blue-50 px-2 py-0.5 rounded-full mt-0.5">حسابك الحالي (أنت)</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- الاتصال -->
                                <td class="px-5 py-4 text-slate-600">
                                    <span class="font-mono text-xs block text-slate-800">{{ $user->email }}</span>
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $user->phone ?? 'لا يوجد هاتف' }}</span>
                                </td>

                                <!-- الدور -->
                                <td class="px-5 py-4">
                                    @php
                                        $badgeColor = match($user->role) {
                                            'admin'        => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            'dentist'      => 'bg-blue-50 text-[#1E40AF] border-blue-200',
                                            'receptionist' => 'bg-[#87A96B]/15 text-[#557143] border-[#87A96B]/30',
                                            default        => 'bg-slate-100 text-slate-600 border-slate-200',
                                        };
                                        $roleName = match($user->role) {
                                            'admin'        => 'مدير النظام',
                                            'dentist'      => 'طبيب أسنان',
                                            'receptionist' => 'موظف استقبال',
                                            default        => 'مريض',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black border {{ $badgeColor }}">
                                        {{ $roleName }}
                                    </span>
                                </td>

                                <!-- الحالة -->
                                <td class="px-5 py-4">
                                    @if($user->is_active ?? true)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-[#87A96B]/15 text-[#557143]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#87A96B]"></span> نشط
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> معطل مؤقتاً
                                        </span>
                                    @endif
                                </td>

                                <!-- تاريخ الإنشاء -->
                                <td class="px-5 py-4 text-slate-500 text-[11px]">
                                    {{ $user->created_at ? $user->created_at->format('Y-m-d') : '-' }}
                                    <span class="block text-[10px] text-slate-400">{{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</span>
                                </td>

                                <!-- الإجراءات -->
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- تغيير الدور -->
                                        <button type="button"
                                                @click="roleModalUser = { id: {{ $user->id }}, name: @js($user->name), role: '{{ $user->role }}' }"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-[#1E40AF] border border-slate-200 transition font-bold text-[11px]"
                                                title="تعديل الدور">
                                            تعديل الدور
                                        </button>

                                        <!-- تفعيل / تعطيل -->
                                        <form method="POST" action="{{ route('admin.settings.users.toggle-status', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('هل أنت متأكد من تغيير حالة حساب {{ $user->name }}؟')"
                                                    class="p-1.5 rounded-xl border transition text-[11px] font-bold {{ ($user->is_active ?? true) ? 'bg-slate-50 hover:bg-rose-50 text-slate-700 hover:text-rose-600 border-slate-200' : 'bg-[#87A96B]/15 text-[#557143] border-[#87A96B]/30' }}"
                                                    title="{{ ($user->is_active ?? true) ? 'تعطيل الحساب' : 'تفعيل الحساب' }}">
                                                @if($user->is_active ?? true)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                @endif
                                            </button>
                                        </form>

                                        <!-- إعادة تعيين كلمة المرور -->
                                        <button type="button"
                                                @click="passwordModalUser = { id: {{ $user->id }}, name: @js($user->name) }"
                                                class="p-1.5 rounded-xl bg-slate-50 hover:bg-amber-50 text-slate-700 hover:text-amber-600 border border-slate-200 transition"
                                                title="إعادة تعيين كلمة المرور">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <p class="text-xs font-bold text-slate-500">لم يتم العثور على أي مستخدمين يطابقون معايير البحث.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ترقيم الصفحات -->
            @if($users->hasPages())
                <div class="pt-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- نافذة منبثقة 1: تعديل دور المستخدم (Role Edit Modal)      -->
    <!-- ======================================================== -->
    <div x-show="roleModalUser"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">
        <div @click.outside="roleModalUser = null"
             class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-black text-slate-900">تعديل دور المستخدم</h3>
                <button type="button" @click="roleModalUser = null" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <template x-if="roleModalUser">
                <form :action="'{{ url('admin/settings/users') }}/' + roleModalUser.id + '/role'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <span class="text-xs text-slate-400 block mb-1">المستخدم:</span>
                        <span class="text-sm font-black text-slate-900" x-text="roleModalUser.name"></span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">اختر الدور الجديد:</label>
                        <select name="role" x-model="roleModalUser.role" class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                            <option value="admin">مدير النظام (صلاحيات كاملة)</option>
                            <option value="dentist">طبيب أسنان / استشاري</option>
                            <option value="receptionist">موظف استقبال</option>
                            <option value="patient">مريض / مراجع</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="roleModalUser = null" class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">إلغاء</button>
                        <button type="submit" class="px-6 py-2.5 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 shadow">تحديث الدور ✓</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- نافذة منبثقة 2: إعادة تعيين كلمة المرور (Password Reset)  -->
    <!-- ======================================================== -->
    <div x-show="passwordModalUser"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">
        <div @click.outside="passwordModalUser = null"
             class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-black text-slate-900">إعادة تعيين كلمة المرور</h3>
                <button type="button" @click="passwordModalUser = null" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <template x-if="passwordModalUser">
                <form :action="'{{ url('admin/settings/users') }}/' + passwordModalUser.id + '/reset-password'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <span class="text-xs text-slate-400 block mb-1">المستخدم:</span>
                        <span class="text-sm font-black text-slate-900" x-text="passwordModalUser.name"></span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">كلمة المرور الجديدة (8 أحرف على الأقل):</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">تأكيد كلمة المرور:</label>
                        <input type="password" name="password_confirmation" required minlength="8"
                               class="w-full text-xs font-bold rounded-2xl border border-slate-200 p-3 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="passwordModalUser = null" class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">إلغاء</button>
                        <button type="submit" class="px-6 py-2.5 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 shadow">تعيين كلمة المرور ✓</button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
