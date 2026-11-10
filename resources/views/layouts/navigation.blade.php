<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50 shadow-[0_4px_25px_rgba(30,64,175,0.04)] transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <!-- Right side: Logo & Navigation Links -->
            <div class="flex items-center space-x-8 space-x-reverse">
                <!-- Medical Clinic Brand -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 space-x-reverse group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-700 to-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-700/20 group-hover:scale-105 transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-lg font-black text-slate-900 tracking-tight block group-hover:text-blue-700 transition">مركز ريادة لطب الأسنان</span>
                        <span class="text-[10px] text-[#87A96B] font-extrabold tracking-wider uppercase block -mt-0.5">RIYADA DENTAL CARE</span>
                    </div>
                </a>

                <!-- Nav Links Desktop -->
                <div class="hidden md:flex items-center space-x-2 space-x-reverse mr-4">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-full text-xs font-bold transition duration-200 {{ request()->routeIs('*.dashboard') || request()->routeIs('dashboard') ? 'bg-blue-700 text-white shadow-sm shadow-blue-700/25' : 'text-slate-600 hover:text-blue-700 hover:bg-blue-50/60' }}">
                        لوحة التحكم
                    </a>

                    @if(Auth::user()->isPatient())
                        <a href="{{ route('patient.appointments.create') }}" class="px-5 py-2 rounded-full text-xs font-bold transition duration-200 {{ request()->routeIs('patient.appointments.create') ? 'bg-blue-700 text-white shadow-md' : 'bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 shadow-sm shadow-blue-600/20' }}">
                            + حجز موعد جديد
                        </a>
                    @endif

                    <a href="{{ route('home') }}" target="_blank" class="px-3.5 py-2 rounded-full text-xs font-bold text-slate-500 hover:text-blue-700 hover:bg-slate-50 transition flex items-center gap-1.5">
                        <span>واجهة المركز</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    </a>
                </div>
            </div>

            <!-- Left side: User Profile & Role Pill -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4 sm:space-x-reverse">
                @php
                    $roleBadges = [
                        'admin' => ['label' => 'مدير المركز', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200/60'],
                        'dentist' => ['label' => 'طبيب أسنان', 'class' => 'bg-blue-50 text-blue-700 border-blue-200/60'],
                        'receptionist' => ['label' => 'فريق الاستقبال', 'class' => 'bg-amber-50 text-amber-700 border-amber-200/60'],
                        'patient' => ['label' => 'مريض مسجل', 'class' => 'bg-[#87A96B]/10 text-[#557143] border-[#87A96B]/30'],
                    ];
                    $badge = $roleBadges[Auth::user()->role] ?? ['label' => Auth::user()->role, 'class' => 'bg-slate-100 text-slate-700 border-slate-200'];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold border shadow-xs {{ $badge['class'] }}">
                    {{ $badge['label'] }}
                </span>

                <!-- User Dropdown Menu -->
                <div class="relative" x-data="{ openMenu: false }">
                    <button @click="openMenu = !openMenu" @click.outside="openMenu = false" class="flex items-center space-x-3 space-x-reverse bg-white hover:bg-slate-50/80 border border-slate-200/80 px-4 py-2 rounded-full shadow-[0_2px_10px_rgba(0,0,0,0.03)] hover:shadow-md transition duration-200">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-blue-700 to-blue-600 text-white flex items-center justify-center text-xs font-black shadow-xs">
                            {{ mb_substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>

                    <div x-show="openMenu" x-transition class="absolute left-0 mt-2 w-52 bg-white rounded-2xl shadow-[0_12px_35px_rgba(30,64,175,0.08)] border border-slate-100 py-1.5 z-50">
                        <div class="px-4 py-2.5 border-b border-slate-100">
                            <span class="block text-xs font-bold text-slate-900">{{ Auth::user()->name }}</span>
                            <span class="block text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</span>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-blue-50/50 hover:text-blue-700 text-right transition">
                            الملف الشخصي
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-right px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger Button for Mobile -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2.5 rounded-2xl text-slate-500 hover:text-blue-700 hover:bg-blue-50/50 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-slate-100 bg-white/95 backdrop-blur-md px-4 pt-3 pb-5 space-y-2">
        <div class="py-2 border-b border-slate-100 flex items-center justify-between">
            <div>
                <div class="font-bold text-slate-900 text-sm">{{ Auth::user()->name }}</div>
                <div class="text-[11px] text-slate-400">{{ Auth::user()->email }}</div>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['class'] }}">
                {{ $badge['label'] }}
            </span>
        </div>
        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-800 hover:bg-blue-50 hover:text-blue-700">
            لوحة التحكم
        </a>
        @if(Auth::user()->isPatient())
            <a href="{{ route('patient.appointments.create') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 text-center">
                + حجز موعد جديد
            </a>
        @endif
        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50">
            الملف الشخصي
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-right px-3 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50">
                تسجيل الخروج
            </button>
        </form>
    </div>
</nav>
