<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3 space-x-reverse">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-lg shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-900">الملف الشخصي وإعدادات الحساب</h1>
                <p class="text-xs text-slate-500">تعديل بيانات الحساب وكلمة المرور ومعلومات الأمان</p>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 sm:p-10 bg-white border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)] rounded-3xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-10 bg-white border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)] rounded-3xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-10 bg-white border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)] rounded-3xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>