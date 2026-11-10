@extends('layouts.receptionist')

@section('title', 'جدول مواعيد الأطباء - التقويم الشهري')

@section('content')
<div class="space-y-6" x-data="{ 
    activeDay: null,
    calendarDays: {{ json_encode($calendarDays) }}
}">

    <!-- الترويسة وأزرار الإجراءات -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(30,64,175,0.03)]">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#87A96B]"></span>
                <span class="text-xs font-black text-[#87A96B] uppercase tracking-wider">جدول العيادة</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">جدول مواعيد الأطباء الشهري</h1>
            <p class="text-xs text-slate-500 mt-1">عرض ديناميكي لمواعيد الأطباء موزعاً حسب أيام الشهر، مع إمكانية الفحص والتأكيد الفوري.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('receptionist.appointments.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200 transition">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <span>عرض الجدول المسطح</span>
            </a>

            <a href="{{ route('receptionist.appointments.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold bg-[#1E40AF] text-white shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>حجز موعد جديد</span>
            </a>
        </div>
    </div>

    <!-- شريط تصفية الشهر والأطباء -->
    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        
        <!-- التنقل بين الشهور -->
        <div class="flex items-center gap-3">
            <a href="{{ route('receptionist.appointments.calendar', ['year' => $prevMonthDate->year, 'month' => $prevMonthDate->month, 'doctor_id' => $selectedDoctor, 'status' => $selectedStatus]) }}" 
               class="p-2.5 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-[#1E40AF] border border-slate-200/80 transition"
               title="الشهر السابق">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            <div class="text-center min-w-[140px]">
                <span class="text-base font-black text-slate-900 block">{{ $monthNameArabic }}</span>
            </div>

            <a href="{{ route('receptionist.appointments.calendar', ['year' => $nextMonthDate->year, 'month' => $nextMonthDate->month, 'doctor_id' => $selectedDoctor, 'status' => $selectedStatus]) }}" 
               class="p-2.5 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-[#1E40AF] border border-slate-200/80 transition"
               title="الشهر القادم">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <a href="{{ route('receptionist.appointments.calendar', ['year' => now()->year, 'month' => now()->month, 'doctor_id' => $selectedDoctor, 'status' => $selectedStatus]) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                الشهر الحالي
            </a>
        </div>

        <!-- فلاتر الطبيب والحالة -->
        <form method="GET" action="{{ route('receptionist.appointments.calendar') }}" class="flex flex-wrap items-center gap-2.5">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="month" value="{{ $month }}">

            <select name="doctor_id" onchange="this.form.submit()" class="text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#1E40AF] outline-none font-medium text-slate-700">
                <option value="">جميع الأطباء</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}" {{ $selectedDoctor == $doc->id ? 'selected' : '' }}>
                        د. {{ $doc->name }}
                    </option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#1E40AF] outline-none font-medium text-slate-700">
                <option value="">جميع الحالات</option>
                <option value="confirmed" {{ $selectedStatus === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>ملغي</option>
            </select>
        </form>

    </div>

    <!-- بطاقات إحصائيات الشهر -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-[10px] font-bold text-slate-400">إجمالي مواعيد الشهر</span>
            <span class="text-lg font-black text-slate-900 block mt-0.5">{{ $monthTotalAppointments }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-[10px] font-bold text-blue-600">مؤكد</span>
            <span class="text-lg font-black text-[#1E40AF] block mt-0.5">{{ $monthConfirmedCount }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-[10px] font-bold text-amber-600">قيد الانتظار</span>
            <span class="text-lg font-black text-amber-600 block mt-0.5">{{ $monthPendingCount }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-[10px] font-bold text-emerald-600">مكتمل</span>
            <span class="text-lg font-black text-emerald-600 block mt-0.5">{{ $monthCompletedCount }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-[10px] font-bold text-rose-600">ملغي</span>
            <span class="text-lg font-black text-rose-600 block mt-0.5">{{ $monthCancelledCount }}</span>
        </div>
    </div>

    <!-- شبكة التقويم 7 أعمدة (السبت إلى الجمعة RTL) -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.03)] overflow-hidden">
        
        <!-- أسماء أيام الأسبوع -->
        <div class="grid grid-cols-7 gap-2.5 text-center text-xs font-black text-slate-500 pb-4 border-b border-slate-100 mb-3">
            <div class="py-1">السبت</div>
            <div class="py-1">الأحد</div>
            <div class="py-1">الإثنين</div>
            <div class="py-1">الثلاثاء</div>
            <div class="py-1">الأربعاء</div>
            <div class="py-1">الخميس</div>
            <div class="py-1 text-rose-500">الجمعة (عطلة)</div>
        </div>

        <!-- خلايا الأيام -->
        <div class="grid grid-cols-7 gap-2.5 text-right text-xs">
            
            <!-- خلايا فارغة قبل بداية الشهر -->
            @for($p = 0; $p < $blankDaysCount; $p++)
                <div class="min-h-[105px] p-2 bg-slate-100/40 rounded-2xl border border-dashed border-slate-200/70 opacity-40"></div>
            @endfor

            <!-- أيام الشهر الفعلية -->
            @foreach($calendarDays as $day)
                <div @click="activeDay = calendarDays.find(d => d.date === '{{ $day['date'] }}')"
                     class="min-h-[105px] p-2.5 rounded-2xl border text-right flex flex-col justify-between cursor-pointer transition duration-200 {{ $day['isToday'] ? 'bg-blue-50/60 border-blue-400 ring-2 ring-blue-500/20 shadow-xs' : 'bg-slate-50/80 border-slate-100 hover:border-[#1E40AF] hover:bg-white hover:shadow-xs' }}">
                    
                    <!-- رأس الخلية -->
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-black text-xs {{ $day['isToday'] ? 'text-blue-700 font-extrabold' : 'text-slate-800' }}">
                            {{ $day['dayNumber'] }}
                        </span>
                        @if($day['isToday'])
                            <span class="text-[9px] font-black bg-blue-600 text-white px-2 py-0.5 rounded-full">اليوم</span>
                        @elseif($day['total'] > 0)
                            <span class="text-[9px] font-bold text-slate-400 bg-white px-1.5 py-0.5 rounded-md border border-slate-200">
                                {{ $day['total'] }}
                            </span>
                        @endif
                    </div>

                    <!-- مؤشرات المواعيد -->
                    <div class="space-y-1 my-auto">
                        @if($day['total'] > 0)
                            @if($day['confirmed'] > 0)
                                <span class="text-[10px] font-bold bg-[#87A96B]/15 text-[#567042] border border-[#87A96B]/30 rounded-lg px-2 py-0.5 block truncate">
                                    {{ $day['confirmed'] }} مؤكد
                                </span>
                            @endif
                            @if($day['pending'] > 0)
                                <span class="text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 rounded-lg px-2 py-0.5 block truncate">
                                    {{ $day['pending'] }} انتظار
                                </span>
                            @endif
                            @if($day['completed'] > 0)
                                <span class="text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200 rounded-lg px-2 py-0.5 block truncate">
                                    {{ $day['completed'] }} مكتمل
                                </span>
                            @endif
                            @if($day['cancelled'] > 0)
                                <span class="text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200 rounded-lg px-2 py-0.5 block truncate">
                                    {{ $day['cancelled'] }} ملغي
                                </span>
                            @endif
                        @else
                            <span class="text-[10px] text-slate-300 block font-normal text-center py-2">
                                -
                            </span>
                        @endif
                    </div>

                    <!-- أسفل الخلية: تفاصيل وحجز -->
                    <div class="pt-1 border-t border-slate-100/80 flex items-center justify-between text-[10px] text-slate-400">
                        <span class="hover:text-blue-700 font-bold transition">تفاصيل</span>
                        <a href="{{ route('receptionist.appointments.create', ['date' => $day['date']]) }}" 
                           @click.stop 
                           class="text-blue-600 hover:text-blue-800 hover:underline font-bold"
                           title="حجز موعد في هذا اليوم">
                            + حجز
                        </a>
                    </div>

                </div>
            @endforeach

        </div>

    </div>

    <!-- نافذة منبثقة لتفاصيل مواعيد اليوم المختار -->
    <div x-show="activeDay" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.away="activeDay = null" 
             class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 max-h-[85vh] flex flex-col text-right">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-black text-slate-900">
                        مواعيد يوم: <span class="text-[#1E40AF]" x-text="activeDay ? activeDay.date : ''"></span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        إجمالي المواعيد المسجلة: <span class="font-bold text-slate-800" x-text="activeDay ? activeDay.total : 0"></span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a :href="'{{ route('receptionist.appointments.create') }}?date=' + (activeDay ? activeDay.date : '')"
                       class="px-4 py-2 rounded-xl text-xs font-bold bg-[#1E40AF] text-white hover:bg-blue-800 transition">
                        + حجز جديد
                    </a>
                    <button @click="activeDay = null" class="p-2 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto my-4 divide-y divide-slate-100">
                <template x-if="activeDay && activeDay.appointments.length === 0">
                    <div class="py-12 text-center text-slate-400 text-xs">
                        لا توجد مواعيد مسجلة في هذا التاريخ.
                    </div>
                </template>

                <template x-if="activeDay && activeDay.appointments.length > 0">
                    <div>
                        <template x-for="apt in activeDay.appointments" :key="apt.id">
                            <div class="py-3.5 flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-slate-900 text-xs" x-text="apt.start_time"></span>
                                        <span class="text-slate-300">-</span>
                                        <span class="font-bold text-slate-900 text-xs" x-text="apt.patient_name"></span>
                                        <span class="text-[10px] text-slate-400" x-text="apt.patient_phone"></span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        <span>د. <strong class="text-slate-700" x-text="apt.dentist_name"></strong></span>
                                        <span class="mx-1.5">•</span>
                                        <span x-text="apt.service_name"></span>
                                    </div>
                                    <div x-show="apt.notes" class="text-[10px] text-slate-400 mt-1 italic" x-text="apt.notes"></div>
                                </div>

                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                                          :class="{
                                              'bg-blue-100 text-blue-700': apt.status === 'confirmed',
                                              'bg-amber-100 text-amber-700': apt.status === 'pending',
                                              'bg-emerald-100 text-emerald-700': apt.status === 'completed',
                                              'bg-rose-100 text-rose-700': apt.status === 'cancelled'
                                          }"
                                          x-text="apt.status"></span>

                                    <a :href="apt.edit_url" class="p-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition" title="تعديل">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button @click="activeDay = null" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                    إغلاق
                </button>
            </div>

        </div>
    </div>

</div>
@endsection
