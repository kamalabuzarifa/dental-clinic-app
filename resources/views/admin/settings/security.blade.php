@extends('layouts.admin')

@section('title', 'الأمان وسجل النظام - إعدادات النظام')
@section('page_title', 'الأمان وسجل النظام')

@section('content')
<script>
    function securitySettingsHub() {
        return {
            allowUserRegistration: {{ ($settings['allow_user_registration'] ?? '1') == '1' ? 'true' : 'false' }},
            logFailedLogins: {{ ($settings['log_failed_logins'] ?? '1') == '1' ? 'true' : 'false' }},
            activeLogTab: '{{ request()->hasAny(["q", "action", "user_id", "date_from", "date_to", "page"]) ? "logs" : "settings" }}', // 'settings', 'logs', 'sessions'
            expandedLogIndex: null,
            expandedAuditIndex: null,
            toggleLog(index) {
                this.expandedLogIndex = this.expandedLogIndex === index ? null : index;
            },
            toggleAudit(index) {
                this.expandedAuditIndex = this.expandedAuditIndex === index ? null : index;
            }
        };
    }
</script>

<div class="space-y-6" x-data="securitySettingsHub()">
    <!-- شريط التبويبات العلوي للأقسام الثمانية -->
    @include('admin.settings.partials.tabs')

    <!-- التبديل الداخلي داخل صفحة الأمان وسجل النظام -->
    <div class="bg-white rounded-2xl p-1.5 border border-slate-200/80 shadow-sm flex items-center gap-2 max-w-fit">
        <button type="button"
                @click="activeLogTab = 'settings'"
                :class="activeLogTab === 'settings' ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition">
            🔒 ضوابط الأمان والجلسات
        </button>
        <button type="button"
                @click="activeLogTab = 'logs'"
                :class="activeLogTab === 'logs' ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
            📋 سجل التدقيق والعمليات (Audit Logs)
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black"
                  :class="activeLogTab === 'logs' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">
                {{ $auditLogs->total() }}
            </span>
        </button>
        <button type="button"
                @click="activeLogTab = 'sessions'"
                :class="activeLogTab === 'sessions' ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
            💻 الجلسات النشطة
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black"
                  :class="activeLogTab === 'sessions' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">
                {{ count($activeSessions) }}
            </span>
        </button>
    </div>

    <!-- ======================================================== -->
    <!-- القسم 1: نموذج ضوابط الأمان وحماية الجلسات              -->
    <!-- ======================================================== -->
    <div x-show="activeLogTab === 'settings'" class="space-y-6">
        <form method="POST" action="{{ route('admin.settings.security.update') }}">
            @csrf

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 mb-6">
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

            <!-- شريط الحفظ الثابت (Sticky Save Footer) -->
            <div class="sticky bottom-6 z-20 bg-white/95 backdrop-blur-md rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.12)] flex items-center justify-between gap-4 mt-8">
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <svg class="w-4 h-4 text-[#87A96B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>يتم تطبيق إعدادات الأمان ومُدد الجلسات فور الحفظ.</span>
                </div>

                <div class="flex items-center space-x-3 space-x-reverse w-full sm:w-auto justify-end">
                    <button type="submit"
                            class="px-8 py-3 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition duration-200 shadow-lg shadow-blue-900/20 hover:-translate-y-0.5">
                        حفظ إعدادات الأمان وسجل النظام ✓
                    </button>
                </div>
            </div>
        </form>

        <!-- بطاقة أدوات الصيانة السريعة -->
        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                        ⚡
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">أدوات الصيانة السريعة</h3>
                        <p class="text-xs text-slate-400">تنظيف الذاكرة المؤقتة، تفريغ السجلات، وتحسين استجابة المنظومة</p>
                    </div>
                </div>
                <span class="text-xs font-mono text-slate-500">حجم ملف السجل: <strong class="text-slate-800">{{ $logFileSize }}</strong></span>
            </div>

            <div class="flex flex-wrap gap-3">
                <form method="POST" action="{{ route('admin.settings.security.clear-cache') }}" class="inline">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('هل ترغب في مسح وتفريغ كاش النظام والواجهات؟')"
                            class="px-5 py-2.5 rounded-2xl bg-blue-50 text-[#1E40AF] hover:bg-blue-100 border border-blue-200 text-xs font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>تفريغ الكاش المؤقت (Clear Cache)</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.settings.security.clear-logs') }}" class="inline">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('هل أنت متأكد من تفريغ ملف سجلات النظام بالكامل؟')"
                            class="px-5 py-2.5 rounded-2xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 text-xs font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>تفريغ ملف السجلات (Clear Logs)</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- القسم 2: سجل التدقيق والعمليات الدائم (Database Audit Trail) -->
    <!-- ======================================================== -->
    <div x-show="activeLogTab === 'logs'" class="space-y-6" style="display: none;">
        
        <!-- بطاقة السجل الدائم في قاعدة البيانات -->
        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center space-x-3.5 space-x-reverse">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                        🛡️
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-900">سجل التدقيق والعمليات الدائم (Security Audit Trail)</h2>
                        <p class="text-xs text-slate-400 mt-0.5">سجل دائم ومحمي في قاعدة البيانات يوثق كافة العمليات الحساسة، التعديلات، والصلاحيات مع عنوان IP</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                        قاعدة البيانات: {{ $auditLogs->total() }} عملية مسجلة
                    </span>
                </div>
            </div>

            <!-- نموذج البحث والفلترة المتقدمة -->
            <form method="GET" action="{{ route('admin.settings.security') }}" class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                <input type="hidden" name="tab" value="security">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- البحث بالكلمات -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">بحث بالنص / IP</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="ابحث في الوصف أو المستخدم..."
                               class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-white focus:border-[#1E40AF] focus:ring-1 focus:ring-[#1E40AF] outline-none">
                    </div>

                    <!-- تصفية حسب نوع العملية -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">نوع العملية</label>
                        <select name="action" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-white focus:border-[#1E40AF] focus:ring-1 focus:ring-[#1E40AF] outline-none">
                            <option value="">-- كافة العمليات --</option>
                            @foreach($auditActions as $act)
                                <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- تصفية حسب المستخدم -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">المستخدم</label>
                        <select name="user_id" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-white focus:border-[#1E40AF] focus:ring-1 focus:ring-[#1E40AF] outline-none">
                            <option value="">-- كافة المستخدمين --</option>
                            @foreach($auditUsers as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- من تاريخ -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">من تاريخ</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2 bg-white focus:border-[#1E40AF] outline-none">
                    </div>

                    <!-- إلى تاريخ -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">إلى تاريخ</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2 bg-white focus:border-[#1E40AF] outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200/60">
                    @if(request()->hasAny(['q', 'action', 'user_id', 'date_from', 'date_to']))
                        <a href="{{ route('admin.settings.security') }}" 
                           class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:text-slate-700 bg-white border border-slate-200 transition">
                            إلغاء الفلاتر ✕
                        </a>
                    @endif
                    <button type="submit" 
                            class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition shadow-sm">
                        تصفية النتائج
                    </button>
                </div>
            </form>

            <!-- جدول سجل التدقيق الدائم -->
            @if($auditLogs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-[#F8FAFC] text-slate-900 font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 rounded-r-xl">الوقت والتاريخ</th>
                                <th class="px-4 py-3">المستخدم</th>
                                <th class="px-4 py-3">نوع العملية</th>
                                <th class="px-4 py-3">الوصف والتفاصيل</th>
                                <th class="px-4 py-3">عنوان IP</th>
                                <th class="px-4 py-3 rounded-l-xl text-center">البيانات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($auditLogs as $audit)
                                @php
                                    $actionBadge = match($audit->action) {
                                        'login'           => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'logout'          => 'bg-slate-100 text-slate-600 border-slate-200',
                                        'failed_login', 'blocked_login' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'role_change', 'roles_update' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'password_reset'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'status_toggle'   => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                        'delete', 'delete_doctor', 'delete_patient', 'delete_service' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'create', 'booking', 'create_doctor', 'create_patient', 'create_service' => 'bg-blue-50 text-[#1E40AF] border-blue-200',
                                        default           => 'bg-slate-50 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <tr class="hover:bg-blue-50/20 transition">
                                    <td class="px-4 py-3.5 whitespace-nowrap text-slate-500 font-mono text-[11px]" dir="ltr">
                                        {{ $audit->created_at->format('Y-m-d H:i:s') }}
                                        <span class="block text-[10px] text-slate-400">{{ $audit->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-slate-900">{{ $audit->user_name }}</div>
                                        @if($audit->user)
                                            <span class="text-[10px] text-slate-400 font-bold">({{ $audit->user->role }})</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black border {{ $actionBadge }}">
                                            {{ $audit->action }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-800 max-w-sm">
                                        {{ $audit->description }}
                                    </td>
                                    <td class="px-4 py-3.5 font-mono text-[11px] text-slate-600" dir="ltr">
                                        {{ $audit->ip_address ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        @if($audit->new_values || $audit->old_values)
                                            <button type="button" 
                                                    @click="toggleAudit({{ $audit->id }})"
                                                    class="px-2 py-1 rounded-lg text-[10px] font-bold text-[#1E40AF] bg-blue-50 hover:bg-blue-100 transition">
                                                <span x-text="expandedAuditIndex === {{ $audit->id }} ? 'إخفاء' : 'عرض القيم'"></span>
                                            </button>
                                        @else
                                            <span class="text-slate-300 text-[10px]">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($audit->new_values || $audit->old_values)
                                    <tr x-show="expandedAuditIndex === {{ $audit->id }}" x-transition class="bg-slate-50/80">
                                        <td colspan="6" class="p-4 border-t border-slate-100">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-mono" dir="ltr">
                                                @if($audit->old_values)
                                                    <div class="p-3 rounded-xl bg-white border border-slate-200">
                                                        <span class="block text-[11px] font-bold text-slate-500 mb-1">Old Values:</span>
                                                        <pre class="overflow-x-auto text-[11px] text-slate-700">{{ json_encode($audit->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    </div>
                                                @endif
                                                @if($audit->new_values)
                                                    <div class="p-3 rounded-xl bg-white border border-slate-200">
                                                        <span class="block text-[11px] font-bold text-slate-500 mb-1">New Values:</span>
                                                        <pre class="overflow-x-auto text-[11px] text-slate-700">{{ json_encode($audit->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- ترقيم الصفحات (Pagination) -->
                <div class="pt-4 border-t border-slate-100">
                    {{ $auditLogs->links() }}
                </div>
            @else
                <div class="py-12 text-center text-slate-400 space-y-2">
                    <div class="w-12 h-12 mx-auto rounded-full bg-slate-50 text-slate-400 flex items-center justify-center font-bold text-xl">
                        🔍
                    </div>
                    <p class="text-xs font-bold text-slate-600">لم يتم العثور على سجلات تطابق شروط البحث الحالية.</p>
                </div>
            @endif
        </div>

        <!-- الصندوق الثانوي: ملف سجلات الأخطاء النصية (laravel.log) -->
        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center space-x-3.5 space-x-reverse">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-lg flex-shrink-0">
                        📄
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">سجلات التطبيق والأخطاء النصية (storage/logs/laravel.log)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">سجل نصي مؤقت خاص بأخطاء بيئة التطوير واستثناءات PHP. مسحه لا يؤثر على سجل التدقيق الدائم أعلاه.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-mono text-slate-500">حجم الملف: <strong>{{ $logFileSize }}</strong></span>
                    <form method="POST" action="{{ route('admin.settings.security.clear-logs') }}" class="inline">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('هل أنت متأكد من تفريغ ملف السجلات النصية المؤقت؟ هذا لن يؤثر على سجل التدقيق الدائم.')"
                                class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 text-xs font-bold transition">
                            مسح ملف السجلات النصية
                        </button>
                    </form>
                </div>
            </div>

            @if(count($systemLogs) > 0)
                <div class="space-y-3">
                    @foreach($systemLogs as $idx => $log)
                        @php
                            $levelColor = match($log['level']) {
                                'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'WARNING' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'INFO', 'NOTICE' => 'bg-blue-50 text-[#1E40AF] border-blue-200',
                                default => 'bg-slate-50 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <div class="p-4 rounded-2xl border border-slate-100 hover:border-slate-200 bg-slate-50/60 transition space-y-2">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black border uppercase {{ $levelColor }}">
                                        {{ $log['level'] }}
                                    </span>
                                    <span class="text-xs font-mono font-bold text-slate-700" dir="ltr">{{ $log['date'] }}</span>
                                    <span class="text-[10px] font-bold text-slate-400">[{{ $log['environment'] }}]</span>
                                </div>
                                <button type="button"
                                        @click="toggleLog({{ $idx }})"
                                        class="text-[11px] font-bold text-[#1E40AF] hover:underline self-start sm:self-auto">
                                    <span x-text="expandedLogIndex === {{ $idx }} ? 'إخفاء التفاصيل ↑' : 'عرض التفاصيل الكاملة ↓'"></span>
                                </button>
                            </div>

                            <p class="text-xs text-slate-800 font-mono leading-relaxed truncate" dir="ltr">
                                {{ $log['message'] }}
                            </p>

                            <div x-show="expandedLogIndex === {{ $idx }}" x-transition class="pt-2 border-t border-slate-200">
                                <pre class="p-3 rounded-xl bg-slate-900 text-slate-100 text-[11px] font-mono overflow-x-auto max-h-60" dir="ltr">{{ $log['full'] }}</pre>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-slate-400 space-y-1">
                    <p class="text-xs font-bold text-slate-600">ملف السجلات النصية فارغ حالياً.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- القسم 3: الجلسات النشطة والأجهزة المتصلة                -->
    <!-- ======================================================== -->
    <div x-show="activeLogTab === 'sessions'" class="space-y-6" style="display: none;">
        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
            <div class="flex items-center space-x-3.5 space-x-reverse pb-5 border-b border-slate-100">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                    💻
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900">الجلسات النشطة والأجهزة المتصلة</h2>
                    <p class="text-xs text-slate-400 mt-0.5">متابعة حسابات المستخدمين المسجلة وعناوين IP وأوقات النشاط الأخيرة</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#F8FAFC] text-slate-900 font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3.5 rounded-r-xl">المستخدم</th>
                            <th class="px-5 py-3.5">عنوان IP</th>
                            <th class="px-5 py-3.5">المتصفح ونظام التشغيل</th>
                            <th class="px-5 py-3.5 rounded-l-xl">آخر نشاط</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($activeSessions as $session)
                            <tr class="hover:bg-blue-50/20 transition">
                                <td class="px-5 py-4">
                                    @if($session->user_name)
                                        <div class="font-bold text-slate-900">{{ $session->user_name }}</div>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $session->user_email }}</span>
                                    @else
                                        <span class="text-slate-400 font-bold">زائر / غير مسجل</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 font-mono text-slate-700" dir="ltr">
                                    {{ $session->ip_address ?? '127.0.0.1' }}
                                </td>
                                <td class="px-5 py-4 text-slate-600 max-w-xs truncate" dir="ltr" title="{{ $session->user_agent }}">
                                    {{ $session->user_agent ?? 'Unknown Client' }}
                                </td>
                                <td class="px-5 py-4 text-slate-500 font-mono text-[11px]">
                                    {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 font-bold">
                                    لا توجد جلسات مسجلة في جدول الجلسات حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
