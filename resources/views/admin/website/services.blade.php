@extends('layouts.admin')

@section('title', 'عرض الخدمات الطبية - إدارة الموقع الإلكتروني')
@section('page_title', 'عرض الخدمات على الموقع')

@section('content')
<div class="space-y-6">
    <!-- شريط التبويبات العلوي -->
    @include('admin.website.partials.tabs')

    <div class="bg-white rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] border border-slate-100 p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div class="flex items-center space-x-3.5 space-x-reverse">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-xl shadow-sm flex-shrink-0">
                    🩺
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900">التحكم في ظهور الخدمات بالواجهة العامة</h2>
                    <p class="text-xs text-slate-400 mt-0.5">تحديد أي الخدمات تظهر للزوار، أيها مميزة في الصفحة الرئيسية، والترتيب الرقمي</p>
                </div>
            </div>

            <a href="{{ route('admin.services.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1E40AF] text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition">
                <span>+</span>
                <span>إضافة خدمة جديدة</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">الخدمة</th>
                        <th class="py-3 px-4">السعر والمدة</th>
                        <th class="py-3 px-4 text-center">الظهور بالموقع</th>
                        <th class="py-3 px-4 text-center">خدمة مميزة (الرئيسية)</th>
                        <th class="py-3 px-4 text-center">ترتيب العرض</th>
                        <th class="py-3 px-4 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($services as $service)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-xl bg-blue-50 text-[#1E40AF] flex items-center justify-center font-bold text-base shadow-xs">
                                        {{ $service->icon ?: '🦷' }}
                                    </span>
                                    <div>
                                        <a href="{{ route('services.show', $service->id) }}" target="_blank" class="font-black text-slate-900 hover:text-[#1E40AF] transition">
                                            {{ $service->name }} ↗
                                        </a>
                                        <p class="text-[11px] text-slate-400 line-clamp-1 max-w-xs">{{ $service->description }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-4 font-bold text-slate-700">
                                <div>{{ number_format($service->price, 0) }} ₪</div>
                                <span class="text-[10px] text-slate-400 font-normal">{{ $service->duration_minutes }} دقيقة</span>
                            </td>

                            <form method="POST" action="{{ route('admin.website.services.visibility', $service->id) }}">
                                @csrf
                                @method('PATCH')
                                <td class="py-4 px-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="show_on_website" value="1" {{ $service->show_on_website ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                                    </label>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_featured" value="1" {{ $service->is_featured ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1E40AF]"></div>
                                    </label>
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <input type="number" name="sort_order" value="{{ $service->sort_order ?? 0 }}" min="0" max="99" class="w-14 text-center text-xs font-bold rounded-lg border border-slate-200 p-1 bg-slate-50 focus:bg-white focus:border-[#1E40AF] outline-none">
                                </td>

                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-[#1E40AF] hover:bg-[#1E40AF] hover:text-white transition" title="حفظ إعدادات هذا العنصر">
                                            حفظ ✓
                                        </button>
                                        <a href="{{ route('admin.services.edit', $service->id) }}" class="p-1 text-slate-400 hover:text-slate-700 transition" title="تعديل تفاصيل الخدمة">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </form>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-400 font-bold">لا توجد خدمات مضافة حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
