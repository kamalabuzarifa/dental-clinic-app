@extends('layouts.admin')

@section('title', 'إدارة الأطباء')

@section('content')
<div class="space-y-8">

    <!-- ترويسة الصفحة وزر الإضافة -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">إدارة الأطباء الاستشاريين</h1>
            <p class="text-xs text-slate-500 mt-1">متابعة ملفات طاقم الأطباء، الجداول الزمنية، ومستوى الإنجاز.</p>
        </div>

        <a href="{{ route('admin.doctors.create') }}" 
           class="inline-flex items-center justify-center px-8 py-3.5 text-xs font-bold text-white transition-all duration-300 bg-[#1E40AF] rounded-full shadow-lg hover:bg-blue-800 hover:shadow-blue-900/20 hover:-translate-y-0.5 self-start sm:self-auto">
            <span class="ml-1.5 text-base">+</span> إضافة طبيب جديد
        </a>
    </div>

    <!-- شبكة بطاقات الأطباء (Doctors Grid) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($doctors as $doctor)
            <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-7 flex flex-col justify-between hover:shadow-card-hover hover:-translate-y-1 transition duration-300 relative group">
                <!-- شارة الحالة / التوافر -->
                <div class="flex items-center justify-between mb-5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-[#87A96B]/15 text-[#567042] border border-[#87A96B]/30">
                        <span class="w-2 h-2 rounded-full bg-[#87A96B]"></span>
                        متاح للعمل
                    </span>

                    <span class="text-xs font-bold text-[#1E40AF] bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">
                        {{ $doctor->dentist_appointments_count ?? 0 }} حجز مسجل
                    </span>
                </div>

                <!-- الصورة والاسم والتخصص -->
                <div class="flex items-center space-x-4 space-x-reverse mb-6">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#1E40AF] to-blue-500 border-2 border-white flex items-center justify-center text-white font-black text-xl shadow-md flex-shrink-0">
                        {{ mb_substr($doctor->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <h3 class="text-base font-bold text-slate-900 truncate">{{ $doctor->name }}</h3>
                        <p class="text-xs font-bold text-[#1E40AF] mt-0.5">استشاري معتمد في طب الأسنان</p>
                        <p class="text-[11px] text-slate-400 truncate mt-1">{{ $doctor->email }}</p>
                    </div>
                </div>

                <!-- تفاصيل إضافية -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2.5 mb-6 text-xs text-slate-600">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">رقم الهاتف:</span>
                        <span class="font-bold text-slate-800">{{ $doctor->phone ?? 'غير محدد' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">فترة الدوام:</span>
                        <span class="font-bold text-slate-800">09:00 ص - 05:00 م</span>
                    </div>
                </div>

                <!-- أزرار الإجراءات (تعديل / حذف) -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                    <a href="{{ route('admin.doctors.edit', $doctor) }}" 
                       class="flex-1 text-center py-2.5 rounded-full text-xs font-bold text-slate-700 bg-slate-50 hover:bg-blue-50 hover:text-[#1E40AF] hover:border-blue-200 border border-slate-200 transition">
                        تعديل الملف
                    </a>
                    <button type="button" 
                            @click="confirmDelete('{{ route('admin.doctors.destroy', $doctor) }}', 'حذف حساب الطبيب', 'هل أنت متأكد من رغبتك في حذف حساب {{ $doctor->name }}؟ لن يتمكن من تسجيل الدخول للنظام مجدداً.')"
                            class="px-5 py-2.5 rounded-full text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">
                        حذف
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-12 text-center rounded-3xl border border-slate-100 text-slate-400">
                لا يوجد أطباء مسجلون حالياً.
            </div>
        @endforelse
    </div>

    @if(method_exists($doctors, 'hasPages') && $doctors->hasPages())
        <div class="p-4 bg-white rounded-3xl border border-slate-100">
            {{ $doctors->links() }}
        </div>
    @endif

</div>
@endsection
