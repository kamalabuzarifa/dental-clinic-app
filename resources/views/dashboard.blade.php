<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            لوحة التحكم
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 text-slate-900 text-center">
                <p class="font-bold text-base mb-2">مرحباً بك، {{ Auth::user()->name }}!</p>
                <p class="text-xs text-slate-500">جاري توجيهك إلى لوحة التحكم الخاصة بدورك...</p>
            </div>
        </div>
    </div>
</x-app-layout>
