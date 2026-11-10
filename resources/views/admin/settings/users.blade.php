@extends('layouts.admin')

@section('title', 'إدارة المستخدمين - إعدادات النظام')
@section('page_title', 'إدارة المستخدمين')

@section('content')
<script>
    function usersSettingsHub() {
        return {
            roleModalUser: null,
            passwordModalUser: null
        };
    }
</script>

<div class="space-y-6" x-data="usersSettingsHub()">
    <!-- شريط التبويبات العلوي -->
    @include('admin.settings.partials.tabs')

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
            <span class="block">يرجى مراجعة الأخطاء التالية:</span>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
            <form method="GET" action="{{ route('admin.settings.users') }}" class="flex flex-wrap items-center gap-2.5">
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
                    <a href="{{ route('admin.settings.users') }}" class="p-2.5 rounded-2xl text-xs font-bold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition" title="إلغاء التصفية">
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

    <!-- نافذة منبثقة 1: تعديل دور المستخدم (Role Edit Modal) -->
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

    <!-- نافذة منبثقة 2: إعادة تعيين كلمة المرور (Password Reset) -->
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
