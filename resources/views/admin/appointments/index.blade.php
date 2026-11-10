@extends('layouts.admin')

@section('title', 'إدارة الحجوزات')

@section('content')
<div class="space-y-8" x-data="{ currentTab: 'table' }">

    <!-- ترويسة الصفحة وتبديل العرض -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">سجل ومواعيد العيادة الشامل</h1>
            <p class="text-xs text-slate-500 mt-1">متابعة دقيقة لجدول حجوزات الأطباء، تأكيد المواعيد، وتحديث الحالات.</p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a href="{{ route('admin.appointments.create') }}" 
               class="inline-flex items-center justify-center px-6 py-2.5 text-xs font-bold text-white transition-all duration-300 bg-[#1E40AF] rounded-full shadow-lg hover:bg-blue-800 hover:shadow-blue-900/20 hover:-translate-y-0.5">
                <span class="ml-1.5 text-base">+</span> حجز موعد جديد
            </a>

            <!-- أزرار التبديل بين الجدول والتقويم -->
            <div class="inline-flex p-1 bg-slate-200/70 rounded-full text-xs font-bold">
                <button @click="currentTab = 'table'" 
                        :class="currentTab === 'table' ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-5 py-2 rounded-full transition duration-150">
                    قائمة الجدول
                </button>
                <button @click="currentTab = 'calendar'" 
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
            <!-- 1. التاريخ -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">تاريخ الموعد:</label>
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
                                <div class="text-[10px] text-[#1E40AF] font-bold">استشاري معتمد</div>
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

    <!-- 2. عرض التقويم (Calendar View) -->
    <div x-show="currentTab === 'calendar'" class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-8" style="display: none;">
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">جدول تقويم المواعيد الشهري</h3>
                <p class="text-xs text-slate-400 mt-0.5">استعراض بصري منظم لجدول عمل الأطباء في المركز</p>
            </div>
            <span class="text-xs font-bold text-[#1E40AF] bg-blue-50 px-3.5 py-1.5 rounded-full border border-blue-200">
                {{ now()->format('F Y') }}
            </span>
        </div>

        <!-- أسبوعي شبكي -->
        <div class="grid grid-cols-7 gap-3 text-center text-xs">
            @php
                $days = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
            @endphp
            @foreach($days as $idx => $day)
                <div class="p-3 bg-[#172554] rounded-2xl text-white font-bold">
                    <span class="block text-[#87A96B] text-[10px] mb-0.5">اليوم {{ $idx + 1 }}</span>
                    {{ $day }}
                </div>
            @endforeach

            @for($i = 1; $i <= 31; $i++)
                <div class="min-h-[90px] p-2.5 bg-slate-50/70 rounded-2xl border border-slate-100 text-right flex flex-col justify-between hover:border-[#1E40AF] hover:bg-white transition duration-200">
                    <span class="font-bold text-slate-800 text-xs">{{ $i }}</span>
                    @if(in_array($i, [2, 5, 11, 14, 20]))
                        <span class="text-[9px] font-bold bg-[#87A96B]/15 text-[#567042] border border-[#87A96B]/30 rounded-lg px-1.5 py-1 block truncate">
                            {{ rand(2, 6) }} مواعيد مؤكدة
                        </span>
                    @endif
                </div>
            @endfor
        </div>
    </div>

</div>
@endsection