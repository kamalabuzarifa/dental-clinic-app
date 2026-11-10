@extends('layouts.admin')

@section('title', 'أوقات العمل والدوام - إعدادات النظام')
@section('page_title', 'أوقات العمل والدوام')

@section('content')
<script>
    function workingHoursSettingsHub() {
        return {
            days: @js($workingHours)
        };
    }
</script>

<div class="space-y-6" x-data="workingHoursSettingsHub()">
    <!-- شريط التبويبات العلوي -->
    @include('admin.settings.partials.tabs')

    <form method="POST" action="{{ route('admin.settings.working-hours.update') }}">
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

        <!-- شريط الحفظ الثابت (Sticky Save Footer) -->
        <div class="sticky bottom-6 z-20 bg-white/95 backdrop-blur-md rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.12)] flex items-center justify-between gap-4 mt-8">
            <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 font-medium">
                <svg class="w-4 h-4 text-[#87A96B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>يتم اعتماد أوقات العمل في نظام الحجز المباشر فور الحفظ.</span>
            </div>

            <div class="flex items-center space-x-3 space-x-reverse w-full sm:w-auto justify-end">
                <button type="submit"
                        class="px-8 py-3 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition duration-200 shadow-lg shadow-blue-900/20 hover:-translate-y-0.5">
                    حفظ جدول أوقات العمل ✓
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
