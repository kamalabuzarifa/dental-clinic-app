@extends('layouts.admin')

@section('title', 'لوحة تحكم الإدارة')

@section('content')
<div class="space-y-8">

    {{-- ======================================================== --}}
    {{-- ترويسة الصفحة --}}
    {{-- ======================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">لوحة تحكم الإدارة</h1>
            <p class="text-xs text-slate-500 mt-1">
                مرحباً {{ Auth::user()->name ?? '' }}، استعرض مؤشرات الأداء السريري والجداول اليومية لمركز ريادة.
            </p>
        </div>
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] text-xs font-semibold text-slate-700 self-start sm:self-auto flex-shrink-0">
            <span class="w-2 h-2 rounded-full bg-[#87A96B] animate-pulse"></span>
            <span>{{ now()->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}</span>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- 1. بطاقات الإحصائيات (6 بطاقات حقيقية) --}}
    {{-- ======================================================== --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">

        {{-- إجمالي المرضى --}}
        <a href="{{ route('admin.patients.index') }}"
           class="group p-5 bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 flex flex-col gap-3 transition hover:-translate-y-1 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] duration-300">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center flex-shrink-0 group-hover:bg-[#1E40AF] group-hover:text-white transition-colors duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block">إجمالي المرضى</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($stats['total_patients']) }}</span>
            </div>
        </a>

        {{-- مواعيد اليوم --}}
        <a href="{{ route('admin.appointments.index', ['date' => now()->toDateString()]) }}"
           class="group p-5 bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 flex flex-col gap-3 transition hover:-translate-y-1 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] duration-300">
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center flex-shrink-0 group-hover:bg-[#1E40AF] group-hover:text-white transition-colors duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block">مواعيد اليوم</span>
                <span class="text-2xl font-black text-[#1E40AF]">{{ number_format($stats['today_appointments']) }}</span>
            </div>
        </a>

        {{-- قيد الانتظار --}}
        <a href="{{ route('admin.appointments.index', ['status' => 'pending']) }}"
           class="group p-5 bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 flex flex-col gap-3 transition hover:-translate-y-1 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] duration-300">
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block">قيد الانتظار</span>
                <span class="text-2xl font-black text-amber-600">{{ number_format($stats['pending_appointments']) }}</span>
            </div>
        </a>

        {{-- مؤكدة --}}
        <a href="{{ route('admin.appointments.index', ['status' => 'confirmed']) }}"
           class="group p-5 bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 flex flex-col gap-3 transition hover:-translate-y-1 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] duration-300">
            <div class="w-11 h-11 rounded-2xl bg-[#87A96B]/15 text-[#557143] flex items-center justify-center flex-shrink-0 group-hover:bg-[#87A96B] group-hover:text-white transition-colors duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block">مؤكدة</span>
                <span class="text-2xl font-black text-[#557143]">{{ number_format($stats['confirmed_appointments']) }}</span>
            </div>
        </a>

        {{-- مكتملة --}}
        <a href="{{ route('admin.appointments.index', ['status' => 'completed']) }}"
           class="group p-5 bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 flex flex-col gap-3 transition hover:-translate-y-1 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] duration-300">
            <div class="w-11 h-11 rounded-2xl bg-[#87A96B]/15 text-[#87A96B] flex items-center justify-center flex-shrink-0 group-hover:bg-[#87A96B] group-hover:text-white transition-colors duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block">مكتملة</span>
                <span class="text-2xl font-black text-[#87A96B]">{{ number_format($stats['completed_appointments']) }}</span>
            </div>
        </a>

        {{-- الأطباء --}}
        <a href="{{ route('admin.doctors.index') }}"
           class="group p-5 bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 flex flex-col gap-3 transition hover:-translate-y-1 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] duration-300">
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 block">الأطباء</span>
                <span class="text-2xl font-black text-indigo-600">{{ number_format($stats['total_dentists']) }}</span>
            </div>
        </a>

    </div>

    {{-- ======================================================== --}}
    {{-- 2. تنبيهات الحجوزات المعلقة --}}
    {{-- ======================================================== --}}
    @if($pendingAlerts->isNotEmpty())
    <div class="bg-amber-50 border border-amber-200/80 rounded-3xl p-5 shadow-[0_10px_30px_-5px_rgba(217,119,6,0.07)]">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-black text-amber-800">
                    {{ $pendingAlerts->count() }} {{ $pendingAlerts->count() == 1 ? 'حجز يحتاج' : 'حجوزات تحتاج' }} إلى تأكيد
                </h3>
                <p class="text-xs text-amber-600 mt-0.5">المواعيد التالية لا تزال في حالة انتظار — يُنصح بمراجعتها وتأكيدها.</p>
            </div>
            <a href="{{ route('admin.appointments.index', ['status' => 'pending']) }}"
               class="flex-shrink-0 text-xs font-bold text-amber-700 hover:text-amber-900 hover:underline transition">
                عرض الكل ←
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($pendingAlerts as $alert)
            <div class="bg-white rounded-2xl border border-amber-100 p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-black text-sm flex-shrink-0">
                    {{ mb_substr(optional($alert->patient)->name ?? '؟', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-black text-slate-900 truncate">{{ optional($alert->patient)->name ?? 'مريض غير محدد' }}</p>
                    <p class="text-[10px] text-slate-500 truncate">{{ optional($alert->service)->name ?? '-' }} — {{ $alert->appointment_date ? $alert->appointment_date->format('d/m') : '-' }} الساعة {{ $alert->start_time ? \Carbon\Carbon::parse($alert->start_time)->format('h:i A') : '-' }}</p>
                </div>
                <form method="POST" action="{{ route('admin.appointments.status', $alert) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit"
                            class="text-[10px] font-bold bg-[#87A96B] text-white px-3 py-1.5 rounded-xl hover:bg-[#6F8F56] transition flex-shrink-0">
                        تأكيد
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ======================================================== --}}
    {{-- 3. جدول اليوم + إحصائيات الأسبوع (صف واحد في Grid) --}}
    {{-- ======================================================== --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- 3A. جدول مواعيد اليوم (يأخذ 2/3 العرض في الشاشات الكبيرة) --}}
        <div class="xl:col-span-2 bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 overflow-hidden flex flex-col">

            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">جدول مواعيد اليوم</h2>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ now()->locale('ar')->isoFormat('dddd D MMMM') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.appointments.index', ['date' => now()->toDateString()]) }}"
                   class="text-xs font-bold text-[#1E40AF] hover:text-blue-800 hover:underline transition flex-shrink-0">
                    عرض كامل ←
                </a>
            </div>

            @if($todayAppointments->isEmpty())
                <div class="flex-1 flex flex-col items-center justify-center py-14 text-center px-6">
                    <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-400">لا توجد مواعيد مجدولة لهذا اليوم</p>
                    <a href="{{ route('admin.appointments.create') }}"
                       class="mt-3 text-xs font-bold text-[#1E40AF] hover:underline">إضافة موعد جديد</a>
                </div>
            @else
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-[#F8FAFC] border-b border-slate-100 sticky top-0">
                            <tr>
                                <th class="px-5 py-3.5 font-bold text-slate-500 text-[11px]">الوقت</th>
                                <th class="px-5 py-3.5 font-bold text-slate-500 text-[11px]">المريض</th>
                                <th class="px-5 py-3.5 font-bold text-slate-500 text-[11px] hidden sm:table-cell">الطبيب</th>
                                <th class="px-5 py-3.5 font-bold text-slate-500 text-[11px] hidden md:table-cell">الخدمة</th>
                                <th class="px-5 py-3.5 font-bold text-slate-500 text-[11px]">الحالة</th>
                                <th class="px-5 py-3.5 font-bold text-slate-500 text-[11px] text-center">إجراء</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($todayAppointments as $apt)
                            <tr class="hover:bg-blue-50/20 transition duration-150">
                                <td class="px-5 py-3.5">
                                    <span class="font-black text-slate-900 block">{{ \Carbon\Carbon::parse($apt->start_time)->format('h:i') }}</span>
                                    <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($apt->start_time)->format('A') }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-bold text-slate-900 block">{{ optional($apt->patient)->name ?? 'غير محدد' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ optional($apt->patient)->phone ?? '' }}</span>
                                </td>
                                <td class="px-5 py-3.5 hidden sm:table-cell">
                                    <span class="font-semibold text-slate-700">{{ optional($apt->dentist)->name ?? 'غير محدد' }}</span>
                                </td>
                                <td class="px-5 py-3.5 hidden md:table-cell">
                                    <span class="font-semibold text-slate-700 block">{{ optional($apt->service)->name ?? 'كشف عام' }}</span>
                                    @if(optional($apt->service)->duration_minutes)
                                    <span class="text-[10px] text-slate-400">{{ $apt->service->duration_minutes }} دقيقة</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @include('admin.partials.status-badge', ['status' => $apt->status])
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <form method="POST" action="{{ route('admin.appointments.status', $apt) }}">
                                        @csrf @method('PATCH')
                                        <select name="status" onchange="this.form.submit()"
                                                class="text-[10px] font-bold rounded-xl border border-slate-200 bg-slate-50 py-1 pr-2 pl-5 focus:border-[#1E40AF] focus:ring-1 focus:ring-[#1E40AF] outline-none cursor-pointer">
                                            <option value="pending"   {{ $apt->status == 'pending'   ? 'selected' : '' }}>انتظار</option>
                                            <option value="confirmed" {{ $apt->status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                            <option value="completed" {{ $apt->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                            <option value="cancelled" {{ $apt->status == 'cancelled' ? 'selected' : '' }}>إلغاء</option>
                                            <option value="no_show"   {{ $apt->status == 'no_show'   ? 'selected' : '' }}>غياب</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- 3B. إحصائيات الأسبوع (1/3 العرض) --}}
        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 overflow-hidden flex flex-col">

            <div class="p-5 sm:p-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#87A96B]/15 text-[#557143] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">آخر 7 أيام</h2>
                        <p class="text-[10px] text-slate-400 mt-0.5">إجمالي الحجوزات اليومية</p>
                    </div>
                </div>
            </div>

            <div class="p-5 flex-1 flex flex-col justify-between gap-3">
                @php
                    $maxTotal = collect($weeklyData)->max('total');
                    $weekTotal = collect($weeklyData)->sum('total');
                    $weekCompleted = collect($weeklyData)->sum('completed');
                @endphp

                {{-- ملخص الأسبوع --}}
                <div class="grid grid-cols-2 gap-3 mb-2">
                    <div class="bg-[#F8FAFC] rounded-2xl p-3 text-center">
                        <span class="text-xl font-black text-slate-900">{{ $weekTotal }}</span>
                        <span class="text-[10px] text-slate-400 font-bold block mt-0.5">إجمالي الأسبوع</span>
                    </div>
                    <div class="bg-[#87A96B]/10 rounded-2xl p-3 text-center">
                        <span class="text-xl font-black text-[#557143]">{{ $weekCompleted }}</span>
                        <span class="text-[10px] text-[#557143] font-bold block mt-0.5">مكتملة</span>
                    </div>
                </div>

                {{-- أشرطة الأيام --}}
                <div class="space-y-2.5">
                    @foreach($weeklyData as $day)
                    <div class="flex items-center gap-2.5">
                        <span class="text-[10px] font-bold text-slate-500 w-16 flex-shrink-0 {{ $day['is_today'] ? 'text-[#1E40AF]' : '' }}">
                            {{ $day['label'] }}
                        </span>
                        <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                            @if($maxTotal > 0)
                            <div class="h-full rounded-full transition-all duration-500
                                        {{ $day['is_today'] ? 'bg-[#1E40AF]' : 'bg-[#87A96B]' }}"
                                 style="width: {{ $maxTotal > 0 ? round(($day['total'] / $maxTotal) * 100) : 0 }}%">
                            </div>
                            @endif
                        </div>
                        <span class="text-[10px] font-black w-5 text-left flex-shrink-0
                                     {{ $day['is_today'] ? 'text-[#1E40AF]' : 'text-slate-700' }}">
                            {{ $day['total'] }}
                        </span>
                    </div>
                    @endforeach
                </div>

                {{-- أعلى الخدمات --}}
                @if($topServices->isNotEmpty())
                <div class="mt-3 pt-4 border-t border-slate-100">
                    <p class="text-[10px] font-black text-slate-500 mb-2.5 uppercase tracking-wider">أكثر الخدمات طلباً</p>
                    @foreach($topServices->take(3) as $svc)
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-[11px] text-slate-700 font-semibold truncate flex-1">{{ $svc->name }}</span>
                        <span class="text-[10px] font-black text-[#87A96B] flex-shrink-0">{{ $svc->appointments_count }} حجز</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ======================================================== --}}
    {{-- 4. جدول أحدث الحجوزات (محسّن) --}}
    {{-- ======================================================== --}}
    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 overflow-hidden">

        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-900">أحدث الحجوزات المسجلة</h2>
                    <p class="text-[10px] text-slate-400 mt-0.5">متابعة آخر {{ $recentAppointments->count() }} حجزاً حسب تاريخ الإضافة</p>
                </div>
            </div>
            <a href="{{ route('admin.appointments.index') }}"
               class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition shadow-md shadow-blue-900/10 self-start sm:self-auto flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                عرض كافة الحجوزات
            </a>
        </div>

        @if($recentAppointments->isEmpty())
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-400">لا توجد أي حجوزات مسجلة حتى الآن.</p>
                <a href="{{ route('admin.appointments.create') }}"
                   class="mt-3 inline-block text-xs font-bold text-[#1E40AF] hover:underline">إضافة أول حجز</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#F8FAFC] border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-4 font-bold text-slate-500 text-[11px]">المريض</th>
                            <th class="px-5 py-4 font-bold text-slate-500 text-[11px] hidden sm:table-cell">الطبيب</th>
                            <th class="px-5 py-4 font-bold text-slate-500 text-[11px] hidden md:table-cell">الخدمة</th>
                            <th class="px-5 py-4 font-bold text-slate-500 text-[11px]">التاريخ والوقت</th>
                            <th class="px-5 py-4 font-bold text-slate-500 text-[11px]">الحالة</th>
                            <th class="px-5 py-4 font-bold text-slate-500 text-[11px] text-center">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recentAppointments as $apt)
                        <tr class="hover:bg-blue-50/20 transition duration-150">

                            {{-- المريض --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-[#1E40AF] flex items-center justify-center font-black text-xs flex-shrink-0">
                                        {{ mb_substr(optional($apt->patient)->name ?? '؟', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-bold text-slate-900 block truncate max-w-[120px]">{{ optional($apt->patient)->name ?? 'غير محدد' }}</span>
                                        <span class="text-[10px] text-slate-400 truncate block">{{ optional($apt->patient)->phone ?? '' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- الطبيب --}}
                            <td class="px-5 py-4 hidden sm:table-cell">
                                <span class="font-semibold text-slate-700">{{ optional($apt->dentist)->name ?? 'غير محدد' }}</span>
                            </td>

                            {{-- الخدمة --}}
                            <td class="px-5 py-4 hidden md:table-cell">
                                <span class="font-semibold text-slate-700 block max-w-[140px] truncate">{{ optional($apt->service)->name ?? 'كشف عام' }}</span>
                                @if(optional($apt->service)->duration_minutes)
                                <span class="text-[10px] text-slate-400">{{ $apt->service->duration_minutes }} دقيقة</span>
                                @endif
                            </td>

                            {{-- التاريخ والوقت --}}
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-900 block">
                                    {{ $apt->appointment_date ? $apt->appointment_date->format('d/m/Y') : '-' }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-medium mt-0.5 block">
                                    {{ $apt->start_time ? \Carbon\Carbon::parse($apt->start_time)->format('h:i A') : '' }}
                                </span>
                            </td>

                            {{-- الحالة --}}
                            <td class="px-5 py-4">
                                @include('admin.partials.status-badge', ['status' => $apt->status])
                            </td>

                            {{-- الإجراءات --}}
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <form method="POST" action="{{ route('admin.appointments.status', $apt) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <select name="status" onchange="this.form.submit()"
                                                class="text-[10px] font-bold rounded-xl border border-slate-200 bg-slate-50 py-1.5 pr-2 pl-5 focus:border-[#1E40AF] focus:ring-1 focus:ring-[#1E40AF] outline-none cursor-pointer">
                                            <option value="pending"   {{ $apt->status == 'pending'   ? 'selected' : '' }}>انتظار</option>
                                            <option value="confirmed" {{ $apt->status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                            <option value="completed" {{ $apt->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                            <option value="cancelled" {{ $apt->status == 'cancelled' ? 'selected' : '' }}>إلغاء</option>
                                            <option value="no_show"   {{ $apt->status == 'no_show'   ? 'selected' : '' }}>غياب</option>
                                        </select>
                                    </form>

                                    @if($apt->status !== 'cancelled' && $apt->status !== 'completed')
                                    <form method="POST"
                                          action="{{ route('admin.appointments.cancel', $apt) }}"
                                          onsubmit="return confirm('هل تريد إلغاء هذا الحجز؟')"
                                          class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="p-1.5 text-rose-400 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition"
                                                title="إلغاء الحجز">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- تذييل الجدول --}}
            <div class="p-4 border-t border-slate-100 bg-[#F8FAFC] flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-[11px] text-slate-400 font-semibold">
                    عرض آخر {{ $recentAppointments->count() }} حجزاً من إجمالي {{ number_format($stats['total_appointments'] ?? 0) }}
                </span>
                <a href="{{ route('admin.appointments.index') }}"
                   class="text-xs font-bold text-[#1E40AF] hover:text-blue-800 hover:underline transition">
                    عرض كافة الحجوزات ←
                </a>
            </div>
        @endif
    </div>

    {{-- ======================================================== --}}
    {{-- 5. روابط سريعة (Quick Actions) --}}
    {{-- ======================================================== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('admin.appointments.create') }}"
           class="group flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] hover:border-[#1E40AF]/30 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.10)] transition duration-300">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1E40AF] flex items-center justify-center flex-shrink-0 group-hover:bg-[#1E40AF] group-hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <span class="text-xs font-bold text-slate-700 group-hover:text-[#1E40AF] transition-colors">حجز جديد</span>
        </a>

        <a href="{{ route('admin.patients.index') }}"
           class="group flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] hover:border-[#87A96B]/40 hover:shadow-[0_20px_40px_-10px_rgba(135,169,107,0.10)] transition duration-300">
            <div class="w-9 h-9 rounded-xl bg-[#87A96B]/15 text-[#557143] flex items-center justify-center flex-shrink-0 group-hover:bg-[#87A96B] group-hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-xs font-bold text-slate-700 group-hover:text-[#557143] transition-colors">قائمة المرضى</span>
        </a>

        <a href="{{ route('admin.doctors.index') }}"
           class="group flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] hover:border-indigo-200 hover:shadow-[0_20px_40px_-10px_rgba(79,70,229,0.08)] transition duration-300">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="text-xs font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">الأطباء</span>
        </a>

        <a href="{{ route('admin.reports.index') }}"
           class="group flex items-center gap-3 p-4 bg-white rounded-2xl border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] hover:border-slate-300 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.08)] transition duration-300">
            <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center flex-shrink-0 group-hover:bg-slate-700 group-hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <span class="text-xs font-bold text-slate-700 group-hover:text-slate-900 transition-colors">التقارير</span>
        </a>
    </div>

</div>
@endsection
