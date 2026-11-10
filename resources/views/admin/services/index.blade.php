@extends('layouts.admin')

@section('title', 'الخدمات الطبية والتجميلية')

@section('content')
<div class="space-y-8">

    <!-- ترويسة الصفحة وزر الإضافة -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">إدارة الخدمات والأسعار</h1>
            <p class="text-xs text-slate-500 mt-1">تحديد الإجراءات الطبية والتجميلية، مدة كل جلسة، والتسعيرة الرسمية بالمركز.</p>
        </div>

        <a href="{{ route('admin.services.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3 text-xs font-bold text-white transition-all duration-300 bg-[#1E40AF] rounded-full shadow-lg hover:bg-blue-800 hover:shadow-blue-900/20 hover:-translate-y-0.5 self-start sm:self-auto">
            <span class="ml-1.5 text-base">+</span> إضافة خدمة جديدة
        </a>
    </div>

    <!-- جدول الخدمات الطبية -->
    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 overflow-hidden">
        <div class="p-6 sm:p-7 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-3 space-x-reverse">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center font-black text-sm shadow-sm">
                    ✦
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">قائمة الخدمات المعتمدة بالمركز</h2>
                    <p class="text-xs text-slate-400 mt-0.5">الخدمات الطبية التجميلية والعلاجية المتاحة للحجز</p>
                </div>
            </div>
            <span class="text-xs font-bold text-[#1E40AF] bg-blue-50 border border-blue-200 px-3.5 py-1.5 rounded-full">
                {{ $services->total() ?? $services->count() }} خدمات مسجلة
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#F8FAFC] text-slate-900 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">اسم الخدمة الطبية</th>
                        <th class="px-6 py-4">الوصف</th>
                        <th class="px-6 py-4">مدة الجلسة</th>
                        <th class="px-6 py-4">السعر الرسمي</th>
                        <th class="px-6 py-4 text-center">عدد الحجوزات</th>
                        <th class="px-6 py-4 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($services as $service)
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4 font-bold text-slate-900 text-sm">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1E40AF] flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-sm">
                                        ✦
                                    </div>
                                    <span>{{ $service->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-xs text-slate-500">
                                <span class="line-clamp-1">{{ $service->description ?? 'جلسة سنية متكاملة بأحدث التقنيات.' }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 px-3 py-1 rounded-full font-bold">
                                    <span>⏰</span> {{ $service->duration_minutes }} دقيقة
                                </span>
                            </td>
                            <td class="px-6 py-4 font-black text-slate-900 text-sm">
                                {{ number_format($service->price, 0) }} <span class="text-xs text-[#87A96B] font-bold">ر.س</span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#87A96B]/15 text-[#567042] border border-[#87A96B]/30">
                                    {{ $service->appointments_count ?? 0 }} حجز
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.services.edit', $service) }}" 
                                       class="px-3.5 py-1.5 rounded-full text-xs font-bold text-slate-700 bg-slate-50 hover:bg-blue-50 hover:text-[#1E40AF] border border-slate-200 transition">
                                        تعديل
                                    </a>
                                    
                                    <button type="button" 
                                            @click="confirmDelete('{{ route('admin.services.destroy', $service) }}', 'حذف خدمة {{ $service->name }}', 'هل أنت متأكد من رغبتك في حذف هذه الخدمة الطبية؟ لن يتمكن المرضى من حجزها بعد الآن.')"
                                            class="px-3.5 py-1.5 rounded-full text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                                        حذف
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                لا توجد خدمات مضافة حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($services, 'hasPages') && $services->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $services->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
