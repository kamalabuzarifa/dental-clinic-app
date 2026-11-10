<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\UpdateBookingSettingsRequest;
use App\Http\Requests\Admin\Settings\UpdateClinicSettingsRequest;
use App\Http\Requests\Admin\Settings\UpdateNotificationSettingsRequest;
use App\Http\Requests\Admin\Settings\UpdateSecuritySettingsRequest;
use App\Http\Requests\Admin\Settings\UpdateWebsiteSettingsRequest;
use App\Http\Requests\Admin\Settings\UpdateWorkingHoursSettingsRequest;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display the comprehensive system settings page with tabbed modules.
     */
    public function index(Request $request): View
    {
        return view('admin.settings.index', $this->getCommonData($request));
    }

    /**
     * Subpage 1: بيانات المركز والشعار
     */
    public function clinic(Request $request): View
    {
        return view('admin.settings.clinic', $this->getCommonData($request));
    }

    /**
     * Subpage 2: الحجوزات والمواعيد
     */
    public function booking(Request $request): View
    {
        return view('admin.settings.booking', $this->getCommonData($request));
    }

    /**
     * Subpage 3: أوقات العمل والدوام
     */
    public function workingHours(Request $request): View
    {
        return view('admin.settings.working-hours', $this->getCommonData($request));
    }

    /**
     * Subpage 4: الإشعارات والتذكيرات
     */
    public function notifications(Request $request): View
    {
        return view('admin.settings.notifications', $this->getCommonData($request));
    }

    /**
     * Subpage 5: الموقع والواجهة العامة
     */
    public function website(Request $request): View
    {
        return view('admin.settings.website', $this->getCommonData($request));
    }

    /**
     * Subpage 6: الأدوار والصلاحيات
     */
    public function roles(Request $request): View
    {
        return view('admin.settings.roles', $this->getCommonData($request));
    }

    /**
     * Subpage 7: إدارة المستخدمين
     */
    public function users(Request $request): View
    {
        return view('admin.settings.users', $this->getCommonData($request));
    }

    /**
     * Subpage 8: الأمان وسجل النظام
     */
    public function security(Request $request): View
    {
        return view('admin.settings.security', $this->getCommonData($request));
    }

    /**
     * Prepare all common data required across settings pages.
     */
    private function getCommonData(?Request $request = null): array
    {
        $settings = Setting::getAll();

        // 1. Default Working Hours (7 days)
        $defaultDays = [
            ['name' => 'السبت', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الأحد', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الاثنين', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الثلاثاء', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الأربعاء', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الخميس', 'is_off' => false, 'start' => '09:00', 'end' => '21:00'],
            ['name' => 'الجمعة', 'is_off' => true, 'start' => '14:00', 'end' => '20:00'],
        ];

        $workingHours = !empty($settings['working_hours'])
            ? json_decode($settings['working_hours'], true)
            : $defaultDays;

        // 2. Roles Catalog & Counts
        $rolesDefinition = [
            'admin' => [
                'name' => 'مدير النظام',
                'description' => 'صلاحيات كاملة وغير مقيدة لإدارة كافة جوانب المركز، الإعدادات، والمستخدمين.',
                'badge_color' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            ],
            'dentist' => [
                'name' => 'طبيب أسنان / استشاري',
                'description' => 'الوصول إلى جدول المواعيد السريرية، ملفات المرضى، وسجلات العلاج.',
                'badge_color' => 'bg-blue-50 text-[#1E40AF] border-blue-200',
            ],
            'receptionist' => [
                'name' => 'موظف الاستقبال',
                'description' => 'إدارة الحجوزات اليومية، تأكيد واستقبال المرضى، وتسجيل الملفات الجديدة.',
                'badge_color' => 'bg-[#87A96B]/15 text-[#557143] border-[#87A96B]/30',
            ],
            'patient' => [
                'name' => 'مريض / مراجع',
                'description' => 'حجز المواعيد إلكترونياً، متابعة حالة الحجوزات، واستعراض الخدمات.',
                'badge_color' => 'bg-slate-100 text-slate-700 border-slate-200',
            ],
        ];

        $userCountsByRole = User::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        // 3. Permission Modules Catalog
        $permissionModules = [
            'dashboard' => [
                'title' => 'لوحة التحكم والمؤشرات',
                'icon' => '📊',
                'permissions' => [
                    'dashboard.view' => 'عرض لوحة التحكم والإحصائيات السريرية والمالية',
                ],
            ],
            'dentists' => [
                'title' => 'إدارة الأطباء والاستشاريين',
                'icon' => '👨‍⚕️',
                'permissions' => [
                    'dentists.view' => 'عرض قائمة طاقم الأطباء',
                    'dentists.create' => 'إضافة طبيب استشاري جديد',
                    'dentists.edit' => 'تعديل بيانات الطبيب والتخصص',
                    'dentists.delete' => 'حذف سجل طبيب من المنظومة',
                ],
            ],
            'patients' => [
                'title' => 'إدارة المرضى والمراجعين',
                'icon' => '👥',
                'permissions' => [
                    'patients.view' => 'عرض قائمة وسجلات المرضى',
                    'patients.create' => 'تسجيل ملف مريض جديد',
                    'patients.edit' => 'تعديل البيانات الشخصية للمريض',
                    'patients.delete' => 'حذف ملف مريض من النظام',
                    'patients.view_medical_records' => 'الاطلاع على السجل الطبي والتشخيص السريري',
                ],
            ],
            'appointments' => [
                'title' => 'إدارة الحجوزات والمواعيد',
                'icon' => '📅',
                'permissions' => [
                    'appointments.view' => 'استعراض جدول المواعيد والتقويم',
                    'appointments.create' => 'حجز موعد جديد لمريض',
                    'appointments.edit' => 'تعديل توقيت أو تفاصيل الموعد',
                    'appointments.cancel' => 'إلغاء الموعد وإشعار المريض',
                    'appointments.delete' => 'حذف سجل الموعد نهائياً',
                    'appointments.confirm' => 'تأكيد الحجوزات المعلقة وقيد الانتظار',
                    'appointments.change_status' => 'تحديث الحالة السريرية (مكتمل / غياب / حضور)',
                ],
            ],
            'services' => [
                'title' => 'الخدمات الطبية والأسعار',
                'icon' => '🦷',
                'permissions' => [
                    'services.view' => 'استعراض قائمة الخدمات الطبية',
                    'services.create' => 'إضافة خدمة أو إجراء علاجي جديد',
                    'services.edit' => 'تعديل الخدمة والمدة الزمنية',
                    'services.delete' => 'حذف خدمة طبية من القائمة',
                    'services.manage_prices' => 'تعديل أسعار الخدمات وتطبيق الخصومات',
                ],
            ],
            'reports' => [
                'title' => 'التقارير والإحصائيات المالية',
                'icon' => '📈',
                'permissions' => [
                    'reports.view' => 'استعراض التقارير الدورية ومؤشرات الأداء',
                    'reports.export' => 'تصدير وطباعة التقارير السريرية والمالية',
                ],
            ],
            'users' => [
                'title' => 'إدارة المستخدمين والموظفين',
                'icon' => '🔐',
                'permissions' => [
                    'users.view' => 'عرض قائمة حسابات المستخدمين والموظفين',
                    'users.create' => 'إنشاء حساب موظف أو مستخدم جديد',
                    'users.edit' => 'تعديل بيانات الحساب والحالة',
                    'users.delete' => 'تعطيل وحذف حسابات المستخدمين',
                    'users.assign_roles' => 'تعيين وتغيير الأدوار والصلاحيات',
                ],
            ],
            'settings' => [
                'title' => 'إعدادات النظام والأمان',
                'icon' => '⚙️',
                'permissions' => [
                    'settings.view' => 'عرض إعدادات وهوية المركز',
                    'settings.edit' => 'تعديل بيانات العيادة وأوقات العمل',
                    'settings.manage_permissions' => 'إدارة وتخصيص مصفوفة الصلاحيات للأدوار',
                ],
            ],
        ];

        // 4. Stored or Default Role Permissions
        $storedRolePerms = Setting::get('role_permissions');
        if (is_string($storedRolePerms)) {
            $storedRolePerms = json_decode($storedRolePerms, true);
        }
        $rolePermissions = is_array($storedRolePerms) ? $storedRolePerms : User::defaultRolePermissions();

        // 5. Users List with Search, Filter & Pagination
        $search = $request ? trim($request->get('search', '')) : '';
        $roleFilter = $request ? trim($request->get('role_filter', '')) : '';
        $statusFilter = $request ? $request->get('status_filter', '') : '';

        $usersQuery = User::query()
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($roleFilter, function ($q, $roleFilter) {
                $q->where('role', $roleFilter);
            })
            ->when($statusFilter !== '' && $statusFilter !== null, function ($q) use ($statusFilter) {
                $q->where('is_active', $statusFilter === '1');
            })
            ->orderByDesc('created_at');

        $users = $usersQuery->paginate(12)->withQueryString();

        // Total active admins check
        $activeAdminsCount = User::where('role', 'admin')->where('is_active', true)->count();

        // 6. System & Security Environment Stats
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_driver' => DB::connection()->getDriverName(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP CLI / Web Server',
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug') ? 'مفعل (Debug ON)' : 'معطل (Production Mode)',
            'timezone' => config('app.timezone'),
            'session_lifetime' => Setting::get('session_lifetime_minutes', config('session.lifetime', '120')),
            'max_login_attempts' => Setting::get('max_login_attempts', '5'),
            'allow_registration' => Setting::get('allow_user_registration', '1') === '1',
        ];

        // 7. Active Sessions & System Audit Logs
        try {
            $activeSessions = DB::table('sessions')
                ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
                ->select('sessions.*', 'users.name as user_name', 'users.email as user_email', 'users.role as user_role')
                ->orderByDesc('last_activity')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) {
            $activeSessions = collect();
        }

        $logPath = storage_path('logs/laravel.log');
        $systemLogs = [];
        $logFileSize = '0 KB';
        if (file_exists($logPath)) {
            $logFileSize = round(filesize($logPath) / 1024, 2) . ' KB';
            $logContent = file_get_contents($logPath);
            preg_match_all('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+([a-zA-Z0-9_\-]+)\.([A-Z]+):\s+(.*?)(?=\n\[\d{4}-\d{2}-\d{2}|\z)/ms', $logContent, $matches, PREG_SET_ORDER);
            
            $matches = array_reverse($matches);
            $recent = array_slice($matches, 0, 25);

            foreach ($recent as $m) {
                $systemLogs[] = [
                    'date'        => $m[1],
                    'environment' => $m[2],
                    'level'       => $m[3],
                    'message'     => \Illuminate\Support\Str::limit(trim($m[4]), 250),
                    'full'        => trim($m[4]),
                ];
            }
        }

        // Audit Logs (Database-backed permanent audit trail)
        $auditQuery = AuditLog::query()->with('user');
        if ($request) {
            if ($q = trim($request->input('q', ''))) {
                $auditQuery->where(function ($w) use ($q) {
                    $w->where('description', 'like', "%{$q}%")
                      ->orWhere('user_name', 'like', "%{$q}%")
                      ->orWhere('ip_address', 'like', "%{$q}%")
                      ->orWhere('action', 'like', "%{$q}%");
                });
            }

            if ($action = $request->input('action')) {
                $auditQuery->where('action', $action);
            }

            if ($userId = $request->input('user_id')) {
                $auditQuery->where('user_id', $userId);
            }

            if ($dateFrom = $request->input('date_from')) {
                $auditQuery->whereDate('created_at', '>=', $dateFrom);
            }

            if ($dateTo = $request->input('date_to')) {
                $auditQuery->whereDate('created_at', '<=', $dateTo);
            }
        }

        $auditLogs = $auditQuery->orderByDesc('id')->paginate(15)->withQueryString();
        $auditActions = AuditLog::distinct()->pluck('action')->filter()->values();
        $auditUsers = User::orderBy('name')->get(['id', 'name', 'role']);

        // External communication channel configurations
        $mailConfigStatus = [
            'driver'   => config('mail.default', 'log'),
            'host'     => config('mail.mailers.smtp.host', '127.0.0.1'),
            'port'     => config('mail.mailers.smtp.port', 2525),
            'from'     => config('mail.from.address', 'noreply@clinic.com'),
            'is_smtp'  => config('mail.default') === 'smtp',
            'is_log'   => config('mail.default') === 'log',
        ];
        $smsConfigStatus = SmsService::getStatus();

        return compact(
            'settings',
            'workingHours',
            'rolesDefinition',
            'userCountsByRole',
            'permissionModules',
            'rolePermissions',
            'users',
            'activeAdminsCount',
            'systemInfo',
            'activeSessions',
            'systemLogs',
            'logFileSize',
            'search',
            'roleFilter',
            'statusFilter',
            'auditLogs',
            'auditActions',
            'auditUsers',
            'mailConfigStatus',
            'smsConfigStatus'
        );
    }

    /**
     * Dedicated Update: Clinic Information and Logo
     */
    public function updateClinic(UpdateClinicSettingsRequest $request): RedirectResponse
    {
        if ($request->hasFile('clinic_logo')) {
            $oldLogo = Setting::get('clinic_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('clinic_logo')->store('logos', 'public');
            Setting::set('clinic_logo', $path);
        }

        foreach (['clinic_name', 'clinic_phone', 'clinic_email', 'clinic_address'] as $field) {
            Setting::set($field, $request->input($field, ''));
        }

        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('settings_update', "تحديث بيانات المركز والشعار المؤسسي", null, null, $request->except(['_token', 'clinic_logo']));
        Log::info("قام المدير [{$adminName}] بتحديث بيانات المركز والشعار المؤسسي.");

        return redirect()
            ->route('admin.settings.clinic')
            ->with('success', 'تم حفظ بيانات المركز والشعار المؤسسي بنجاح!');
    }

    /**
     * Dedicated Update: Booking Policies and Rules
     */
    public function updateBooking(UpdateBookingSettingsRequest $request): RedirectResponse
    {
        $fields = [
            'default_session_duration',
            'min_advance_booking_hours',
            'appointment_buffer_minutes',
            'max_daily_appointments_per_dentist',
            'cancellation_cutoff_hours',
            'confirmation_cutoff_hours',
        ];

        foreach ($fields as $field) {
            Setting::set($field, $request->input($field, ''));
        }

        Setting::set('prevent_overlapping_appointments', $request->boolean('prevent_overlapping_appointments') ? '1' : '0');
        Setting::set('emergency_immediate_booking', $request->boolean('emergency_immediate_booking') ? '1' : '0');

        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('settings_update', "تحديث ضوابط وسياسات الحجوزات والمواعيد", null, null, $request->except('_token'));
        Log::info("قام المدير [{$adminName}] بتحديث ضوابط وسياسات الحجوزات والمواعيد.");

        return redirect()
            ->route('admin.settings.booking')
            ->with('success', 'تم حفظ وتحديث سياسات الحجوزات والمواعيد بنجاح!');
    }

    /**
     * Dedicated Update: Weekly Working Hours Schedule
     */
    public function updateWorkingHours(UpdateWorkingHoursSettingsRequest $request): RedirectResponse
    {
        $formattedDays = [];
        foreach ($request->input('days', []) as $dayData) {
            $formattedDays[] = [
                'name'   => $dayData['name'] ?? '',
                'is_off' => isset($dayData['is_off']) && ($dayData['is_off'] === '1' || $dayData['is_off'] === true || $dayData['is_off'] === 'true'),
                'start'  => $dayData['start'] ?? '09:00',
                'end'    => $dayData['end'] ?? '21:00',
            ];
        }

        Setting::set('working_hours', json_encode($formattedDays, JSON_UNESCAPED_UNICODE));

        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('settings_update', "تحديث جدول أوقات العمل والدوام الأسبوعي", null, null, ['days' => $formattedDays]);
        Log::info("قام المدير [{$adminName}] بتحديث جدول أوقات العمل والدوام الأسبوعي.");

        return redirect()
            ->route('admin.settings.working-hours')
            ->with('success', 'تم تحديث جدول أوقات العمل والدوام بنجاح!');
    }

    /**
     * Dedicated Update: Notification Channels and Reminders
     */
    public function updateNotifications(UpdateNotificationSettingsRequest $request): RedirectResponse
    {
        Setting::set('sms_reminder_hours_before', $request->input('sms_reminder_hours_before', '24'));
        Setting::set('whatsapp_reminder', $request->boolean('whatsapp_reminder') ? '1' : '0');
        Setting::set('auto_cancel_24h', $request->boolean('auto_cancel_24h') ? '1' : '0');
        Setting::set('dentist_email_notification', $request->boolean('dentist_email_notification') ? '1' : '0');
        Setting::set('patient_confirmation_sms', $request->boolean('patient_confirmation_sms') ? '1' : '0');

        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('settings_update', "تحديث قنوات التذكير وإعدادات الإشعارات", null, null, $request->except('_token'));
        Log::info("قام المدير [{$adminName}] بتحديث قنوات التذكير والإشعارات.");

        return redirect()
            ->route('admin.settings.notifications')
            ->with('success', 'تم تحديث إعدادات الإشعارات وقنوات التذكير بنجاح!');
    }

    /**
     * Test email server connection.
     */
    public function testEmailConnection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_email' => ['required', 'email'],
        ], [
            'test_email.required' => 'يرجى إدخال عنوان البريد الإلكتروني لفحص الاتصال.',
            'test_email.email'    => 'صيغة البريد الإلكتروني غير صالحة.',
        ]);

        $recipient = $validated['test_email'];
        $mailer = config('mail.default', 'log');

        AuditLog::record('test_email', "فحص اتصال البريد الإلكتروني للعنوان [{$recipient}] (المشغل: {$mailer})");

        if ($mailer === 'log') {
            try {
                Mail::raw("رسالة فحص الاتصال وتأكيد جاهزية إشعارات البريد في مركز ريادة لطب وجراحة الأسنان.", function ($message) use ($recipient) {
                    $message->to($recipient)
                            ->subject('فحص اتصال البريد - مركز ريادة لطب وجراحة الأسنان');
                });

                return redirect()
                    ->route('admin.settings.notifications')
                    ->with('warning', "خدمة البريد تعمل حالياً في وضع السجلات (Log Mode). تم تدوين رسالة الاختبار بنجاح في ملفات النظام، ولكنها لم تُرسل لبريد حقيقي لعدم تهيئة بيانات خادم SMTP خارجي.");
            } catch (\Throwable $e) {
                return redirect()
                    ->route('admin.settings.notifications')
                    ->with('error', 'تعذر كتابة رسالة الاختبار: ' . $e->getMessage());
            }
        }

        // When external SMTP provider credentials exist:
        try {
            Mail::raw("رسالة فحص الاتصال وتأكيد جاهزية إشعارات البريد في مركز ريادة لطب وجراحة الأسنان.", function ($message) use ($recipient) {
                $message->to($recipient)
                        ->subject('فحص اتصال البريد - مركز ريادة لطب وجراحة الأسنان');
            });

            return redirect()
                ->route('admin.settings.notifications')
                ->with('success', "تم الاتصال بخادم البريد بنجاح وإرسال رسالة الاختبار إلى [{$recipient}].");
        } catch (\Throwable $e) {
            Log::error("فشل إرسال بريد الاختبار إلى [{$recipient}]: " . $e->getMessage());

            return redirect()
                ->route('admin.settings.notifications')
                ->with('error', "فشل الاتصال بخادم البريد ({$mailer}): " . $e->getMessage());
        }
    }

    /**
     * Test SMS gateway connection.
     */
    public function testSmsConnection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_phone' => ['required', 'string'],
        ], [
            'test_phone.required' => 'يرجى إدخال رقم الهاتف لفحص اتصال الرسائل النصية.',
        ]);

        $result = SmsService::send($validated['test_phone'], 'رسالة فحص الاتصال من مركز ريادة لطب وجراحة الأسنان');

        AuditLog::record('test_sms', "فحص اتصال خدمة الرسائل النصية SMS للرقم [{$validated['test_phone']}]");

        if ($result['success']) {
            return redirect()
                ->route('admin.settings.notifications')
                ->with('success', $result['message']);
        }

        return redirect()
            ->route('admin.settings.notifications')
            ->with('warning', $result['message']);
    }

    /**
     * Dedicated Update: Website and Landing Page Settings
     */
    public function updateWebsite(UpdateWebsiteSettingsRequest $request): RedirectResponse
    {
        $fields = [
            'announcement_bar',
            'social_twitter',
            'social_instagram',
            'social_facebook',
            'social_whatsapp',
        ];

        foreach ($fields as $field) {
            Setting::set($field, $request->input($field, ''));
        }

        Setting::set('maintenance_mode', $request->boolean('maintenance_mode') ? '1' : '0');

        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('settings_update', "تحديث بيانات الموقع والواجهة العامة (وضع الصيانة: " . ($request->boolean('maintenance_mode') ? 'مفعل' : 'معطل') . ")", null, null, $request->except('_token'));
        Log::info("قام المدير [{$adminName}] بتحديث بيانات الموقع والواجهة العامة (وضع الصيانة: " . ($request->boolean('maintenance_mode') ? 'مفعل' : 'معطل') . ").");

        return redirect()
            ->route('admin.settings.website')
            ->with('success', 'تم حفظ وتحديث إعدادات الموقع والواجهة العامة بنجاح!');
    }

    /**
     * Dedicated Update: Security Controls and Session Limits
     */
    public function updateSecurity(UpdateSecuritySettingsRequest $request): RedirectResponse
    {
        Setting::set('session_lifetime_minutes', $request->input('session_lifetime_minutes', '120'));
        Setting::set('max_login_attempts', $request->input('max_login_attempts', '5'));
        Setting::set('allow_user_registration', $request->boolean('allow_user_registration') ? '1' : '0');
        Setting::set('log_failed_logins', $request->boolean('log_failed_logins') ? '1' : '0');

        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('security_update', "تحديث ضوابط الأمان ومهلة الجلسات ومحاولات تسجيل الدخول", null, null, $request->except('_token'));
        Log::info("قام المدير [{$adminName}] بتحديث ضوابط الأمان ومهلة الجلسات.");

        return redirect()
            ->route('admin.settings.security')
            ->with('success', 'تم حفظ ضوابط الأمان وسجل النظام بنجاح!');
    }

    /**
     * Clear system and application cache.
     */
    public function clearCache(): RedirectResponse
    {
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('system_action', "تفريغ الذاكرة المؤقتة (Cache) وكاش الواجهات للنظام");
        Log::info("قام المدير [{$adminName}] بمسح وتفريغ ذاكرة التخزين المؤقت (Cache).");

        return redirect()
            ->route('admin.settings.security')
            ->with('success', 'تم مسح ذاكرة التخزين المؤقت (Cache) وتفريغ الكاش بنجاح!');
    }

    /**
     * Clear application log file.
     */
    public function clearLogs(): RedirectResponse
    {
        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('system_action', "قام المدير [{$adminName}] بمسح ملف سجلات التطبيق النصية (laravel.log). سجل التدقيق الدائم (Audit Logs) محفوظ ومحمي.");
        Log::info("قام المدير [{$adminName}] بطلب تفريغ ملف سجلات النظام.");

        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        return redirect()
            ->route('admin.settings.security')
            ->with('success', 'تم تفريغ ملف سجلات أخطاء وعمليات النظام النصية بنجاح! وسجل التدقيق الدائم محفوظ في قاعدة البيانات.');
    }

    /**
     * Store or update settings in database.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            // Basic Info
            'clinic_name'             => ['nullable', 'string', 'max:255'],
            'clinic_phone'            => ['nullable', 'string', 'max:50'],
            'clinic_email'            => ['nullable', 'email', 'max:255'],
            'clinic_address'          => ['nullable', 'string', 'max:500'],
            'clinic_logo'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],

            // Booking Settings
            'default_session_duration'           => ['nullable', 'in:15,30,45,60,90'],
            'min_advance_booking_hours'          => ['nullable', 'integer', 'min:0', 'max:168'],
            'appointment_buffer_minutes'         => ['nullable', 'integer', 'min:0', 'max:60'],
            'max_daily_appointments_per_dentist' => ['nullable', 'integer', 'min:1', 'max:100'],
            'cancellation_cutoff_hours'          => ['nullable', 'integer', 'min:0', 'max:72'],
            'confirmation_cutoff_hours'          => ['nullable', 'integer', 'min:0', 'max:72'],

            // Notifications
            'sms_reminder_hours_before'          => ['nullable', 'integer', 'min:1', 'max:72'],

            // Website & Social Media
            'announcement_bar'        => ['nullable', 'string', 'max:500'],
            'social_twitter'          => ['nullable', 'string', 'max:255'],
            'social_instagram'        => ['nullable', 'string', 'max:255'],
            'social_facebook'         => ['nullable', 'string', 'max:255'],
            'social_whatsapp'         => ['nullable', 'string', 'max:255'],

            // Security Settings
            'session_lifetime_minutes'=> ['nullable', 'integer', 'min:15', 'max:1440'],
            'max_login_attempts'      => ['nullable', 'integer', 'min:3', 'max:20'],
        ], [
            'clinic_logo.image' => 'يجب أن يكون ملف الشعار صورة صحيحة.',
            'clinic_logo.max'   => 'الحد الأقصى لحجم الشعار هو 2 ميجابايت.',
        ]);

        // 1. معالجة رفع الشعار (Logo Upload)
        if ($request->hasFile('clinic_logo')) {
            $oldLogo = Setting::get('clinic_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('clinic_logo')->store('logos', 'public');
            Setting::set('clinic_logo', $path);
        }

        // 2. معالجة النصوص والحقول العادية
        $textFields = [
            'clinic_name',
            'clinic_phone',
            'clinic_email',
            'clinic_address',
            'default_session_duration',
            'min_advance_booking_hours',
            'appointment_buffer_minutes',
            'max_daily_appointments_per_dentist',
            'cancellation_cutoff_hours',
            'confirmation_cutoff_hours',
            'sms_reminder_hours_before',
            'announcement_bar',
            'social_twitter',
            'social_instagram',
            'social_facebook',
            'social_whatsapp',
            'session_lifetime_minutes',
            'max_login_attempts',
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field, ''));
            }
        }

        // 3. معالجة مفاتيح التبديل (Toggles / Booleans)
        $toggleFields = [
            'maintenance_mode',
            'whatsapp_reminder',
            'auto_cancel_24h',
            'dentist_email_notification',
            'patient_confirmation_sms',
            'prevent_overlapping_appointments',
            'allow_dentist_edit_prices',
            'allow_receptionist_delete_appointments',
            'allow_patient_cancel_12h',
            'emergency_immediate_booking',
            'allow_user_registration',
            'log_failed_logins',
        ];

        foreach ($toggleFields as $toggle) {
            $value = $request->boolean($toggle) ? '1' : '0';
            Setting::set($toggle, $value);
        }

        // 4. معالجة جدول أوقات العمل الرسمية (Working Hours)
        if ($request->has('days') && is_array($request->input('days'))) {
            $formattedDays = [];
            foreach ($request->input('days') as $dayData) {
                $formattedDays[] = [
                    'name'   => $dayData['name'] ?? '',
                    'is_off' => isset($dayData['is_off']) && ($dayData['is_off'] === '1' || $dayData['is_off'] === true || $dayData['is_off'] === 'true'),
                    'start'  => $dayData['start'] ?? '09:00',
                    'end'    => $dayData['end'] ?? '21:00',
                ];
            }
            Setting::set('working_hours', json_encode($formattedDays, JSON_UNESCAPED_UNICODE));
        }

        $activeTab = $request->input('active_tab', 'clinic');
        $redirectMap = [
            'clinic'        => 'admin.settings.clinic',
            'booking'       => 'admin.settings.booking',
            'hours'         => 'admin.settings.working-hours',
            'working-hours' => 'admin.settings.working-hours',
            'notifications' => 'admin.settings.notifications',
            'website'       => 'admin.settings.website',
            'roles'         => 'admin.settings.roles',
            'users'         => 'admin.settings.users',
            'security'      => 'admin.settings.security',
        ];

        $targetRoute = $redirectMap[$activeTab] ?? 'admin.settings.clinic';

        return redirect()
            ->route($targetRoute)
            ->with('success', 'تم حفظ وتحديث إعدادات النظام وتطبيق التغييرات بنجاح!');
    }

    /**
     * Update permissions matrix for all user roles.
     */
    public function updateRolePermissions(Request $request): RedirectResponse
    {
        $permissions = $request->input('permissions', []);

        // Guard: Admin role must always have core permissions to prevent lockout
        $coreAdminPermissions = [
            'dashboard.view',
            'settings.view',
            'settings.edit',
            'settings.manage_permissions',
            'users.view',
            'users.edit',
            'users.assign_roles',
        ];

        if (!isset($permissions['admin']) || !is_array($permissions['admin'])) {
            $permissions['admin'] = [];
        }

        foreach ($coreAdminPermissions as $corePerm) {
            if (!in_array($corePerm, $permissions['admin'], true)) {
                $permissions['admin'][] = $corePerm;
            }
        }

        // Clean & ensure array format
        $cleaned = [];
        foreach (['admin', 'dentist', 'receptionist', 'patient'] as $role) {
            $cleaned[$role] = array_values(array_unique($permissions[$role] ?? []));
        }

        Setting::set('role_permissions', json_encode($cleaned, JSON_UNESCAPED_UNICODE));

        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('roles_update', "تحديث مصفوفة صلاحيات الأدوار في النظام", null, null, ['permissions' => $cleaned]);
        Log::info("قام المدير [{$adminName}] بتحديث مصفوفة صلاحيات الأدوار.");

        return redirect()
            ->route('admin.settings.roles')
            ->with('success', 'تم تحديث مصفوفة الصلاحيات للأدوار بنجاح وتطبيقها فوراً!');
    }

    /**
     * Update a specific user's role.
     */
    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:admin,dentist,receptionist,patient'],
        ], [
            'role.in' => 'الدور المختار غير صالح.',
        ]);

        // Safety rule: Prevent downgrading the last active admin
        if ($user->role === 'admin' && $validated['role'] !== 'admin') {
            $activeAdminsCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($activeAdminsCount <= 1) {
                return back()->with('error', 'لا يمكن تغيير دور هذا المستخدم: يجب أن يبقى على الأقل مدير نظام واحد نشط لحماية النظام.');
            }
        }

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('role_change', "تغيير دور المستخدم [{$user->name}] من [{$oldRole}] إلى [{$validated['role']}]", $user, ['role' => $oldRole], ['role' => $validated['role']]);
        Log::info("قام المدير [{$adminName}] بتغيير دور المستخدم [{$user->name}] من [{$oldRole}] إلى [{$validated['role']}].");

        return redirect()
            ->route('admin.settings.users')
            ->with('success', "تم تعديل دور المستخدم ({$user->name}) بنجاح إلى: " . $validated['role']);
    }

    /**
     * Toggle a user's active status (activate/suspend).
     */
    public function toggleUserStatus(User $user): RedirectResponse
    {
        // Safety rule 1: Current logged-in user cannot deactivate their own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك تعطيل حسابك الشخصي أثناء تسجيل الدخول به.');
        }

        // Safety rule 2: Cannot deactivate the last active admin
        if ($user->role === 'admin' && $user->is_active) {
            $activeAdminsCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($activeAdminsCount <= 1) {
                return back()->with('error', 'لا يمكن تعطيل هذا الحساب: هو آخر مدير نظام نشط متاح.');
            }
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'تفعيل الحساب' : 'تعطيل الحساب مؤقتاً';

        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('status_toggle', "إجراء [{$statusText}] للمستخدم [{$user->name}]", $user, ['is_active' => !$user->is_active], ['is_active' => $user->is_active]);
        Log::info("قام المدير [{$adminName}] بإجراء [{$statusText}] للمستخدم [{$user->name}].");

        return redirect()
            ->route('admin.settings.users')
            ->with('info', "تم {$statusText} للمستخدم ({$user->name}) بنجاح.");
    }

    /**
     * Reset a user's password securely from admin panel.
     */
    public function resetUserPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'يرجى إدخال كلمة المرور الجديدة.',
            'password.min'       => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        $adminName = auth()->user()?->name ?? 'مدير النظام';
        AuditLog::record('password_reset', "إعادة تعيين كلمة مرور المستخدم [{$user->name}]", $user);
        Log::info("قام المدير [{$adminName}] بإعادة تعيين كلمة مرور المستخدم [{$user->name}].");

        return redirect()
            ->route('admin.settings.users')
            ->with('success', "تمت إعادة تعيين كلمة مرور المستخدم ({$user->name}) بنجاح!");
    }
}
