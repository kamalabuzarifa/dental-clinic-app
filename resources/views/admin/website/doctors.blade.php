@extends('layouts.admin')

@section('title', 'عرض الأطباء بالواجهة العامة - إدارة الموقع الإلكتروني')
@section('page_title', 'عرض الأطباء على الموقع')

@section('content')
<div class="space-y-6">
    <!-- شريط التبويبات العلوي -->
    @include('admin.website.partials.tabs')

    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div class="flex items-center space-x-3.5 space-x-reverse">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                    👨‍⚕️
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900">إدارة ظهور الكادر الطبي في الواجهة العامة (/doctors)</h2>
                    <p class="text-xs text-slate-400 mt-0.5">التحكم في إظهار أو إخفاء الطبيب، النبذة التعريفية العامة، التخصص الدقيق، وترتيب العرض</p>
                </div>
            </div>

            <a href="{{ route('admin.doctors.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1E40AF] text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition">
                <span>+</span>
                <span>إضافة طبيب جديد للنظام</span>
            </a>
        </div>

        <div class="space-y-4">
            @forelse($doctors as $doctor)
                <div class="p-5 rounded-2xl border transition duration-200 {{ $doctor->show_on_website ? 'bg-slate-50/70 border-slate-200' : 'bg-slate-100/50 border-dashed border-slate-300 opacity-80' }}">
                    <form method="POST" action="{{ route('admin.website.doctors.visibility', $doctor->id) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <!-- بيانات الطبيب الأساسية -->
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 overflow-hidden flex items-center justify-center text-slate-500 font-bold shadow-xs flex-shrink-0">
                                    @if($doctor->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($doctor->avatar))
                                        <img src="{{ asset('storage/' . $doctor->avatar) }}" alt="{{ $doctor->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-sm font-black text-[#1E40AF]">{{ mb_substr($doctor->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-black text-slate-900">{{ $doctor->name }}</h3>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $doctor->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                            {{ $doctor->is_active ? 'حساب نشط' : 'حساب معطل' }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $doctor->show_on_website ? 'bg-blue-100 text-[#1E40AF]' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $doctor->show_on_website ? 'ظاهر بالموقع' : 'مخفي من الموقع' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $doctor->email }} • {{ $doctor->phone ?? 'لا يوجد هاتف' }}</p>
                                </div>
                            </div>

                            <!-- مفتاح الظهور + الترتيب + الحفظ السريع -->
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-bold text-slate-700">الظهور في الموقع:</label>
                                    <input type="checkbox" name="show_on_website" value="1" {{ $doctor->show_on_website ? 'checked' : '' }} class="rounded text-[#1E40AF] focus:ring-[#1E40AF] h-4 w-4">
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <label class="text-xs font-bold text-slate-700">الترتيب:</label>
                                    <input type="number" name="sort_order" value="{{ $doctor->sort_order ?? 0 }}" min="0" max="99" class="w-14 text-center text-xs font-bold rounded-lg border border-slate-200 p-1.5 bg-white focus:border-[#1E40AF] outline-none">
                                </div>

                                <button type="submit" class="px-4 py-1.5 rounded-xl text-xs font-bold bg-[#1E40AF] text-white hover:bg-blue-700 shadow-sm transition">
                                    تحديث البيانات ✓
                                </button>
                            </div>
                        </div>

                        <!-- التخصص والنبذة والصورة العامة -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t border-slate-200/80">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">المسمى التخصصي المعروض بالموقع:</label>
                                <input type="text" name="specialty" value="{{ $doctor->specialty ?? 'طبيب وجراح أسنان' }}" placeholder="مثلاً: استشاري طب وجراحة الفم والأسنان" class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-white focus:border-[#1E40AF] outline-none">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">النبذة التعريفية العامة للمريض (Bio):</label>
                                <textarea name="bio" rows="2" placeholder="اكتب نبذة مختصرة عن خبرات واعتمادات الطبيب تظهر في بطاقة الطبيب وصفحته الخاصة..." class="w-full text-xs font-bold rounded-xl border border-slate-200 p-2.5 bg-white focus:border-[#1E40AF] outline-none">{{ $doctor->bio }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 font-bold">لا يوجد أطباء مسجلين في النظام حتى الآن.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
