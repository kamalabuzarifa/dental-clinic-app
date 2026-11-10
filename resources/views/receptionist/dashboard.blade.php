@extends('layouts.receptionist')

@section('title', 'لوحة تحكم موظف الاستقبال')

@section('content')
<div class="space-y-8">

    <!-- 1. ترويسة لوحة التحكم والإجراءات السريعة -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(30,64,175,0.03)]">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#87A96B] animate-pulse"></span>
                <span class="text-xs font-black text-[#87A96B] uppercase tracking-wider">مكتب الاستقبال والحجوزات</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">مرحباً بك، {{ Auth::user()->name }} 👋</h1>
            <p class="text-xs text-slate-500 mt-1">متابعة مواعيد العيادة اليومية، تأكيد حضور المرضى، وإدارة جدول الأطباء لحظياً.</p>
        </div>
        
        <div class="flex items-center flex-wrap gap-2.5">
            <a href="{{ route('receptionist.appointments.create') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold bg-[#1E40AF] text-white shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>حجز موعد جديد</span>
            </a>

            <a href="{{ route('receptionist.patients.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-[#87A96B] text-white shadow-lg shadow-[#87A96B]/20 hover:bg-[#6f8f56] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>فتح ملف مريض</span>
            </a>

            <a href="{{ route('receptionist.appointments.calendar') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200 transition">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>جدول الأطباء</span>
            </a>
        </div>
    </div>

    <!-- 2. بطاقات الإحصائيات الحقيقية المباشرة (Real DB Metrics) -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
        
        <!-- مواعيد اليوم -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400">إجمالي مواعيد اليوم</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-slate-900">{{ $stats['today_total'] }}</span>
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">اليوم</span>
            </div>
        </div>

        <!-- مؤكدة اليوم -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-blue-600">مؤكدة اليوم</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-[#1E40AF]">{{ $stats['today_confirmed'] }}</span>
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            </div>
        </div>

        <!-- قيد الانتظار اليوم -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-amber-600">قيد الانتظار</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-600">{{ $stats['today_pending'] }}</span>
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
            </div>
        </div>

        <!-- مكتملة اليوم -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-emerald-600">اكتملت اليوم</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-emerald-600">{{ $stats['today_completed'] }}</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
        </div>

        <!-- المواعيد القادمة -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-purple-600">مواعيد قادمة</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-purple-700">{{ $stats['upcoming_total'] }}</span>
                <span class="text-[10px] text-purple-500 font-bold">مستقبلاً</span>
            </div>
        </div>

        <!-- إجمالي المرضى المسجلين -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-500">المرضى المسجلون</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-slate-800">{{ $stats['total_patients'] }}</span>
                <span class="text-[10px] text-slate-400 font-bold">ملف</span>
            </div>
        </div>

        <!-- بحاجة لمتابعة -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-rose-600">تحتاج تأكيد</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black text-rose-600">{{ $stats['needs_followup_count'] }}</span>
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
            </div>
        </div>

    </div>

    <!-- 3. جدول مواعيد اليوم والفلترة حسب التاريخ -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.03)] overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#1E40AF]"></span>
                    <h2 class="text-base font-black text-slate-900">جدول مواعيد العيادة</h2>
                </div>
                <p class="text-xs text-slate-400 mt-1">تتبع وصول المرضى وتحديث حالة الموعد (تأكيد، تم الانتهاء، إلغاء) فورياً.</p>
            </div>

            <!-- محدد التاريخ المباشر -->
            <form method="GET" action="{{ route('receptionist.dashboard') }}" class="flex items-center gap-2">
                <label for="filterDate" class="text-xs font-bold text-slate-500 whitespace-nowrap">التاريخ المعروض:</label>
                <input type="date" 
                       id="filterDate" 
                       name="date" 
                       value="{{ $selectedDate }}" 
                       onchange="this.form.submit()" 
                       class="text-xs rounded-2xl border-slate-200 px-3 py-2 bg-slate-50 font-bold text-slate-700 focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition">
                @if($selectedDate !== $today)
                    <a href="{{ route('receptionist.dashboard') }}" class="text-xs font-bold text-[#1E40AF] hover:underline px-2">اليوم</a>
                @endif
            </form>
        </div>

        @if($dailyAppointments->isEmpty())
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-3xl bg-blue-50 text-[#1E40AF] mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-700">لا توجد مواعيد مسجلة لتاريخ {{ $selectedDate }}</p>
                <p class="text-xs text-slate-400 mt-1">يمكنك تسجيل حجز جديد بالنقر على زر حجز موعد جديد في الأعلى.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">الوقت</th>
                            <th class="px-6 py-4">المريض</th>
                            <th class="px-6 py-4">الطبيب المعالج</th>
                            <th class="px-6 py-4">الخدمة الطبية</th>
                            <th class="px-6 py-4">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات وتحديث الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($dailyAppointments as $apt)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="px-6 py-4">
                                    <div class="font-black text-slate-900 text-xs">
                                        {{ date('h:i A', strtotime($apt->start_time)) }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $apt->end_time ? date('h:i A', strtotime($apt->end_time)) : '' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <a href="{{ route('receptionist.patients.show', $apt->patient_id) }}" class="font-bold text-slate-900 hover:text-[#1E40AF] transition">
                                        {{ $apt->patient->name }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        📞 {{ $apt->patient->phone ?? 'لا يوجد هاتف' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">د. {{ $apt->dentist->name }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-semibold text-[11px]">
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

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        
                                        <!-- زر تأكيد سريع للموعد المعلق -->
                                        @if($apt->status === 'pending')
                                            <form method="POST" action="{{ route('receptionist.appointments.status', $apt->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" 
                                                        title="تأكيد الحضور والموعد"
                                                        class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-[#1E40AF] transition font-bold text-xs flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>تأكيد</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- زر تم الانتهاء / اكتمل للموعد المؤكد -->
                                        @if($apt->status === 'confirmed')
                                            <form method="POST" action="{{ route('receptionist.appointments.status', $apt->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" 
                                                        title="تحديد كاكتمل"
                                                        class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition font-bold text-xs flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>اكتمل</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- زر التعديل -->
                                        <a href="{{ route('receptionist.appointments.edit', $apt->id) }}" 
                                           class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 transition"
                                           title="تعديل تفاصيل الموعد">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </a>

                                        <!-- زر إلغاء الموعد -->
                                        @if($apt->status !== 'cancelled' && $apt->status !== 'completed')
                                            <form method="POST" action="{{ route('receptionist.appointments.cancel', $apt->id) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من رغبتك في إلغاء هذا الموعد؟')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        title="إلغاء الموعد"
                                                        class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition">
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
        @endif

    </div>

    <!-- 4. قسمان متجاوران: أحدث الحجوزات المضافة + مواعيد بحاجة لتأكيد ومتابعة -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- أحدث الحجوزات المسجلة -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(30,64,175,0.03)]">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#87A96B]"></span>
                    <h3 class="text-sm font-black text-slate-900">آخر الحجوزات المدخلة بالعيادة</h3>
                </div>
                <a href="{{ route('receptionist.appointments.index') }}" class="text-xs font-bold text-[#1E40AF] hover:underline">
                    عرض الكل ←
                </a>
            </div>

            @if($latestAppointments->isEmpty())
                <p class="text-xs text-slate-400 py-6 text-center">لا توجد حجوزات مسجلة مؤخراً.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($latestAppointments as $lApt)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 text-[#1E40AF] font-black text-xs flex items-center justify-center flex-shrink-0">
                                    {{ mb_substr($lApt->patient->name ?? 'م', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-900 truncate">{{ $lApt->patient->name }}</div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $lApt->appointment_date }} | {{ date('h:i A', strtotime($lApt->start_time)) }} | د. {{ $lApt->dentist->name }}
                                    </div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $lApt->status === 'confirmed' ? 'bg-blue-50 text-blue-700' : ($lApt->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">
                                {{ $lApt->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- مواعيد قيد الانتظار تحتاج تأكيد ومتابعة -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(30,64,175,0.03)]">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <h3 class="text-sm font-black text-slate-900">مواعيد قيد الانتظار تحتاج تأكيد</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                    {{ $pendingAppointments->count() }} موعد
                </span>
            </div>

            @if($pendingAppointments->isEmpty())
                <div class="py-8 text-center">
                    <p class="text-xs font-bold text-slate-500">جميع المواعيد القادمة مؤكدة بالكامل ✓</p>
                    <p class="text-[10px] text-slate-400 mt-1">لا توجد مواعيد معلقة بحاجة لتأكيد هاتفي حالياً.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($pendingAppointments as $pApt)
                        <div class="py-3 flex items-center justify-between gap-3">
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ $pApt->patient->name }}</div>
                                <div class="text-[10px] text-slate-400">
                                    📞 {{ $pApt->patient->phone ?? 'لا يوجد' }} | {{ $pApt->appointment_date }} | {{ date('h:i A', strtotime($pApt->start_time)) }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('receptionist.appointments.status', $pApt->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="px-3 py-1 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] transition">
                                        تأكيد الآن
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
