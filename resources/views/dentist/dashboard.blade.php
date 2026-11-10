<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">لوحة تحكم الطبيب</h1>
                <p class="text-sm text-slate-500 mt-1">أهلاً بك {{ Auth::user()->name }}، استعرض جدول مواعيدك وحالات المرضى اليوم.</p>
            </div>
            <div class="text-xs font-bold bg-blue-50 text-blue-700 px-4 py-2 rounded-xl border border-blue-200">
                اليوم: {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Dentist Statistics -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 block mb-1">مواعيد اليوم</span>
                    <span class="text-3xl font-black text-blue-600">{{ $stats['today_count'] ?? 0 }}</span>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 block mb-1">المكتمل اليوم</span>
                    <span class="text-3xl font-black text-emerald-600">{{ $stats['today_completed'] ?? 0 }}</span>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 block mb-1">المواعيد القادمة</span>
                    <span class="text-3xl font-black text-teal-600">{{ $stats['upcoming_count'] ?? 0 }}</span>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 block mb-1">إجمالي المرضى</span>
                    <span class="text-3xl font-black text-slate-800">{{ $stats['total_patients'] ?? 0 }}</span>
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <span class="w-3 h-3 rounded-full bg-blue-600 animate-pulse"></span>
                        <h2 class="text-lg font-black text-slate-900">جدول مواعيد اليوم ({{ now()->toDateString() }})</h2>
                    </div>
                    <span class="text-xs font-bold text-slate-500">
                        {{ $todayAppointments->count() }} مواعيد مسجلة
                    </span>
                </div>

                @if($todayAppointments->isEmpty())
                    <div class="p-12 text-center text-slate-400 text-sm">
                        لا توجد مواعيد متبقية لجدول اليوم.
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($todayAppointments as $apt)
                            <div class="p-6 hover:bg-slate-50/70 transition flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                                <div class="flex items-start space-x-4 space-x-reverse">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 font-black text-sm flex items-center justify-center flex-shrink-0">
                                        {{ date('h:i', strtotime($apt->start_time)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-3 space-x-reverse mb-1">
                                            <h3 class="font-bold text-slate-900 text-base">المريض: {{ $apt->patient->name }}</h3>
                                            <span class="text-xs text-slate-400 font-semibold">({{ $apt->patient->phone ?? $apt->patient->email }})</span>
                                        </div>
                                        <div class="text-xs font-semibold text-teal-700 mb-2">
                                            الخدمة: {{ $apt->service->name }} ({{ $apt->service->duration_minutes }} دقيقة)
                                        </div>
                                        <div class="flex items-center gap-4 text-xs text-slate-500">
                                            <span>التوقيت: من {{ date('h:i A', strtotime($apt->start_time)) }} إلى {{ date('h:i A', strtotime($apt->end_time)) }}</span>
                                            @if($apt->notes)
                                                <span class="bg-amber-50 text-amber-800 px-2.5 py-0.5 rounded-lg border border-amber-200">
                                                    ملاحظة المريض: {{ $apt->notes }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Updater Form -->
                                <div class="flex items-center space-x-3 space-x-reverse self-end lg:self-center">
                                    <form method="POST" action="{{ route('dentist.appointments.status', $apt) }}" class="flex items-center space-x-2 space-x-reverse">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="text-xs font-bold rounded-xl border-slate-200 bg-slate-50 py-2 pr-3 pl-8 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="confirmed" {{ $apt->status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                            <option value="completed" {{ $apt->status == 'completed' ? 'selected' : '' }}>مكتمل ✓</option>
                                            <option value="no_show" {{ $apt->status == 'no_show' ? 'selected' : '' }}>لم يحضر ✕</option>
                                            <option value="cancelled" {{ $apt->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Future Appointments -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-lg font-black text-slate-900">المواعيد القادمة في الأيام المقبلة</h2>
                </div>

                @if($upcomingAppointments->isEmpty())
                    <div class="p-8 text-center text-xs text-slate-400">
                        لا توجد مواعيد مستقبلية مسجلة.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4">المريض</th>
                                    <th class="px-6 py-4">الخدمة</th>
                                    <th class="px-6 py-4">التاريخ</th>
                                    <th class="px-6 py-4">الوقت</th>
                                    <th class="px-6 py-4">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($upcomingAppointments as $futApt)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-6 py-4 font-bold text-slate-900">{{ $futApt->patient->name }}</td>
                                        <td class="px-6 py-4">{{ $futApt->service->name }}</td>
                                        <td class="px-6 py-4 font-semibold">{{ $futApt->appointment_date->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4">{{ date('h:i A', strtotime($futApt->start_time)) }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $futApt->status }}
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
</x-app-layout>
