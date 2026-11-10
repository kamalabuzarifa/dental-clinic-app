@extends('layouts.receptionist')

@section('title', 'إدارة الحجوزات والمواعيد')

@section('content')
<div class="space-y-6">

    <!-- الترويسة وأزرار الإجراءات السريعة -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(30,64,175,0.03)]">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#1E40AF]"></span>
                <span class="text-xs font-black text-[#1E40AF] uppercase tracking-wider">الحجوزات والزيارات</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">إدارة الحجوزات والمواعيد</h1>
            <p class="text-xs text-slate-500 mt-1">عرض وتصفية جميع المواعيد، تأكيد الحضور، تعديل المواعيد، وإلغاء الزيارات.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('receptionist.appointments.calendar') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200 transition">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>عرض التقويم الشهري</span>
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

    <!-- بطاقات الإحصاءات المصغرة -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400">إجمالي الحجوزات</span>
            <span class="text-xl font-black text-slate-900 block mt-1">{{ $stats['total'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-[11px] font-bold text-blue-600">مواعيد اليوم</span>
            <span class="text-xl font-black text-[#1E40AF] block mt-1">{{ $stats['today'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-[11px] font-bold text-emerald-600">حجوزات مؤكدة</span>
            <span class="text-xl font-black text-emerald-600 block mt-1">{{ $stats['confirmed'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            <span class="text-[11px] font-bold text-amber-600">قيد الانتظار</span>
            <span class="text-xl font-black text-amber-600 block mt-1">{{ $stats['pending'] }}</span>
        </div>
    </div>

    <!-- صندوق الفلاتر المتقدمة -->
    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
        <form method="GET" action="{{ route('receptionist.appointments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            
            <!-- التاريخ -->
            <div>
                <label for="filter_date" class="block text-[11px] font-bold text-slate-500 mb-1">التاريخ</label>
                <input type="date" 
                       id="filter_date" 
                       name="date" 
                       value="{{ $selectedDate }}" 
                       class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#1E40AF] outline-none transition font-medium">
            </div>

            <!-- الطبيب -->
            <div>
                <label for="filter_doctor" class="block text-[11px] font-bold text-slate-500 mb-1">الطبيب المعالج</label>
                <select id="filter_doctor" name="doctor_id" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#1E40AF] outline-none transition font-medium">
                    <option value="">جميع الأطباء</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ $selectedDoctor == $doc->id ? 'selected' : '' }}>
                            د. {{ $doc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- الحالة -->
            <div>
                <label for="filter_status" class="block text-[11px] font-bold text-slate-500 mb-1">الحالة</label>
                <select id="filter_status" name="status" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#1E40AF] outline-none transition font-medium">
                    <option value="">جميع الحالات</option>
                    <option value="confirmed" {{ $selectedStatus === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                    <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    <option value="no_show" {{ $selectedStatus === 'no_show' ? 'selected' : '' }}>لم يحضر</option>
                </select>
            </div>

            <!-- بحث اسم المريض / الهاتف -->
            <div>
                <label for="filter_search" class="block text-[11px] font-bold text-slate-500 mb-1">اسم أو هاتف المريض</label>
                <input type="text" 
                       id="filter_search" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="مثال: خالد أو 05..." 
                       class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#1E40AF] outline-none transition font-medium">
            </div>

            <!-- أزرار التصفية -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 rounded-xl text-xs font-bold bg-[#1E40AF] text-white hover:bg-blue-800 transition">
                    تصفية
                </button>
                @if($selectedDate || $selectedDoctor || $selectedStatus || $search)
                    <a href="{{ route('receptionist.appointments.index') }}" class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                        إعادة تعيين
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- جدول المواعيد -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.03)] overflow-hidden">
        @if($appointments->isEmpty())
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-3xl bg-slate-50 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-700">لم يتم العثور على أي مواعيد تطابق شروط الفلترة</p>
                <p class="text-xs text-slate-400 mt-1">قم بتعديل معايير البحث أو تسجيل موعد جديد.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">رقم الموعد</th>
                            <th class="px-6 py-4">التاريخ والوقت</th>
                            <th class="px-6 py-4">المريض</th>
                            <th class="px-6 py-4">الطبيب</th>
                            <th class="px-6 py-4">الخدمة الطبية</th>
                            <th class="px-6 py-4">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
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

                                <td class="px-6 py-4">
                                    <a href="{{ route('receptionist.patients.show', $apt->patient_id) }}" class="font-bold text-slate-900 hover:text-[#1E40AF] transition block">
                                        {{ $apt->patient->name }}
                                    </a>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $apt->patient->phone ?? $apt->patient->email }}
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

                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        
                                        <!-- زر تأكيد سريع -->
                                        @if($apt->status === 'pending')
                                            <form method="POST" action="{{ route('receptionist.appointments.status', $apt->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" 
                                                        class="px-2.5 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] transition">
                                                    تأكيد
                                                </button>
                                            </form>
                                        @endif

                                        <!-- زر اكتمال سريع -->
                                        @if($apt->status === 'confirmed')
                                            <form method="POST" action="{{ route('receptionist.appointments.status', $apt->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" 
                                                        class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition">
                                                    اكتمل
                                                </button>
                                            </form>
                                        @endif

                                        <!-- زر تعديل الموعد -->
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
                                                        class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition"
                                                        title="إلغاء الموعد">
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

            <!-- الترقيم -->
            <div class="p-4 border-t border-slate-100">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
