@extends('layouts.receptionist')

@section('title', 'إدارة ملفات المرضى')

@section('content')
<div class="space-y-6">

    <!-- الترويسة وأزرار الإجراءات -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(30,64,175,0.03)]">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#87A96B]"></span>
                <span class="text-xs font-black text-[#87A96B] uppercase tracking-wider">سجلات المراجعين</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">إدارة ملفات المرضى</h1>
            <p class="text-xs text-slate-500 mt-1">البحث في ملفات المرضى، فتح سجلات جديدة، ومتابعة سجل الحجوزات السابقة والقادمة.</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3.5 py-2 rounded-2xl bg-blue-50 text-[#1E40AF] text-xs font-bold border border-blue-100">
                إجمالي الملفات: {{ $totalPatientsCount }}
            </span>
            <a href="{{ route('receptionist.patients.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold bg-[#1E40AF] text-white shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>فتح ملف مريض جديد</span>
            </a>
        </div>
    </div>

    <!-- صندوق البحث -->
    <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm">
        <form method="GET" action="{{ route('receptionist.patients.index') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="ابحث بالاسم، رقم الهاتف، أو البريد الإلكتروني للمريض..." 
                       class="w-full pr-11 pl-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition placeholder:text-slate-400 text-slate-800 font-medium">
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-2xl text-xs font-bold bg-[#1E40AF] text-white hover:bg-blue-800 transition">
                بحث
            </button>

            @if($search)
                <a href="{{ route('receptionist.patients.index') }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                    إلغاء البحث
                </a>
            @endif
        </form>
    </div>

    <!-- جدول المرضى -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.03)] overflow-hidden">
        @if($patients->isEmpty())
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-3xl bg-slate-50 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-700">لم يتم العثور على أي مرضى مسجلين</p>
                <p class="text-xs text-slate-400 mt-1">تأكد من عبارة البحث أو افتح ملف مريض جديد الآن.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">المريض</th>
                            <th class="px-6 py-4">رقم الهاتف</th>
                            <th class="px-6 py-4">البريد الإلكتروني</th>
                            <th class="px-6 py-4 text-center">عدد الزيارات</th>
                            <th class="px-6 py-4">آخر زيارة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($patients as $patient)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-[#87A96B]/15 text-[#557143] font-black text-xs flex items-center justify-center flex-shrink-0">
                                            {{ mb_substr($patient->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('receptionist.patients.show', $patient->id) }}" class="font-bold text-slate-900 hover:text-[#1E40AF] transition block">
                                                {{ $patient->name }}
                                            </a>
                                            <span class="text-[10px] text-slate-400">ملف رقم #{{ $patient->id }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-bold text-slate-800" dir="ltr">
                                    {{ $patient->phone ?: '—' }}
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    {{ $patient->email }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-[#1E40AF]">
                                        {{ $patient->patient_appointments_count }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-slate-500">
                                    @php
                                        $lastApt = $patient->patientAppointments->first();
                                    @endphp
                                    {{ $lastApt ? $lastApt->appointment_date : 'لا توجد زيارات سابقة' }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- زر عرض الملف وسجل المواعيد -->
                                        <a href="{{ route('receptionist.patients.show', $patient->id) }}" 
                                           class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#1E40AF] font-bold text-xs transition flex items-center gap-1"
                                           title="عرض الملف وسجل المواعيد">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>عرض الملف</span>
                                        </a>

                                        <!-- زر حجز موعد سريع لهذا المريض -->
                                        <a href="{{ route('receptionist.appointments.create', ['patient_id' => $patient->id]) }}" 
                                           class="px-3 py-1.5 rounded-xl bg-[#87A96B]/15 hover:bg-[#87A96B]/25 text-[#557143] font-bold text-xs transition flex items-center gap-1"
                                           title="حجز موعد للمريض">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <span>حجز</span>
                                        </a>

                                        <!-- زر تعديل البيانات الأساسية -->
                                        <a href="{{ route('receptionist.patients.edit', $patient->id) }}" 
                                           class="p-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 transition"
                                           title="تعديل بيانات المريض">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- الترقيم (Pagination) -->
            <div class="p-4 border-t border-slate-100">
                {{ $patients->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
