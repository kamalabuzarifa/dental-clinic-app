@extends('layouts.receptionist')

@section('title', 'الملف الطبي للمريض: ' . $patient->name)

@section('content')
<div class="space-y-6">

    <!-- شريط الرجوع والإجراءات -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('receptionist.patients.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>العودة لقائمة المرضى</span>
        </a>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('receptionist.appointments.create', ['patient_id' => $patient->id]) }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold bg-[#1E40AF] text-white shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>حجز موعد جديد للمريض</span>
            </a>

            <a href="{{ route('receptionist.patients.edit', $patient->id) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span>تعديل البيانات</span>
            </a>
        </div>
    </div>

    <!-- بطاقة معلومات المريض الأساسية -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.03)]">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-[#1E40AF] to-blue-500 text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-blue-900/20 flex-shrink-0">
                    {{ mb_substr($patient->name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-black text-slate-900">{{ $patient->name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-[#1E40AF]">ملف رقم #{{ $patient->id }}</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">تاريخ فتح الملف: {{ $patient->created_at ? $patient->created_at->format('Y-m-d') : '—' }}</p>
                </div>
            </div>

            <!-- معلومات الاتصال السريعة -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 block mb-0.5">رقم الهاتف</span>
                    <span class="font-black text-slate-800 font-mono" dir="ltr">{{ $patient->phone ?: 'لا يوجد' }}</span>
                </div>
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 block mb-0.5">البريد الإلكتروني</span>
                    <span class="font-bold text-slate-800" dir="ltr">{{ $patient->email }}</span>
                </div>
            </div>
        </div>

        <!-- ملخص إحصائيات مواعيد المريض -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
            <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100">
                <span class="text-[10px] font-bold text-blue-600 block">إجمالي المواعيد</span>
                <span class="text-2xl font-black text-[#1E40AF] mt-1 block">{{ $stats['total'] }}</span>
            </div>
            <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100">
                <span class="text-[10px] font-bold text-emerald-600 block">الجلسات المكتملة</span>
                <span class="text-2xl font-black text-emerald-700 mt-1 block">{{ $stats['completed'] }}</span>
            </div>
            <div class="bg-purple-50/50 p-4 rounded-2xl border border-purple-100">
                <span class="text-[10px] font-bold text-purple-600 block">المواعيد القادمة</span>
                <span class="text-2xl font-black text-purple-700 mt-1 block">{{ $stats['upcoming'] }}</span>
            </div>
            <div class="bg-rose-50/50 p-4 rounded-2xl border border-rose-100">
                <span class="text-[10px] font-bold text-rose-600 block">المواعيد الملغاة</span>
                <span class="text-2xl font-black text-rose-700 mt-1 block">{{ $stats['cancelled'] }}</span>
            </div>
        </div>
    </div>

    <!-- سجل مواعيد وزيارات المريض بالكامل -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.03)] overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#87A96B]"></span>
                <h2 class="text-base font-black text-slate-900">سجل المواعيد والزيارات</h2>
            </div>
            <span class="text-xs font-bold text-slate-400">{{ $appointments->total() }} موعد مسجل</span>
        </div>

        @if($appointments->isEmpty())
            <div class="py-12 text-center text-slate-400 text-xs">
                لا توجد مواعيد مسجلة لهذا المريض حتى الآن.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">رقم الحجز</th>
                            <th class="px-6 py-4">تاريخ ووقت الموعد</th>
                            <th class="px-6 py-4">الطبيب المعالج</th>
                            <th class="px-6 py-4">الخدمة</th>
                            <th class="px-6 py-4">الحالة</th>
                            <th class="px-6 py-4">ملاحظات الحجز</th>
                            <th class="px-6 py-4 text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($appointments as $apt)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="px-6 py-4 font-black text-slate-900">
                                    #{{ $apt->id }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $apt->appointment_date }}</div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ date('h:i A', strtotime($apt->start_time)) }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-800">
                                    د. {{ $apt->dentist->name }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                        {{ $apt->service->name }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $badgeClasses = match($apt->status) {
                                            'confirmed' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                            'completed' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                            'pending'   => 'bg-amber-50 text-amber-700 border border-amber-200',
                                            'cancelled' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                            'no_show'   => 'bg-slate-100 text-slate-600 border border-slate-200',
                                            default     => 'bg-slate-100 text-slate-600',
                                        };
                                        $statusLabels = [
                                            'confirmed' => 'مؤكد',
                                            'completed' => 'مكتمل',
                                            'pending'   => 'قيد الانتظار',
                                            'cancelled' => 'ملغي',
                                            'no_show'   => 'لم يحضر',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $badgeClasses }}">
                                        {{ $statusLabels[$apt->status] ?? $apt->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-slate-500 max-w-xs truncate">
                                    {{ $apt->notes ?: '—' }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('receptionist.appointments.edit', $apt->id) }}" 
                                           class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 transition"
                                           title="تعديل الموعد">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>

                                        @if($apt->status !== 'cancelled' && $apt->status !== 'completed')
                                            <form method="POST" action="{{ route('receptionist.appointments.cancel', $apt->id) }}" class="inline" onsubmit="return confirm('إلغاء هذا الموعد؟')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition"
                                                        title="إلغاء">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <div class="p-4 border-t border-slate-100">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
