@extends('layouts.admin')

@section('title', 'إدارة الحجوزات')

@section('content')
<div class="space-y-8" x-data="{ 
    currentTab: '{{ $currentTab }}', 
    activeDay: null,
    calendarDays: {{ json_encode($calendarDays) }}
}">

    <!-- ترويسة الصفحة وتبديل العرض -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">سجل ومواعيد العيادة الشامل</h1>
            <p class="text-xs text-slate-500 mt-1">متابعة دقيقة لجدول حجوزات الأطباء، تأكيد المواعيد، وتحديث الحالات من قاعدة البيانات الفعلية.</p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a href="{{ route('admin.appointments.create') }}" 
               class="inline-flex items-center justify-center px-6 py-2.5 text-xs font-bold text-white transition-all duration-300 bg-[#1E40AF] rounded-full shadow-lg hover:bg-blue-800 hover:shadow-blue-900/20 hover:-translate-y-0.5">
                <span class="ml-1.5 text-base">+</span> حجز موعد جديد
            </a>

            <!-- أزرار التبديل بين الجدول والتقويم -->
            <div class="inline-flex p-1 bg-slate-200/70 rounded-full text-xs font-bold">
                <button type="button"
                        @click="currentTab = 'table'" 
                        :class="currentTab === 'table' ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-5 py-2 rounded-full transition duration-150">
                    قائمة الجدول
                </button>
                <button type="button"
                        @click="currentTab = 'calendar'" 
                        :class="currentTab === 'calendar' ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-5 py-2 rounded-full transition duration-150">
                    عرض التقويم 🗓
                </button>
            </div>
        </div>
    </div>

    <!-- شريط الفلترة المتقدمة (تاريخ، طبيب، حالة) -->
    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6">
        <form method="GET" action="{{ route('admin.appointments.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <input type="hidden" name="tab" :value="currentTab">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="month" value="{{ $month }}">

            <!-- 1. التاريخ -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">تاريخ محدد:</label>
                <input type="date" name="date" value="{{ $selectedDate ?? '' }}" 
                       class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition">
            </div>

            <!-- 2. الطبيب -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">الطبيب المعالج:</label>
                <select name="doctor_id" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition">
                    <option value="">جميع الأطباء</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ ($selectedDoctor ?? '') == $doc->id ? 'selected' : '' }}>
                            {{ $doc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3. الحالة -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">حالة الحجز:</label>
                <select name="status" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-slate-50 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition">
                    <option value="">جميع الحالات</option>
                    <option value="confirmed" {{ ($selectedStatus ?? '') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                    <option value="pending" {{ ($selectedStatus ?? '') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="completed" {{ ($selectedStatus ?? '') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="cancelled" {{ ($selectedStatus ?? '') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>

            <!-- أزرار التطبيق -->
            <div class="flex items-center space-x-2 space-x-reverse">
                <button type="submit" class="flex-1 py-2.5 rounded-full text-xs font-bold bg-[#1E40AF] text-white hover:bg-blue-800 transition shadow-md shadow-blue-900/10">
                    تطبيق الفلترة
                </button>
                <a href="{{ route('admin.appointments.index') }}" class="px-4 py-2.5 rounded-full text-xs font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- 1. عرض جدول الحجوزات التفصيلي -->
    <div x-show="currentTab === 'table'" class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#F8FAFC] text-slate-900 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">المريض</th>
                        <th class="px-6 py-4">الطبيب</th>
                        <th class="px-6 py-4">الخدمة الطبية</th>
                        <th class="px-6 py-4">التاريخ والوقت</th>
                        <th class="px-6 py-4">الحالة</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($appointments as $apt)
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <div class="text-sm">{{ $apt->patient->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-normal">{{ $apt->patient->phone ?? $apt->patient->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                <div>{{ $apt->dentist->name ?? '-' }}</div>
                                <div class="text-[10px] text-[#1E40AF] font-bold">طبيب معتمد</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-900 block">{{ $apt->service->name ?? '-' }}</span>
                                <span class="text-[10px] text-[#87A96B] font-bold">{{ number_format($apt->service->price ?? 0, 0) }} ر.س</span>
                            </td>
                            <td class="px-6 py-4 font-medium">
                                <span class="block text-slate-900 font-bold">{{ $apt->appointment_date ? $apt->appointment_date->format('Y-m-d') : '-' }}</span>
                                <span class="text-[11px] text-slate-500">{{ date('h:i A', strtotime($apt->start_time)) }} - {{ date('h:i A', strtotime($apt->end_time)) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($apt->status === 'confirmed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#87A96B] text-white shadow-sm">مؤكد</span>
                                @elseif($apt->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">قيد الانتظار</span>
                                @elseif($apt->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#87A96B]/20 text-[#567042]">مكتمل ✓</span>
                                @elseif($apt->status === 'cancelled')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">ملغي</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">{{ $apt->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                    <form method="POST" action="{{ route('admin.appointments.status', $apt) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="text-[11px] font-bold rounded-xl border border-slate-200 bg-slate-50 py-1.5 pr-2 pl-6 focus:border-[#1E40AF] focus:ring-1 focus:ring-[#1E40AF] outline-none">
                                            <option value="confirmed" {{ $apt->status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                            <option value="completed" {{ $apt->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                            <option value="pending" {{ $apt->status == 'pending' ? 'selected' : '' }}>انتظار</option>
                                            <option value="cancelled" {{ $apt->status == 'cancelled' ? 'selected' : '' }}>إلغاء</option>
                                        </select>
                                    </form>

                                    @if($apt->status !== 'cancelled')
                                        <form method="POST" action="{{ route('admin.appointments.cancel', $apt) }}" onsubmit="return confirm('إلغاء هذا الموعد؟');" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-1.5 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded-lg transition" title="إلغاء الحجز">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.appointments.edit', $apt) }}" 
                                       class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition" 
                                       title="تعديل تفاصيل الموعد">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    <button type="button" 
                                            @click="confirmDelete('{{ route('admin.appointments.destroy', $apt) }}', 'حذف سجل الموعد', 'هل أنت متأكد من حذف سجل هذا الموعد نهائياً من قاعدة البيانات؟')"
                                            class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" 
                                            title="حذف السجل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                لا توجد حجوزات مطابقة لمعايير الفلترة المحددة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>

    <!-- 2. عرض التقويم الشهري الحقيقي (100% Dynamic Database Calendar) -->
    <div x-show="currentTab === 'calendar'" class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8" style="display: none;">
        
        <!-- ترويسة التقويم والتنقل بين الشهور والأرقام الحقيقية -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-3">
                    <h3 class="text-xl font-black text-slate-900">{{ $monthNameArabic }}</h3>
                    <div class="flex items-center space-x-1 space-x-reverse">
                        <!-- الشهر السابق -->
                        <a href="{{ route('admin.appointments.index', array_merge(request()->query(), ['year' => $prevMonthDate->year, 'month' => $prevMonthDate->month, 'tab' => 'calendar'])) }}" 
                           class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:text-[#1E40AF] hover:bg-slate-50 transition" 
                           title="الشهر السابق">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <!-- الشهر الحالي -->
                        <a href="{{ route('admin.appointments.index', array_merge(request()->query(), ['year' => now()->year, 'month' => now()->month, 'tab' => 'calendar'])) }}" 
                           class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:text-[#1E40AF] hover:bg-slate-50 transition">
                            اليوم
                        </a>
                        <!-- الشهر التالي -->
                        <a href="{{ route('admin.appointments.index', array_merge(request()->query(), ['year' => $nextMonthDate->year, 'month' => $nextMonthDate->month, 'tab' => 'calendar'])) }}" 
                           class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:text-[#1E40AF] hover:bg-slate-50 transition" 
                           title="الشهر التالي">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-1">استعراض بصري ديناميكي مستمد بالكامل من جدول الحجوزات الفعلي</p>
            </div>

            <!-- ملخص أرقام الشهر الحقيقية المستخلصة من قاعدة البيانات -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-full">
                    إجمالي الشهر: <strong class="text-slate-900">{{ $monthTotalAppointments }}</strong>
                </span>
                <span class="text-xs font-bold text-[#567042] bg-[#87A96B]/15 border border-[#87A96B]/30 px-3 py-1.5 rounded-full">
                    مؤكد: <strong>{{ $monthConfirmedCount }}</strong>
                </span>
                <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-full">
                    انتظار: <strong>{{ $monthPendingCount }}</strong>
                </span>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-full">
                    مكتمل: <strong>{{ $monthCompletedCount }}</strong>
                </span>
            </div>
        </div>

        @if($monthTotalAppointments === 0)
            <!-- حالة خلو الشهر من الحجوزات -->
            <div class="p-8 mb-6 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
                <p class="text-xs sm:text-sm font-bold text-slate-600">
                    لا توجد أي مواعيد أو حجوزات مسجلة في شهر {{ $monthNameArabic }} مع معايير الفلترة الحالية.
                </p>
                <p class="text-[11px] text-slate-400 mt-1">يمكنك الضغط على أي يوم في التقويم أدناه لحجز موعد جديد في ذلك التاريخ.</p>
            </div>
        @endif

        <!-- شبكة أيام الأسبوع (السبت - الجمعة) متجاوبة لجميع الشاشات -->
        <div class="overflow-x-auto pb-2">
            <div class="min-w-[680px]">
                
                <!-- عناوين الأيام السبعة (السبت - الجمعة) -->
                <div class="grid grid-cols-7 gap-2.5 text-center text-xs font-bold mb-3">
                    @php
                        $weekHeaders = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
                    @endphp
                    @foreach($weekHeaders as $idx => $dayTitle)
                        <div class="p-3 bg-[#172554] rounded-2xl text-white font-bold flex flex-col justify-center">
                            <span class="block text-[#87A96B] text-[10px] mb-0.5">اليوم {{ $idx + 1 }}</span>
                            {{ $dayTitle }}
                        </div>
                    @endforeach
                </div>

                <!-- خلايا الأيام الفعلية للتقويم -->
                <div class="grid grid-cols-7 gap-2.5 text-right text-xs">
                    
                    <!-- خلايا المحاذاة الفارغة قبل بداية الشهر -->
                    @for($p = 0; $p < $blankDaysCount; $p++)
                        <div class="min-h-[105px] p-2 bg-slate-100/40 rounded-2xl border border-dashed border-slate-200/70 opacity-40"></div>
                    @endfor

                    <!-- خلايا أيام الشهر الفعلية المستمدة من قاعدة البيانات -->
                    @foreach($calendarDays as $day)
                        <div @click="activeDay = calendarDays.find(d => d.date === '{{ $day['date'] }}')"
                             class="min-h-[105px] p-2.5 rounded-2xl border text-right flex flex-col justify-between cursor-pointer transition duration-200 {{ $day['isToday'] ? 'bg-blue-50/60 border-blue-400 ring-2 ring-blue-500/20 shadow-xs' : 'bg-slate-50/80 border-slate-100 hover:border-[#1E40AF] hover:bg-white hover:shadow-xs' }}">
                            
                            <!-- رأس اليوم ورقم التاريخ -->
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

                            <!-- مؤشرات وبادجات المواعيد الحقيقية فقط -->
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

                            <!-- رابط إضافة موعد في هذا اليوم -->
                            <div class="pt-1 border-t border-slate-100/80 flex items-center justify-between text-[10px] text-slate-400">
                                <span class="hover:text-blue-700 font-bold transition">تفاصيل</span>
                                <a href="{{ route('admin.appointments.create', ['date' => $day['date']]) }}" 
                                   @click.stop 
                                   class="text-blue-600 hover:text-blue-800 hover:underline font-bold"
                                   title="حجز موعد جديد في هذا التاريخ">
                                    + حجز
                                </a>
                            </div>

                        </div>
                    @endforeach

                </div>

            </div>
        </div>

        <!-- نافذة منبثقة / مودال لتفاصيل مواعيد اليوم المختار (Interactive Day Modal) -->
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
                
                <!-- رأس المودال -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <h4 class="text-base font-black text-slate-900">
                            مواعيد يوم: <span x-text="activeDay ? activeDay.date : ''" class="text-blue-700"></span>
                        </h4>
                        <p class="text-xs text-slate-400 mt-0.5">
                            إجمالي الحجوزات المسجلة فعلياً: <strong x-text="activeDay ? activeDay.total : 0" class="text-slate-800"></strong>
                        </p>
                    </div>
                    <button type="button" @click="activeDay = null" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">
                        ✕
                    </button>
                </div>

                <!-- محتوى مواعيد اليوم -->
                <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                    <template x-if="activeDay && activeDay.appointments.length === 0">
                        <div class="py-12 text-center text-slate-400 text-xs">
                            لا توجد أي مواعيد محجوزة في هذا التاريخ حتى الآن.
                        </div>
                    </template>

                    <template x-if="activeDay && activeDay.appointments.length > 0">
                        <template x-for="item in activeDay.appointments" :key="item.id">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h5 class="font-black text-sm text-slate-900" x-text="item.patient_name"></h5>
                                        <span class="text-[11px] text-slate-400" x-text="item.patient_phone"></span>
                                    </div>
                                    <div class="text-xs text-slate-600 mt-1">
                                        <span class="font-bold text-blue-700" x-text="item.service_name"></span> | 
                                        طبيب: <span x-text="item.dentist_name"></span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        التوقيت: <strong x-text="item.start_time"></strong> - <strong x-text="item.end_time"></strong> (<span x-text="item.duration_minutes"></span> دقيقة)
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-end sm:self-center">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                                          :class="{
                                              'bg-[#87A96B] text-white': item.status === 'confirmed',
                                              'bg-amber-100 text-amber-800': item.status === 'pending',
                                              'bg-blue-100 text-blue-800': item.status === 'completed',
                                              'bg-rose-100 text-rose-800': item.status === 'cancelled',
                                          }"
                                          x-text="item.status === 'confirmed' ? 'مؤكد' : (item.status === 'pending' ? 'انتظار' : (item.status === 'completed' ? 'مكتمل' : 'ملغي'))">
                                    </span>

                                    <a :href="item.edit_url" class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition text-xs font-bold" title="تعديل الموعد">
                                        تعديل
                                    </a>
                                </div>
                            </div>
                        </template>
                    </template>
                </div>

                <!-- أسفل المودال -->
                <div class="pt-4 border-t border-slate-100 mt-4 flex items-center justify-between">
                    <a :href="'{{ route('admin.appointments.create') }}?date=' + (activeDay ? activeDay.date : '')" 
                       class="px-5 py-2.5 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition shadow-xs">
                        + إضافة حجز في هذا اليوم
                    </a>
                    <button type="button" @click="activeDay = null" class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                        إغلاق
                    </button>
                </div>

            </div>

        </div>

    </div>

</div>
@endsection
