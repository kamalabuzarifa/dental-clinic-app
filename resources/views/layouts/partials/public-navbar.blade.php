<header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.04)]"
        x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        
        <!-- Brand Logo & Name -->
        <a href="{{ route('home') }}" class="flex items-center space-x-3 space-x-reverse group">
            @if(!empty($settings['clinic_logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['clinic_logo']))
                <img src="{{ asset('storage/' . $settings['clinic_logo']) }}" 
                     alt="{{ $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان' }}" 
                     class="w-11 h-11 rounded-2xl object-contain bg-white border border-slate-100 p-1 shadow-md shadow-blue-700/20 group-hover:scale-105 transition duration-300">
            @else
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-700 to-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-700/20 group-hover:scale-105 transition duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            @endif
            <div>
                <span class="text-xl font-black text-slate-900 tracking-tight block group-hover:text-blue-700 transition">
                    {{ $settings['clinic_name'] ?? 'مركز ريادة لطب الأسنان' }}
                </span>
                <span class="text-[10px] text-[#87A96B] font-extrabold tracking-widest uppercase block -mt-0.5">
                    {{ $settings['clinic_name_en'] ?? 'RIYADA DENTAL CARE' }}
                </span>
            </div>
        </a>

        <!-- Desktop Navigation Links (Multi-page with real routes and active states) -->
        <nav class="hidden lg:flex items-center space-x-1 space-x-reverse text-sm font-bold">
            <!-- الرئيسية -->
            <a href="{{ route('home') }}" 
               class="px-3.5 py-2 rounded-xl transition duration-200 {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700 font-black shadow-xs' : 'text-slate-600 hover:text-blue-700 hover:bg-slate-50' }}">
                الرئيسية
            </a>

            <!-- من نحن -->
            <a href="{{ route('about') }}" 
               class="px-3.5 py-2 rounded-xl transition duration-200 {{ request()->routeIs('about') ? 'bg-blue-50 text-blue-700 font-black shadow-xs' : 'text-slate-600 hover:text-blue-700 hover:bg-slate-50' }}">
                من نحن
            </a>

            <!-- الخدمات الطبية -->
            <a href="{{ route('services.index') }}" 
               class="px-3.5 py-2 rounded-xl transition duration-200 {{ request()->routeIs('services.*') ? 'bg-blue-50 text-blue-700 font-black shadow-xs' : 'text-slate-600 hover:text-blue-700 hover:bg-slate-50' }}">
                الخدمات
            </a>

            <!-- الأطباء -->
            <a href="{{ route('doctors.index') }}" 
               class="px-3.5 py-2 rounded-xl transition duration-200 {{ request()->routeIs('doctors.*') ? 'bg-blue-50 text-blue-700 font-black shadow-xs' : 'text-slate-600 hover:text-blue-700 hover:bg-slate-50' }}">
                الأطباء
            </a>

            <!-- حجز موعد -->
            <a href="{{ route('appointments.book') }}" 
               class="px-3.5 py-2 rounded-xl transition duration-200 {{ request()->routeIs('appointments.book') ? 'bg-blue-50 text-blue-700 font-black shadow-xs' : 'text-slate-600 hover:text-blue-700 hover:bg-slate-50' }}">
                حجز موعد
            </a>

            <!-- تواصل معنا -->
            <a href="{{ route('contact') }}" 
               class="px-3.5 py-2 rounded-xl transition duration-200 {{ request()->routeIs('contact*') ? 'bg-blue-50 text-blue-700 font-black shadow-xs' : 'text-slate-600 hover:text-blue-700 hover:bg-slate-50' }}">
                تواصل معنا
            </a>
        </nav>

        <!-- Right Side: User Actions / CTA -->
        <div class="hidden sm:flex items-center space-x-3 space-x-reverse">
            @auth
                <!-- Authenticated Dashboard link -->
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-black text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full shadow-md shadow-blue-700/20 hover:from-blue-700 hover:to-blue-800 hover:-translate-y-0.5">
                    <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    لوحة التحكم
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="p-2.5 text-xs font-bold text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-full transition" 
                            title="تسجيل الخروج">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            @else
                <!-- Guest Links -->
                <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold text-slate-700 hover:text-blue-700 hover:bg-slate-50 rounded-xl transition">
                    تسجيل الدخول
                </a>
                <a href="{{ route('appointments.book') }}" 
                   class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-black text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full shadow-md shadow-blue-700/20 hover:from-blue-700 hover:to-blue-800 hover:-translate-y-0.5">
                    احجز موعدك الآن
                </a>
            @endauth
        </div>

        <!-- Mobile Menu Hamburger Button -->
        <div class="flex items-center lg:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    type="button" 
                    class="p-2 rounded-xl text-slate-600 hover:text-blue-700 hover:bg-slate-100 transition focus:outline-none"
                    aria-label="القائمة">
                <svg class="w-6 h-6" x-show="!mobileMenuOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg class="w-6 h-6" x-show="mobileMenuOpen" style="display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

    </div>

    <!-- Mobile Drawer / Dropdown Menu -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="lg:hidden border-t border-slate-100 bg-white/98 backdrop-blur-md px-4 pt-3 pb-6 space-y-2 shadow-xl"
         style="display: none;">

        <a href="{{ route('home') }}" 
           class="block px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-50' }}">
            الرئيسية
        </a>

        <a href="{{ route('about') }}" 
           class="block px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('about') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-50' }}">
            من نحن
        </a>

        <a href="{{ route('services.index') }}" 
           class="block px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('services.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-50' }}">
            الخدمات الطبية
        </a>

        <a href="{{ route('doctors.index') }}" 
           class="block px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('doctors.*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-50' }}">
            فريق الأطباء
        </a>

        <a href="{{ route('appointments.book') }}" 
           class="block px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('appointments.book') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-50' }}">
            حجز موعد إلكتروني
        </a>

        <a href="{{ route('contact') }}" 
           class="block px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('contact*') ? 'bg-blue-50 text-blue-700 font-black' : 'text-slate-700 hover:bg-slate-50' }}">
            تواصل معنا
        </a>

        <div class="pt-4 border-t border-slate-100 space-y-2">
            @auth
                <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-white bg-blue-700 rounded-xl shadow-md">
                    الانتقال للوحة التحكم
                </a>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-center px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition">
                        تسجيل الخروج
                    </button>
                </form>
            @else
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="text-center px-4 py-2.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}" class="text-center px-4 py-2.5 text-xs font-bold text-white bg-blue-700 hover:bg-blue-800 rounded-xl transition">
                        حساب مريض جديد
                    </a>
                </div>
            @endauth
        </div>

    </div>
</header>
