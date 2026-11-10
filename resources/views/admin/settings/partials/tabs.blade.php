<div class="space-y-4">
    <!-- ترويسة الصفحة ومؤشر الحالة العامة (الاعتماد على Sidebar للتنقل المباشر) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#1E40AF] transition">الرئيسية</a>
                <span>/</span>
                <a href="{{ route('admin.settings.clinic') }}" class="hover:text-[#1E40AF] transition">الإعدادات</a>
                <span>/</span>
                <span class="text-slate-800 font-bold">@yield('page_title', 'إعدادات النظام')</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900">إعدادات النظام والمركز</h1>
            <p class="text-xs text-slate-500 mt-1">إدارة الهوية المؤسسية، ضوابط الحجوزات، مصفوفة الصلاحيات، حسابات المستخدمين، وإعدادات الأمان.</p>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-auto px-4 py-2 bg-white rounded-full border border-slate-200/80 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] text-xs font-semibold text-slate-700">
            <span class="w-2.5 h-2.5 rounded-full {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'bg-rose-500 animate-ping' : 'bg-[#87A96B] animate-pulse' }}"></span>
            <span>{{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'وضع الصيانة مفعّل (الحجز معلّق)' : 'قاعدة البيانات متصلة والنظام نشط' }}</span>
        </div>
    </div>
</div>
