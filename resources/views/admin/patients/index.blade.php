@extends('layouts.admin')

@section('title', 'إدارة المرضى')

@section('content')
<div class="space-y-8">

    <!-- ترويسة الصفحة وزر فتح ملف مريض -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">سجل المرضى والملفات الطبية</h1>
            <p class="text-xs text-slate-500 mt-1">إدارة بيانات المرضى وسجلات الزيارات الطبية والحجوزات السابقة.</p>
        </div>

        <a href="{{ route('admin.patients.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold text-white transition-all duration-300 bg-[#1E40AF] rounded-full shadow-lg hover:bg-blue-800 hover:shadow-blue-900/20 hover:-translate-y-0.5 self-start sm:self-auto">
            <span class="ml-1.5 text-base">+</span> فتح ملف مريض جديد
        </a>
    </div>

    <!-- بطاقة البحث والتصفية المتقدمة -->
    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6">
        <form method="GET" action="{{ route('admin.patients.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="relative w-full sm:max-w-md">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}"
                       placeholder="ابحث برقم الملف، الاسم، الهاتف، أو البريد..." 
                       class="w-full pr-10 pl-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-full focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition placeholder:text-slate-400">
            </div>

            <div class="flex items-center space-x-2 space-x-reverse w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-7 py-2.5 rounded-full text-xs font-bold bg-[#1E40AF] text-white hover:bg-blue-800 transition shadow-md shadow-blue-900/10">
                    بحث وفلترة
                </button>
                @if($search)
                    <a href="{{ route('admin.patients.index') }}" class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                        إعادة تعيين
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- جدول بيانات المرضى المتقدم (Data Table) -->
    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#F8FAFC] text-slate-900 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">رقم الملف</th>
                        <th class="px-6 py-4">اسم المريض</th>
                        <th class="px-6 py-4">الهاتف والبريد</th>
                        <th class="px-6 py-4">تاريخ آخر زيارة</th>
                        <th class="px-6 py-4 text-center">إجمالي الحجوزات</th>
                        <th class="px-6 py-4 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-blue-50/30 transition">
                            <!-- رقم الملف -->
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-blue-900 bg-blue-50 px-3 py-1 rounded-full text-[11px] border border-blue-100">
                                    #REC-{{ str_pad($patient->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            <!-- اسم المريض -->
                            <td class="px-6 py-4 font-bold text-slate-900 text-sm">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#1E40AF] to-blue-500 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-sm">
                                        {{ mb_substr($patient->name, 0, 1) }}
                                    </div>
                                    <span>{{ $patient->name }}</span>
                                </div>
                            </td>

                            <!-- الهاتف والبريد -->
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $patient->phone ?? 'غير متوفر' }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $patient->email }}</div>
                            </td>

                            <!-- تاريخ آخر زيارة -->
                            <td class="px-6 py-4 font-medium text-slate-600">
                                @php
                                    $lastApt = $patient->patientAppointments->first();
                                @endphp
                                @if($lastApt && $lastApt->appointment_date)
                                    <span class="font-bold text-slate-900">{{ $lastApt->appointment_date->format('Y-m-d') }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ date('h:i A', strtotime($lastApt->start_time)) }}</span>
                                @else
                                    <span class="text-slate-400">لا يوجد حجز مسجل</span>
                                @endif
                            </td>

                            <!-- إجمالي الحجوزات -->
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#1E40AF] border border-blue-200">
                                    {{ $patient->patient_appointments_count ?? 0 }} مواعيد
                                </span>
                            </td>

                            <!-- إجراءات -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.patients.edit', $patient) }}" 
                                       class="px-3.5 py-1.5 rounded-full text-xs font-bold text-slate-700 bg-slate-50 hover:bg-blue-50 hover:text-[#1E40AF] border border-slate-200 transition">
                                        تعديل
                                    </a>
                                    <button type="button" 
                                            @click="confirmDelete('{{ route('admin.patients.destroy', $patient) }}', 'إغلاق وحذف ملف المريض', 'هل أنت متأكد من رغبتك في حذف ملف المريض ({{ $patient->name }}) نهائياً؟')"
                                            class="px-3.5 py-1.5 rounded-full text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                        حذف
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                لا يوجد مرضى مسجلون مطابقون للبحث.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($patients, 'hasPages') && $patients->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $patients->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
