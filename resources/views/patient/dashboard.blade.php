<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3 space-x-reverse">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-lg shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900">الملف الصحي للمريض</h1>
                    <p class="text-xs text-slate-500">أهلاً بك، {{ Auth::user()->name }}! تابع مواعيدك وفحوصاتك الطبية بكل سهولة.</p>
                </div>
            </div>
            <div>
                <a href="{{ route('patient.appointments.create') }}" class="inline-flex items-center justify-center px-6 py-2.5 text-xs font-bold text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-full shadow-md shadow-blue-700/20 hover:-translate-y-0.5">
                    + حجز موعد جديد
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            <!-- Medical Welcome Banner -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-800 via-blue-700 to-indigo-900 p-8 sm:p-10 text-white shadow-xl shadow-blue-900/15 border border-blue-600/30">
                <!-- Background Ambient Glow -->
                <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-[#87A96B]/20 rounded-full blur-3xl"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-blue-100 text-[11px] font-bold mb-3 border border-white/20">
                            <span class="w-2 h-2 rounded-full bg-[#87A96B] inline-block"></span>
                            <span>رعاية سنية متكاملة لابتسامة واثقة</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black leading-tight text-white mb-2">
                            أهلاً بك، {{ Auth::user()->name }}
                        </h2>
                        <p class="text-xs sm:text-sm text-blue-100 max-w-xl leading-relaxed">
                            فريقنا الطبي المتميز في انتظارك لتقديم أفضل رعاية وعلاجات متطورة في أجواء مريحة وهادئة تضمن سلامتك.
                        </p>
                    </div>

                    <div class="flex items-center space-x-3 space-x-reverse self-start md:self-center">
                        <a href="{{ route('patient.appointments.create') }}" class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold text-blue-900 transition-all duration-300 bg-white rounded-full shadow-lg hover:bg-blue-50 hover:-translate-y-0.5">
                            + حجز جلسة علاجية
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Stat Card 1 -->
                <div class="flex items-center p-6 bg-white rounded-2xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)] border border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg font-bold ml-4 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 block mb-0.5">المواعيد القادمة</span>
                        <span class="text-2xl font-black text-slate-900">{{ $stats['upcoming_count'] ?? 0 }}</span>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="flex items-center p-6 bg-white rounded-2xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)] border border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-[#87A96B]/15 text-[#557143] flex items-center justify-center text-lg font-bold ml-4 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 block mb-0.5">المواعيد المكتملة</span>
                        <span class="text-2xl font-black text-[#87A96B]">{{ $stats['completed_count'] ?? 0 }}</span>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="flex items-center p-6 bg-white rounded-2xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)] border border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg font-bold ml-4 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 block mb-0.5">إجمالي الجلسات</span>
                        <span class="text-2xl font-black text-slate-900">{{ $stats['total_count'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-2">
                    <div>
                        <h3 class="text-lg font-black text-slate-900">المواعيد القادمة في العيادة</h3>
                        <p class="text-xs text-slate-400 mt-0.5">قائمة المواعيد المجدولة لزياراتك الطبية القادمة</p>
                    </div>
                    <span class="text-xs font-bold text-[#557143] bg-[#87A96B]/15 border border-[#87A96B]/30 px-3.5 py-1 rounded-full">
                        {{ $upcomingAppointments->count() }} مواعيد مؤكدة
                    </span>
                </div>

                @if($upcomingAppointments->isEmpty())
                    <div class="relative p-10 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)] text-center">
                        <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-base font-black text-slate-900 mb-1">لا توجد لديك مواعيد قادمة مجدولة حالياً</h4>
                        <p class="text-xs text-slate-500 max-w-md mx-auto mb-6">احجز جلستك القادمة مع طبيب العيادة باختيار التوقيت الأنسب لجدولك اليومي.</p>
                        <a href="{{ route('patient.appointments.create') }}" class="inline-flex items-center justify-center px-7 py-3 text-xs font-bold text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-full shadow-md shadow-blue-700/20 hover:-translate-y-0.5">
                            + حجز موعد جديد الآن
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($upcomingAppointments as $apt)
                            <div class="relative p-7 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] transition-all duration-300 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.1)] hover:-translate-y-1 group flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                                <div class="flex items-start space-x-4 space-x-reverse">
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-700 to-blue-600 text-white font-black text-lg flex items-center justify-center flex-shrink-0 shadow-md shadow-blue-700/20">
                                        {{ mb_substr($apt->dentist->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-3 space-x-reverse mb-1">
                                            <h4 class="font-black text-slate-900 text-base">{{ $apt->dentist->name }}</h4>
                                            @php
                                                $statusBadges = [
                                                    'confirmed' => ['label' => 'موعد مؤكد ✓', 'class' => 'bg-[#87A96B]/15 text-[#557143] border-[#87A96B]/30'],
                                                    'pending' => ['label' => 'قيد المراجعة', 'class' => 'bg-amber-50 text-amber-800 border-amber-200'],
                                                    'cancelled' => ['label' => 'ملغي', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
                                                    'completed' => ['label' => 'مكتمل', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                                    'no_show' => ['label' => 'لم يحضر', 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
                                                ];
                                                $badge = $statusBadges[$apt->status] ?? ['label' => $apt->status, 'class' => 'bg-slate-100 text-slate-700 border-slate-200'];
                                            @endphp
                                            <span class="text-[11px] font-bold px-3 py-0.5 rounded-full border {{ $badge['class'] }}">
                                                {{ $badge['label'] }}
                                            </span>
                                        </div>

                                        <p class="text-xs font-bold text-blue-700 mb-3">
                                            الخدمة: {{ $apt->service->name }} (مدة الجلسة: {{ $apt->service->duration_minutes }} دقيقة)
                                        </p>

                                        <div class="flex flex-wrap items-center gap-5 text-xs text-slate-500">
                                            <span class="flex items-center gap-1.5 font-semibold text-slate-700">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $apt->appointment_date->format('Y-m-d') }}
                                            </span>
                                            <span class="flex items-center gap-1.5 font-bold text-slate-900">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                من {{ date('h:i A', strtotime($apt->start_time)) }} إلى {{ date('h:i A', strtotime($apt->end_time)) }}
                                            </span>
                                            @if($apt->notes)
                                                <span class="text-slate-400 italic">ملاحظات: "{{ $apt->notes }}"</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-3 space-x-reverse self-end md:self-center">
                                    @if($apt->status !== 'cancelled' && $apt->status !== 'completed')
                                        <form method="POST" action="{{ route('patient.appointments.cancel', $apt) }}" onsubmit="return confirm('هل أنت متأكد من رغبتك في إلغاء هذا الموعد؟');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-5 py-2.5 rounded-full text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition duration-200">
                                                إلغاء الموعد
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Past Appointments History -->
            <div class="relative p-8 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)]">
                <div class="pb-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-black text-slate-900">سجل الجلسات السابقة</h3>
                        <p class="text-xs text-slate-400 mt-0.5">تاريخ زياراتك وفحوصاتك الطبية المكتملة في العيادة</p>
                    </div>
                </div>

                @if($pastAppointments->isEmpty())
                    <div class="p-8 text-center text-xs text-slate-400">
                        لا توجد زيارات سابقة مسجلة حتى الآن.
                    </div>
                @else
                    <div class="overflow-x-auto mt-4">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-[#F8FAFC] text-slate-500 font-bold border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4">الطبيب</th>
                                    <th class="px-6 py-4">الخدمة الطبية</th>
                                    <th class="px-6 py-4">التاريخ</th>
                                    <th class="px-6 py-4">الوقت</th>
                                    <th class="px-6 py-4">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($pastAppointments as $pastApt)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-6 py-4 font-bold text-slate-900">{{ $pastApt->dentist->name }}</td>
                                        <td class="px-6 py-4">{{ $pastApt->service->name }}</td>
                                        <td class="px-6 py-4 font-semibold">{{ $pastApt->appointment_date->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4">{{ date('h:i A', strtotime($pastApt->start_time)) }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $pastBadge = match($pastApt->status) {
                                                    'completed' => ['label' => 'مكتمل ✓', 'class' => 'bg-[#87A96B]/15 text-[#557143] border-[#87A96B]/30'],
                                                    'cancelled' => ['label' => 'ملغي', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
                                                    'no_show' => ['label' => 'لم يحضر', 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
                                                    default => ['label' => $pastApt->status, 'class' => 'bg-slate-100 text-slate-700 border-slate-200']
                                                };
                                            @endphp
                                            <span class="inline-block px-3 py-0.5 rounded-full border {{ $pastBadge['class'] }} text-[11px] font-bold">
                                                {{ $pastBadge['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Floating Action Button (Quick Booking FAB) -->
    <a href="{{ route('patient.appointments.create') }}" 
       class="fixed bottom-8 left-8 z-40 inline-flex items-center gap-2 px-6 py-3.5 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-full shadow-2xl shadow-blue-900/30 hover:-translate-y-1 transition-all duration-300 border-2 border-white/60 backdrop-blur-md">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>حجز موعد جديد</span>
    </a>
</x-app-layout>