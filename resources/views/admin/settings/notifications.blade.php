@extends('layouts.admin')

@section('title', 'الإشعارات والتذكيرات - إعدادات النظام')
@section('page_title', 'الإشعارات والتذكيرات')

@section('content')
<script>
    function notificationSettingsHub() {
        return {
            whatsappReminder: {{ ($settings['whatsapp_reminder'] ?? '1') == '1' ? 'true' : 'false' }},
            autoCancel24h: {{ ($settings['auto_cancel_24h'] ?? '1') == '1' ? 'true' : 'false' }},
            dentistEmailNotification: {{ ($settings['dentist_email_notification'] ?? '1') == '1' ? 'true' : 'false' }},
            patientConfirmationSms: {{ ($settings['patient_confirmation_sms'] ?? '1') == '1' ? 'true' : 'false' }}
        };
    }
</script>

<div class="space-y-6" x-data="notificationSettingsHub()">
    <!-- شريط التبويبات العلوي -->
    @include('admin.settings.partials.tabs')

    <form method="POST" action="{{ route('admin.settings.notifications.update') }}">
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

        <!-- شريط الحفظ الثابت (Sticky Save Footer) -->
        <div class="sticky bottom-6 z-20 bg-white/95 backdrop-blur-md rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.12)] flex items-center justify-between gap-4 mt-8">
            <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-medium">
                <svg class="w-4 h-4 text-[#87A96B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>يتم تطبيق قنوات التنبيه فوراً على المواعيد والعمليات القادمة.</span>
            </div>

            <div class="flex items-center space-x-3 space-x-reverse w-full sm:w-auto justify-end">
                <button type="submit"
                        class="px-8 py-3 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition duration-200 shadow-lg shadow-blue-900/20 hover:-translate-y-0.5">
                    حفظ إعدادات الإشعارات والتنبيهات ✓
                </button>
            </div>
        </div>
    </form>

    <!-- ======================================================== -->
    <!-- قسم أدوات التشخيص وفحص بوابات الإرسال الخارجية            -->
    <!-- ======================================================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        
        <!-- 1. بطاقة فحص اتصال البريد الإلكتروني (SMTP Diagnostics) -->
        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-lg">
                        ✉️
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">حالة خادم البريد (Mail Server)</h3>
                        <p class="text-[11px] text-slate-400">فحص الاتصال وإرسال رسالة بريدية تجريبية</p>
                    </div>
                </div>

                @if(!empty($mailConfigStatus['is_smtp']))
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black border bg-emerald-50 text-emerald-700 border-emerald-200">
                        SMTP جاهز
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black border bg-amber-50 text-amber-700 border-amber-200">
                        وضع السجلات (Log Mode)
                    </span>
                @endif
            </div>

            <!-- معلومات التهيئة الحالية -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span class="text-slate-400">المشغل (Driver):</span>
                    <strong class="font-mono text-slate-800">{{ $mailConfigStatus['driver'] ?? 'log' }}</strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span class="text-slate-400">خادم المضيف (Host):</span>
                    <strong class="font-mono text-slate-800">{{ $mailConfigStatus['host'] ?? '127.0.0.1' }}:{{ $mailConfigStatus['port'] ?? 2525 }}</strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span class="text-slate-400">عنوان المرسل (From):</span>
                    <strong class="font-mono text-slate-800">{{ $mailConfigStatus['from'] ?? 'noreply@clinic.com' }}</strong>
                </div>
            </div>

            @if(empty($mailConfigStatus['is_smtp']))
                <p class="text-[11px] text-amber-700 leading-relaxed bg-amber-50/60 p-3 rounded-xl border border-amber-200/60">
                    ⚠️ خدمة البريد تعمل حالياً بنظام التسجيل المحلي (Log Driver). الرسائل تُكتب في ملفات السجلات ولا تُرسل لبريد خارجي حتى يتم تفعيل إعدادات خادم SMTP في بيئة الإنتاج.
                </p>
            @endif

            <!-- نموذج فحص الإرسال -->
            <form method="POST" action="{{ route('admin.settings.notifications.test-email') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">البريد الإلكتروني للفحص:</label>
                    <input type="email" name="test_email" required value="{{ auth()->user()?->email ?? 'admin@clinic.com' }}"
                           placeholder="name@example.com"
                           class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition">
                </div>
                <button type="submit"
                        class="w-full py-2.5 rounded-xl text-xs font-bold text-[#1E40AF] bg-blue-50 hover:bg-blue-100 border border-blue-200 transition shadow-sm flex items-center justify-center gap-1.5">
                    <span>📨</span>
                    <span>إرسال بريد فحص الاتصال</span>
                </button>
            </form>
        </div>

        <!-- 2. بطاقة فحص اتصال الرسائل النصية القصيرة (SMS Gateway) -->
        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-lg">
                        📱
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">بوابة الرسائل النصية (SMS Gateway)</h3>
                        <p class="text-[11px] text-slate-400">حالة الربط بمزودي الرسائل (Taqnyat / Twilio / Unifonic)</p>
                    </div>
                </div>

                <span class="px-2.5 py-1 rounded-full text-[10px] font-black border {{ $smsConfigStatus['status_class'] ?? 'bg-amber-50 text-amber-700 border-amber-200' }}">
                    {{ $smsConfigStatus['status_label'] ?? 'غير مهيأة' }}
                </span>
            </div>

            <!-- ملاحظة الشفافية والجاهزية -->
            <p class="text-[11px] text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                {{ $smsConfigStatus['note'] ?? '' }}
            </p>

            <!-- نموذج فحص SMS -->
            <form method="POST" action="{{ route('admin.settings.notifications.test-sms') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">رقم الهاتف لفحص الإرسال:</label>
                    <input type="text" name="test_phone" required value="{{ auth()->user()?->phone ?? '0501234567' }}"
                           placeholder="05xxxxxxxx"
                           class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none transition" dir="ltr">
                </div>
                <button type="submit"
                        class="w-full py-2.5 rounded-xl text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition shadow-sm flex items-center justify-center gap-1.5">
                    <span>📲</span>
                    <span>فحص إرسال SMS تجريبي</span>
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
