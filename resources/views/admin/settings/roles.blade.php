@extends('layouts.admin')

@section('title', 'الأدوار والصلاحيات - إعدادات النظام')
@section('page_title', 'الأدوار والصلاحيات')

@section('content')
<script>
    function rolesSettingsHub() {
        return {
            activeRole: 'dentist'
        };
    }
</script>

<div class="space-y-6" x-data="rolesSettingsHub()">
    <!-- شريط التبويبات العلوي -->
    @include('admin.settings.partials.tabs')

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

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 mb-6">
                <span class="block">يرجى مراجعة الأخطاء التالية:</span>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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
@endsection
