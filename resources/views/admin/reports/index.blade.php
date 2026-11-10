@extends('layouts.admin')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="space-y-8">

    <!-- ترويسة الصفحة وأزرار التصدير -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">التقارير التحليلية والمالية</h1>
            <p class="text-xs text-slate-500 mt-1">مؤشرات الأداء السريري، الإيرادات التقديرية، ومعدلات نمو الحجوزات بالمركز.</p>
        </div>

        <div class="flex items-center space-x-3 space-x-reverse self-start sm:self-auto">
            <button onclick="alert('جاري إعداد تقرير PDF الشامل...');" 
                    class="inline-flex items-center space-x-2 space-x-reverse px-5 py-2.5 rounded-full text-xs font-bold text-slate-700 bg-white border border-slate-200/80 hover:bg-slate-50 transition shadow-sm">
                <span>📄</span>
                <span>تصدير تقرير PDF</span>
            </button>
            <button onclick="alert('جاري تنزيل ملف Excel...');" 
                    class="inline-flex items-center space-x-2 space-x-reverse px-5 py-2.5 rounded-full text-xs font-bold text-white bg-[#1E40AF] hover:bg-blue-800 transition shadow-md shadow-blue-900/10">
                <span>📊</span>
                <span>تصدير Excel</span>
            </button>
        </div>
    </div>

    <!-- البطاقات المالية السريعة -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="p-6 bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 flex items-center justify-between transition hover:-translate-y-1 duration-300">
            <div>
                <span class="text-xs font-bold text-slate-400 block mb-1">الإيرادات التقديرية المكتملة</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($estimatedRevenue, 0) }} <span class="text-xs text-[#87A96B] font-bold">ر.س</span></span>
            </div>
            <div class="w-14 h-14 rounded-full bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-xl shadow-inner">
                💰
            </div>
        </div>

        <div class="p-6 bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 flex items-center justify-between transition hover:-translate-y-1 duration-300">
            <div>
                <span class="text-xs font-bold text-slate-400 block mb-1">نسبة إنجاز المواعيد</span>
                @php
                    $rate = $totalAppointments > 0 ? round(($completedAppointments / $totalAppointments) * 100, 1) : 0;
                @endphp
                <span class="text-2xl font-black text-[#87A96B]">{{ $rate }}%</span>
            </div>
            <div class="w-14 h-14 rounded-full bg-[#87A96B]/15 text-[#87A96B] flex items-center justify-center font-bold text-xl shadow-inner">
                📈
            </div>
        </div>

        <div class="p-6 bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 flex items-center justify-between transition hover:-translate-y-1 duration-300">
            <div>
                <span class="text-xs font-bold text-slate-400 block mb-1">متوسط كشف الجلسة</span>
                @php
                    $avg = $completedAppointments > 0 ? round($estimatedRevenue / $completedAppointments, 0) : 0;
                @endphp
                <span class="text-2xl font-black text-slate-900">{{ number_format($avg, 0) }} <span class="text-xs text-[#1E40AF] font-bold">ر.س</span></span>
            </div>
            <div class="w-14 h-14 rounded-full bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-xl shadow-inner">
                🎯
            </div>
        </div>
    </div>

    <!-- بطاقات الرسوم البيانية (Chart Visualizations) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- 1. إيرادات الأشهر الستة الأخيرة -->
        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-7">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1E40AF] flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-sm">
                        ✦
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">حركة الإيرادات الشهرية</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">مقارنة نمو الحجوزات المالية على مدار الأشهر</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-[#1E40AF] bg-blue-50 border border-blue-200 px-3 py-1 rounded-full">آخر 6 أشهر</span>
            </div>

            <!-- محاكي المخطط البياني (CSS Bar Chart Visualization) -->
            <div class="h-56 flex items-end justify-between gap-4 pt-8 px-4 border-b border-slate-100">
                @php
                    $mockMonths = [
                        ['name' => 'مايو', 'height' => '45%', 'val' => '18,500'],
                        ['name' => 'يونيو', 'height' => '60%', 'val' => '24,200'],
                        ['name' => 'يوليو', 'height' => '55%', 'val' => '22,000'],
                        ['name' => 'أغسطس', 'height' => '75%', 'val' => '31,400'],
                        ['name' => 'سبتمبر', 'height' => '85%', 'val' => '36,800'],
                        ['name' => 'أكتوبر', 'height' => '95%', 'val' => '42,500'],
                    ];
                @endphp
                @foreach($mockMonths as $m)
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                        <span class="text-[10px] font-bold text-slate-400 group-hover:text-slate-800 transition opacity-0 group-hover:opacity-100">{{ $m['val'] }}</span>
                        <div class="w-full max-w-[42px] bg-gradient-to-t from-[#172554] to-[#1E40AF] rounded-t-xl group-hover:from-[#1E40AF] group-hover:to-blue-400 transition-all duration-300" style="height: {{ $m['height'] }};"></div>
                        <span class="text-[11px] font-bold text-slate-600 mt-1">{{ $m['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 2. أداء الأطباء وعدد المرضى -->
        <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-7">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1E40AF] flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-sm">
                        ✦
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">مؤشرات أداء طاقم الأطباء</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">توزيع المواعيد المسجلة على كل استشاري</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($doctorsPerformance as $doc)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-bold text-slate-800">{{ $doc->name }}</span>
                            <span class="font-bold text-[#1E40AF]">{{ $doc->dentist_appointments_count ?? 0 }} موعد</span>
                        </div>
                        @php
                            $max = max(1, $doctorsPerformance->max('dentist_appointments_count'));
                            $percent = round((($doc->dentist_appointments_count ?? 0) / $max) * 100);
                        @endphp
                        <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-[#1E40AF] to-[#87A96B] rounded-full" style="width: {{ max(10, $percent) }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- أكثر الخدمات طلباً -->
    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-7">
        <div class="flex items-center space-x-3 space-x-reverse mb-6">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1E40AF] flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-sm">
                ✦
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">أكثر الخدمات الطبية والتجميلية طلباً</h3>
                <p class="text-xs text-slate-400 mt-0.5">الخدمات الأكثر إقبالاً من قبل مراجعي المركز</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($topServices as $service)
                <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-100 flex items-center justify-between hover:border-[#1E40AF]/40 hover:bg-white transition duration-200">
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs">{{ $service->name }}</h4>
                        <span class="text-[11px] text-[#87A96B] font-bold">{{ $service->appointments_count }} حجز منجز</span>
                    </div>
                    <span class="text-xs font-black text-[#1E40AF]">{{ number_format($service->price, 0) }} ر.س</span>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection