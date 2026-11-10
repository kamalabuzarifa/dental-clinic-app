<div class="space-y-4">
    <!-- ترويسة الصفحة ومؤشر الحالة العامة -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#1E40AF] transition">الرئيسية</a>
                <span>/</span>
                <a href="{{ route('admin.website.identity') }}" class="hover:text-[#1E40AF] transition">إدارة الموقع الإلكتروني</a>
                <span>/</span>
                <span class="text-slate-800 font-bold">@yield('page_title', 'لوحة التحكم بالمحتوى')</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900">إدارة الموقع الإلكتروني (CMS)</h1>
            <p class="text-xs text-slate-500 mt-1">التحكم المركزي الكامل في محتوى الواجهة العامة، الهوية، الأقسام، الخدمات، الأطباء، آراء المرضى، وإعدادات SEO.</p>
        </div>

        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a href="{{ route('home') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-full text-xs font-bold shadow-md shadow-blue-700/20 hover:from-blue-700 hover:to-blue-800 hover:-translate-y-0.5 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>معاينة الموقع الحي ↗</span>
            </a>
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-white rounded-full border border-slate-200 text-[11px] font-semibold text-slate-700 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-[#87A96B] animate-pulse"></span>
                <span>تحديث فوري للموقع</span>
            </div>
        </div>
    </div>

    <!-- شريط التبويبات السريع (Quick Navigation Pills) -->
    <div class="flex items-center gap-1.5 p-1.5 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-x-auto text-xs font-bold">
        <a href="{{ route('admin.website.identity') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ request()->routeIs('admin.website.identity') ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-[#1E40AF] hover:bg-slate-50' }}">
            <span>🏥</span>
            <span>الهوية والشعار</span>
        </a>

        <a href="{{ route('admin.website.sections') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ request()->routeIs('admin.website.sections*') ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-[#1E40AF] hover:bg-slate-50' }}">
            <span>📑</span>
            <span>أقسام الرئيسية</span>
        </a>

        <a href="{{ route('admin.website.about') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ request()->routeIs('admin.website.about') ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-[#1E40AF] hover:bg-slate-50' }}">
            <span>📖</span>
            <span>صفحة من نحن</span>
        </a>

        <a href="{{ route('admin.website.services') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ request()->routeIs('admin.website.services') ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-[#1E40AF] hover:bg-slate-50' }}">
            <span>🩺</span>
            <span>عرض الخدمات</span>
        </a>

        <a href="{{ route('admin.website.doctors') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ request()->routeIs('admin.website.doctors') ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-[#1E40AF] hover:bg-slate-50' }}">
            <span>👨‍⚕️</span>
            <span>عرض الأطباء</span>
        </a>

        <a href="{{ route('admin.website.testimonials') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ request()->routeIs('admin.website.testimonials*') ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-[#1E40AF] hover:bg-slate-50' }}">
            <span>⭐</span>
            <span>آراء المرضى</span>
        </a>

        <a href="{{ route('admin.website.faqs') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ request()->routeIs('admin.website.faqs*') ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-[#1E40AF] hover:bg-slate-50' }}">
            <span>❓</span>
            <span>الأسئلة الشائعة</span>
        </a>

        <a href="{{ route('admin.website.seo') }}" 
           class="flex items-center gap-2 px-3.5 py-2 rounded-xl whitespace-nowrap transition {{ request()->routeIs('admin.website.seo') ? 'bg-[#1E40AF] text-white shadow-sm' : 'text-slate-600 hover:text-[#1E40AF] hover:bg-slate-50' }}">
            <span>🔍</span>
            <span>محركات البحث (SEO)</span>
        </a>
    </div>
</div>
