<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900">لوحة تحكم موظف الاستقبال</h1>
                <p class="text-sm text-slate-500 mt-1">إدارة مواعيد العيادة اليومية، تأكيد حضور المرضى، وإدخال حجوزات جديدة.</p>
            </div>
            <div class="flex items-center space-x-3 space-x-reverse">
                <button @click="document.getElementById('bookingModal').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-amber-500 text-white shadow-md hover:bg-amber-600 transition">
                    + تسجيل حجز لمريض
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 block mb-1">مواعيد اليوم الكلية</span>
                    <span class="text-3xl font-black text-amber-600">{{ $stats['today_total'] ?? 0 }}</span>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 block mb-1">المؤكدة اليوم</span>
                    <span class="text-3xl font-black text-blue-600">{{ $stats['today_confirmed'] ?? 0 }}</span>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 block mb-1">المكتملة اليوم</span>
                    <span class="text-3xl font-black text-emerald-600">{{ $stats['today_completed'] ?? 0 }}</span>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 block mb-1">إجمالي المرضى المسجلين</span>
                    <span class="text-3xl font-black text-slate-800">{{ $stats['total_patients'] ?? 0 }}</span>
                </div>
            </div>

            <!-- Date Filter & Daily Appointments -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-black text-slate-900">مواعيد اليوم المحدد</h2>
                        <p class="text-xs text-slate-400 mt-1">تتبع الحضور وتحديث حالات الزيارات فور وصول المرضى.</p>
                    </div>
                    <form method="GET" action="{{ route('receptionist.dashboard') }}" class="flex items-center space-x-2 space-x-reverse">
                        <label for="filterDate" class="text-xs font-bold text-slate-500">اختر التاريخ:</label>
                        <input type="date" id="filterDate" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="text-xs rounded-xl border-slate-200 p-2 bg-slate-50 font-bold focus:ring-amber-500 focus:border-amber-500">
                    </form>
                </div>

                @if($dailyAppointments->isEmpty())
                    <div class="p-12 text-center text-slate-400 text-sm">
                        لا توجد مواعيد مسجلة لتاريخ {{ $selectedDate }}.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs">
                            <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4">الوقت</th>
                                    <th class="px-6 py-4">المريض</th>
                                    <th class="px-6 py-4">الطبيب</th>
                                    <th class="px-6 py-4">الخدمة</th>
                                    <th class="px-6 py-4">الحالة</th>
                                    <th class="px-6 py-4 text-center">الإجراءات وتحديث الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($dailyAppointments as $apt)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-6 py-4 font-black text-slate-900">
                                            {{ date('h:i A', strtotime($apt->start_time)) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-900">{{ $apt->patient->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $apt->patient->phone ?? $apt->patient->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 font-semibold">{{ $apt->dentist->name }}</td>
                                        <td class="px-6 py-4">{{ $apt->service->name }}</td>
                                        <td class="px-6 py-4">
                                            @php
                                                $recBadge = match($apt->status) {
                                                    'confirmed' => ['label' => 'حجز مؤكد', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                                    'completed' => ['label' => 'تم الحضور واكتمل', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                                    'no_show' => ['label' => 'لم يحضر', 'class' => 'bg-gray-100 text-gray-700 border-gray-200'],
                                                    'cancelled' => ['label' => 'ملغي', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
                                                    default => ['label' => $apt->status, 'class' => 'bg-amber-50 text-amber-700 border-amber-200']
                                                };
                                            @endphp
                                            <span class="inline-block px-2.5 py-0.5 rounded-full border {{ $recBadge['class'] }} text-[11px] font-bold">
                                                {{ $recBadge['label'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                                <!-- Status Form -->
                                                <form method="POST" action="{{ route('receptionist.appointments.status', $apt) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" onchange="this.form.submit()" class="text-xs font-bold rounded-lg border-slate-200 bg-slate-50 py-1.5 pr-2 pl-6 focus:ring-amber-500 focus:border-amber-500">
                                                        <option value="confirmed" {{ $apt->status == 'confirmed' ? 'selected' : '' }}>تأكيد</option>
                                                        <option value="completed" {{ $apt->status == 'completed' ? 'selected' : '' }}>اكتمل الزيارة</option>
                                                        <option value="no_show" {{ $apt->status == 'no_show' ? 'selected' : '' }}>لم يحضر</option>
                                                        <option value="cancelled" {{ $apt->status == 'cancelled' ? 'selected' : '' }}>إلغاء</option>
                                                    </select>
                                                </form>

                                                @if($apt->status !== 'cancelled')
                                                    <form method="POST" action="{{ route('receptionist.appointments.cancel', $apt) }}" onsubmit="return confirm('هل تريد إلغاء هذا الموعد؟');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg" title="إلغاء الموعد">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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

        </div>
    </div>

    <!-- Quick Booking Modal for Receptionist -->
    <div id="bookingModal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl relative">
            <button onclick="document.getElementById('bookingModal').classList.add('hidden')" class="absolute left-6 top-6 text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h3 class="text-lg font-black text-slate-900 mb-1">تسجيل حجز موعد جديد</h3>
            <p class="text-xs text-slate-400 mb-6">إدخال موعد نيابة عن مريض حضر شخصياً أو عبر الهاتف.</p>

            <form method="POST" action="{{ route('receptionist.appointments.store') }}" class="space-y-4 text-right">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">المريض:</label>
                    <select name="patient_id" required class="w-full text-xs font-bold rounded-xl border-slate-200 p-2.5">
                        <option value="">-- اختر المريض --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->phone ?? $patient->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">الطبيب:</label>
                    <select name="dentist_id" required class="w-full text-xs font-bold rounded-xl border-slate-200 p-2.5">
                        <option value="">-- اختر الطبيب --</option>
                        @foreach($dentists as $dentist)
                            <option value="{{ $dentist->id }}">{{ $dentist->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">الخدمة:</label>
                    <select name="service_id" required class="w-full text-xs font-bold rounded-xl border-slate-200 p-2.5">
                        <option value="">-- اختر الخدمة --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->duration_minutes }} دقيقة - {{ $service->price }} ر.س)</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">التاريخ:</label>
                        <input type="date" name="appointment_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required class="w-full text-xs font-bold rounded-xl border-slate-200 p-2.5">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">وقت البدء:</label>
                        <input type="time" name="start_time" value="10:00" required class="w-full text-xs font-bold rounded-xl border-slate-200 p-2.5">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">ملاحظات:</label>
                    <textarea name="notes" rows="2" placeholder="ملاحظات الحجز إن وجدت..." class="w-full text-xs font-bold rounded-xl border-slate-200 p-2.5"></textarea>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3 space-x-reverse">
                    <button type="button" onclick="document.getElementById('bookingModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl">إلغاء</button>
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition shadow">حفظ الحجز الآن</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
