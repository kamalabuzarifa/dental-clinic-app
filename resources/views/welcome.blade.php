<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان' }} | الرعاية الطبية المتقدمة والابتسامة المشرقة</title>

    <!-- Google Fonts: Tajawal & Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Tajawal', 'Cairo', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            blue: {
                                50: '#EFF6FF',
                                100: '#DBEAFE',
                                200: '#BFDBFE',
                                300: '#93C5FD',
                                400: '#60A5FA',
                                500: '#3B82F6',
                                600: '#2563EB',
                                700: '#1E40AF',
                                800: '#1E3A8A',
                                900: '#172554',
                            },
                            sage: {
                                50: '#F4F7F2',
                                100: '#E7EFE4',
                                200: '#D2E1CC',
                                300: '#B6CFAC',
                                400: '#9EBF93',
                                500: '#87A96B',
                                600: '#6F8F56',
                                700: '#567042',
                                800: '#435634',
                                900: '#344329',
                            },
                        },
                        primary: '#1E40AF',
                        accent: '#87A96B',
                        surface: '#FFFFFF',
                    },
                    boxShadow: {
                        'soft-shadow': '0 10px 30px -5px rgba(30, 64, 175, 0.08)',
                        'soft-shadow-hover': '0 20px 40px -10px rgba(30, 64, 175, 0.12)',
                        'blue-glow': '0 10px 25px -3px rgba(30, 64, 175, 0.25)',
                        'sage-glow': '0 10px 25px -3px rgba(135, 169, 107, 0.25)',
                    },
                    keyframes: {
                        'fade-in-up': {
                            '0%': { opacity: '0', transform: 'translateY(24px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        'float': {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-8px)' },
                        },
                    },
                    animation: {
                        'fade-in-up': 'fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'float': 'float 4s ease-in-out infinite',
                    },
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Tajawal', sans-serif; }
        .blue-gradient-text {
            background: linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .medical-gradient {
            background: linear-gradient(135deg, #1E40AF 0%, #1D4ED8 100%);
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased selection:bg-[#1E40AF] selection:text-white overflow-x-hidden">

    <!-- Top Announcement Bar -->
    <div class="bg-blue-900 text-blue-100 text-xs py-2.5 px-4 border-b border-blue-800/80">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center space-x-3 space-x-reverse">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#87A96B] text-white shadow-xs">عيادات معتمدة</span>
                <span class="font-medium">{{ $settings['announcement_bar'] ?? 'مركز ريادة لطب وجراحة وتجميل الأسنان: أحدث التقنيات الرقمية المريحة للأسرة' }}</span>
            </div>
            <div class="flex items-center space-x-4 space-x-reverse text-[11px] text-blue-200">
                <span>ساعات العمل: {{ $workingHoursText ?? 'السبت - الخميس (09:00 ص - 09:00 م)' }}</span>
                <span>•</span>
                <span class="text-white font-bold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-[#87A96B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $settings['clinic_phone'] ?? '0501234567' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Clean Medical Glassmorphism Navbar -->
    <header class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.04)] transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 space-x-reverse group">
                @if(!empty($settings['clinic_logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['clinic_logo']))
                    <img src="{{ asset('storage/' . $settings['clinic_logo']) }}" alt="{{ $settings['clinic_name'] ?? 'مركز ريادة' }}" class="w-11 h-11 rounded-2xl object-contain bg-white border border-slate-100 p-1 shadow-md shadow-blue-700/20 group-hover:scale-105 transition duration-300">
                @else
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-700 to-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-700/20 group-hover:scale-105 transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                @endif
                <div>
                    <span class="text-xl font-black text-slate-900 tracking-tight block group-hover:text-blue-700 transition">مركز ريادة لطب الأسنان</span>
                    <span class="text-[10px] text-[#87A96B] font-extrabold tracking-widest uppercase block -mt-0.5">RIYADA DENTAL CARE</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-8 space-x-reverse text-sm font-bold text-slate-600">
                <a href="#services" class="hover:text-blue-700 transition duration-200">الخدمات الطبية</a>
                <a href="#features" class="hover:text-blue-700 transition duration-200">مميزات المركز</a>
                <a href="#dentists" class="hover:text-blue-700 transition duration-200">فريق الأطباء</a>
                <a href="#steps" class="hover:text-blue-700 transition duration-200">خطوات الحجز</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center space-x-3 space-x-reverse">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-7 py-3 text-sm font-bold text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full shadow-md shadow-blue-700/20 hover:from-blue-700 hover:to-blue-800 hover:-translate-y-0.5">
                            لوحة التحكم 
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-bold text-slate-700 hover:text-blue-700 transition">
                            تسجيل الدخول
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-7 py-3 text-sm font-bold text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full shadow-md shadow-blue-700/20 hover:from-blue-700 hover:to-blue-800 hover:-translate-y-0.5">
                            حساب مريض جديد
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Warm & Cheerful Medical Hero Section -->
    <section class="relative overflow-hidden pt-16 pb-24 lg:pt-24 lg:pb-32 bg-gradient-to-b from-white via-blue-50/20 to-[#F8FAFC]">
        <!-- Decorative Ambient Medical Glows -->
        <div class="absolute top-1/4 right-0 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-[#87A96B]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                <!-- Text Column -->
                <div class="lg:col-span-7 text-right">
                    <!-- Medical Trust Pill -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50/80 border border-blue-200/60 text-slate-800 text-xs font-bold mb-8 shadow-xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#87A96B] animate-pulse"></span>
                        <span class="text-blue-800">رعاية سنية متقدمة ومريحة</span>
                        <span class="text-slate-300">|</span>
                        <span class="text-slate-600 font-medium">نظام حجز إلكتروني مؤكد بالدقيقة</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 leading-[1.25] mb-6 tracking-tight">
                        ابتسامة مشرقة وصحية، <br>
                        <span class="blue-gradient-text">برعاية طبية دقيقة وتجربة علاجية بلا قلق.</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 mb-10 max-w-2xl leading-relaxed font-normal">
                        نقدم بيئة طبية رحبة ومريحة مجهزة بأحدث أجهزة التصوير ثلاثي الأبعاد والتقنيات الرقمية غير المؤلمة، مع نظام جدولة ذكي يمنع الانتظار ويمنحك الراحة الكاملة.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap items-center gap-4">
                        @if(($settings['maintenance_mode'] ?? '0') == '1')
                            <div class="inline-flex items-center gap-2 px-6 py-3.5 text-sm font-bold bg-amber-500/10 text-amber-800 border border-amber-300 rounded-full shadow-xs">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <span>نظام الحجز الإلكتروني في وضع الصيانة والتحديث مؤقتاً</span>
                            </div>
                            @if(!empty($settings['social_whatsapp']))
                                <a href="{{ $settings['social_whatsapp'] }}" target="_blank" class="inline-flex items-center justify-center px-6 py-3.5 text-sm font-bold text-white transition-all duration-300 bg-emerald-600 hover:bg-emerald-700 rounded-full shadow-md">
                                    تواصل للحجز عبر واتساب
                                </a>
                            @endif
                        @else
                            @auth
                                <a href="{{ route('patient.appointments.create') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full shadow-lg shadow-blue-700/25 hover:from-blue-700 hover:to-blue-800 hover:-translate-y-1">
                                    احجز موعدك الطبي الآن
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full shadow-lg shadow-blue-700/25 hover:from-blue-700 hover:to-blue-800 hover:-translate-y-1">
                                    احجز استشارتك الأولى
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-blue-700 transition-all duration-300 bg-white border-2 border-blue-600/20 rounded-full shadow-sm hover:border-blue-600 hover:bg-blue-50/50 hover:-translate-y-1">
                                    تسجيل الدخول للمرضى
                                </a>
                            @endauth
                        @endif
                    </div>

                    <!-- Trust Indicators -->
                    <div class="mt-14 pt-8 border-t border-slate-200/80 grid grid-cols-3 gap-6 text-right">
                        <div>
                            <div class="text-3xl font-black text-blue-700">99.8%</div>
                            <div class="text-xs text-slate-500 font-medium mt-1">نسبة رضا مرضانا عن الرعاية</div>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-[#87A96B]">0 دقيقة</div>
                            <div class="text-xs text-slate-500 font-medium mt-1">انعدام وقت الانتظار بمواعيد دقيقة</div>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-slate-900">+10,000</div>
                            <div class="text-xs text-slate-500 font-medium mt-1">حالة علاجية وتجميلية ناجحة</div>
                        </div>
                    </div>
                </div>

                <!-- Pure White Graphic Card with Real Clinic Aesthetics -->
                <div class="lg:col-span-5">
                    <div class="relative">
                        <!-- Ambient Border Glow -->
                        <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-blue-600/15 to-[#87A96B]/20 blur-xl"></div>

                        <div class="relative bg-white rounded-3xl p-8 border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.08)]">
                            <!-- Visual Banner Image -->
                            <div class="relative h-48 w-full rounded-2xl overflow-hidden mb-6 shadow-xs border border-slate-100">
                                <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=800&q=80" alt="عيادة ريادة لطب الأسنان" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-blue-900/80 via-transparent to-transparent flex items-end p-4">
                                    <div class="text-white">
                                        <span class="inline-block text-[10px] font-bold bg-[#87A96B] text-white px-2.5 py-0.5 rounded-full mb-1">عيادات متطورة</span>
                                        <h4 class="text-xs font-black">غرف علاجية مجهزة بأعلى معايير التعقيم والراحة</h4>
                                    </div>
                                </div>
                            </div>

                            <!-- Header of Card -->
                            <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-5">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg font-bold shadow-xs">
                                        <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-black text-slate-900">تجربة المريض المريحة</h3>
                                        <p class="text-[11px] text-slate-400">RIYADA CARE EXPERIENCE</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold bg-[#87A96B]/15 text-[#557143] border border-[#87A96B]/30 px-3 py-1 rounded-full">
                                    حجز فوري متاح
                                </span>
                            </div>

                            <!-- Highlights -->
                            <div class="space-y-3">
                                <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">1</div>
                                        <span class="text-xs font-bold text-slate-800">أجهزة تصوير رقمية ثلاثية الأبعاد 3D</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-blue-700">تشخيص دقيق</span>
                                </div>

                                <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</div>
                                        <span class="text-xs font-bold text-slate-800">استشاريون معتمدون في التقويم والزراعة</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-blue-700">خبرة متخصصة</span>
                                </div>

                                <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div class="w-7 h-7 rounded-full bg-[#87A96B]/20 text-[#557143] flex items-center justify-center text-xs font-bold">3</div>
                                        <span class="text-xs font-bold text-slate-800">تعقيم متكامل بأحدث المعايير الدولية</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-[#87A96B]">أمان تام 100%</span>
                                </div>
                            </div>

                            <!-- Guarantee Box -->
                            <div class="mt-5 p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-center">
                                <p class="text-xs text-blue-950 font-bold mb-0.5">جدولة دقيقة تمنع تضارب المواعيد</p>
                                <p class="text-[11px] text-blue-700/80">حجزك مؤكد بالدقيقة مع الطبيب المختار لضمان راحتك ووقتك الثمين.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-24 bg-white border-y border-slate-100 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-4 py-1.5 rounded-full border border-[#87A96B]/30">الخدمات الطبية والتجميلية</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-4 mb-4">خدمات متكاملة لصحة وجمال أسنانك</h2>
                <p class="text-slate-500 text-sm sm:text-base leading-relaxed">نقدم باقة شاملة من العلاجات التخصصية والتجميلية بإشراف أطباء متميزين وبأعلى معايير الراحة.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($services ?? [] as $service)
                    <!-- Pure White Service Card -->
                    <div class="relative p-8 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] transition-all duration-300 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] hover:-translate-y-1.5 group flex flex-col justify-between">
                        <!-- Top Accent Line -->
                        <div class="absolute top-0 right-0 left-0 h-1 bg-gradient-to-r from-transparent via-blue-600 to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>

                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-lg group-hover:bg-blue-700 group-hover:text-white transition duration-300 shadow-xs">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-500 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                                    {{ $service->duration_minutes }} دقيقة
                                </span>
                            </div>

                            <h3 class="text-lg font-black text-slate-900 mb-2 group-hover:text-blue-700 transition">{{ $service->name }}</h3>
                            <p class="text-xs text-slate-500 mb-8 leading-relaxed line-clamp-3">
                                {{ $service->description ?? 'علاج وتجميل احترافي متقدم باستخدام أفضل الخامات الطبية المعتمدة لراحة قصوى ونتائج تدوم.' }}
                            </p>
                        </div>

                        <div class="pt-5 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] text-slate-400 block">السعر التقديري</span>
                                <span class="text-xl font-black text-slate-900">{{ number_format($service->price, 0) }} <span class="text-xs font-bold text-blue-700">ر.س</span></span>
                            </div>
                            @if(($settings['maintenance_mode'] ?? '0') == '1')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500">
                                    الحجز متوقف مؤقتاً
                                </span>
                            @else
                                <a href="{{ auth()->check() ? route('patient.appointments.create') : route('login') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-full text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-700 hover:text-white transition duration-200">
                                    احجز الخدمة ←
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-slate-400 text-sm">
                        جاري تحديث قائمة الخدمات الطبية...
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Why Choose Us / Features -->
    <section id="features" class="py-24 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-4 py-1.5 rounded-full border border-[#87A96B]/30">لماذا مركز ريادة؟</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-4 mb-4">معايير الراحة والأمان الطبي المتكامل</h2>
                <p class="text-slate-500 text-sm sm:text-base leading-relaxed">نحرص على أن تكون زيارتك للعيادة تجربة مريحة وسلسة تمنحك الثقة والاطمئنان في كل مرحلة.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="relative p-8 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)] transition-all duration-300 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.1)] hover:-translate-y-1.5 group text-right">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg font-bold mb-5 group-hover:bg-blue-700 group-hover:text-white transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2 text-base">دقة المواعيد التامة</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">نظامنا الرقمي يمنع تضارب المواعيد لنضمن بدء جلستك في موعدها المحدد دون إهدار لوقتك.</p>
                </div>

                <div class="relative p-8 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)] transition-all duration-300 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.1)] hover:-translate-y-1.5 group text-right">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg font-bold mb-5 group-hover:bg-[#87A96B] group-hover:text-white transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2 text-base">راحة وعناية فائقة</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">بيئة علاجية هادئة واستقبال دافئ يعين المرضى من كافة الأعمار على الشعور بالراحة التامة.</p>
                </div>

                <div class="relative p-8 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)] transition-all duration-300 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.1)] hover:-translate-y-1.5 group text-right">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg font-bold mb-5 group-hover:bg-blue-700 group-hover:text-white transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2 text-base">خامات علاجية معتمدة</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">نعتمد على أجود خامات طب وتجميل الأسنان المتوافقة حيوياً لتحقيق متانة تدوم طويلاً.</p>
                </div>

                <div class="relative p-8 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.05)] transition-all duration-300 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.1)] hover:-translate-y-1.5 group text-right">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg font-bold mb-5 group-hover:bg-[#87A96B] group-hover:text-white transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2 text-base">كادر طبي مؤهل</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">أطباء واستشاريون متمرسون يحرصون على الاستماع لاحتياجاتك وشرح خطة العلاج بوضوح.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dentists Section -->
    <section id="dentists" class="py-24 bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-4 py-1.5 rounded-full border border-[#87A96B]/30">فريق الأطباء الاستشاريين</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-4 mb-4">أطباء متميزون لخدمتك وعائلتك</h2>
                <p class="text-slate-500 text-sm sm:text-base leading-relaxed">خبرات سريرية ممتدة في تشخيص وعلاج مشاكل الأسنان وتجميل الابتسامة بأعلى كفاءة.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 {{ count($dentists ?? []) >= 3 ? 'lg:grid-cols-3' : 'max-w-4xl mx-auto' }} gap-8">
                @forelse($dentists ?? [] as $dentist)
                    <div class="relative p-8 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)] transition-all duration-300 hover:shadow-[0_20px_40px_-10px_rgba(30,64,175,0.12)] hover:-translate-y-1.5 group text-center flex flex-col justify-between">
                        <div>
                            <!-- Avatar -->
                            <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-blue-700 to-blue-600 text-white font-black text-2xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-700/20 border-4 border-blue-50">
                                {{ mb_substr($dentist->name, 0, 1) }}
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-1">{{ $dentist->name }}</h3>
                            <p class="text-xs text-[#87A96B] font-bold mb-4">طبيب واستشاري معتمد في طب الأسنان</p>
                            
                            <div class="flex items-center justify-center space-x-1 space-x-reverse text-amber-400 text-xs mb-6">
                                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                <span class="text-slate-400 text-[11px] mr-1">(تقييم 5.0 ممتاز)</span>
                            </div>
                        </div>

                        <a href="{{ auth()->check() ? route('patient.appointments.create') : route('login') }}" class="inline-flex items-center justify-center w-full py-3 rounded-full text-xs font-bold text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-md shadow-blue-700/20">
                            حجز موعد مع الطبيب
                        </a>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-10 text-slate-400 text-xs">
                        جاري تحديث بيانات طاقم الأطباء...
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Booking Steps -->
    <section id="steps" class="py-24 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-4 py-1.5 rounded-full border border-[#87A96B]/30">السهولة والسرعة</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-4 mb-4">خطوات الحجز في 3 مراحل بسيطة</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center relative">
                <div class="relative p-8 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)]">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-700 to-blue-600 text-white font-black text-base flex items-center justify-center mx-auto mb-6 shadow-md shadow-blue-700/20">01</div>
                    <h3 class="text-base font-black text-slate-900 mb-2">اختيار الخدمة والطبيب</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">حدد الإجراء المطلوب مثل الفحص العام، التنظيف، أو التقويم مع اختيار طبيبك المفضل.</p>
                </div>

                <div class="relative p-8 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)]">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-700 to-blue-600 text-white font-black text-base flex items-center justify-center mx-auto mb-6 shadow-md shadow-blue-700/20">02</div>
                    <h3 class="text-base font-black text-slate-900 mb-2">تحديد الوقت المناسب</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">اختر التاريخ والوقت المتاح بدقة وسلاسة بما يتوافق مع جدولك اليومي.</p>
                </div>

                <div class="relative p-8 overflow-hidden bg-white border border-slate-100 rounded-3xl shadow-[0_10px_30px_-5px_rgba(30,64,175,0.06)]">
                    <div class="w-12 h-12 rounded-full bg-[#87A96B] text-white font-black text-base flex items-center justify-center mx-auto mb-6 shadow-md shadow-[#87A96B]/20">03</div>
                    <h3 class="text-base font-black text-slate-900 mb-2">تأكيد فوري واستقبال مريح</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">تأكيد مباشر للموعد في نظام العيادة لتبدأ جلستك الطبية في موعدها دون انتظار.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Master CTA Banner (Comfortable Medical Blue Gradient) -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-r from-blue-800 via-blue-700 to-indigo-900 p-10 sm:p-16 text-center text-white shadow-2xl shadow-blue-900/20 border border-blue-600/30">
                <!-- Background Glows -->
                <div class="absolute -top-24 -right-24 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-[#87A96B]/20 rounded-full blur-3xl"></div>

                <div class="relative z-10 max-w-2xl mx-auto">
                    <span class="text-xs font-black uppercase tracking-widest bg-white/15 px-4 py-1 rounded-full text-blue-100 mb-4 inline-block">صحة أسنانك تبدأ بخطوة</span>
                    <h2 class="text-3xl sm:text-5xl font-black mb-6 leading-tight">
                        جاهز لتجربة رعاية سنية مريحة ومتطورة؟
                    </h2>
                    <p class="text-blue-100 text-sm sm:text-base mb-8 leading-relaxed font-normal">
                        انضم إلى آلاف المرضى الذين وجدوا الراحة والاطمئنان في عيادتنا. احجز موعدك الآن عبر نظامنا الذكي بكل يسر.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        @if(($settings['maintenance_mode'] ?? '0') == '1')
                            <span class="inline-flex items-center px-8 py-3.5 text-base font-bold text-white bg-blue-900/60 border border-white/20 rounded-full">
                                نظام الحجز الإلكتروني في وضع الصيانة حالياً
                            </span>
                        @else
                            @auth
                                <a href="{{ route('patient.appointments.create') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-blue-900 transition-all duration-300 bg-white rounded-full shadow-lg hover:bg-blue-50 hover:-translate-y-1">
                                    احجز موعدك الطبي الآن
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-blue-900 transition-all duration-300 bg-white rounded-full shadow-lg hover:bg-blue-50 hover:-translate-y-1">
                                    فتح حساب مريض جديد
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-white transition-all duration-300 bg-blue-900/60 border border-white/20 rounded-full shadow-lg hover:bg-blue-900/90 hover:-translate-y-1">
                                    تسجيل الدخول للمرضى
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Medical Clinic Footer -->
    <footer class="bg-[#1E3A8A] text-white pt-16 pb-10 border-t border-blue-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Contact Info & Details -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 pb-8 border-b border-blue-800/80">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-12 h-12 rounded-2xl bg-white text-blue-700 flex items-center justify-center font-black text-xl shadow-lg">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-black text-white">{{ $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان' }}</span>
                        <span class="text-xs text-[#87A96B] font-bold block">رعاية متخصصة، تقنيات متقدمة، وابتسامة مفعمة بالثقة</span>
                    </div>
                </div>
                <div class="text-xs text-blue-200 text-center md:text-left space-y-1">
                    <div>{{ $settings['clinic_address'] ?? 'الرياض - طريق الملك فهد' }} | هاتف: {{ $settings['clinic_phone'] ?? '0501234567' }}</div>
                    <div>البريد الإلكتروني: {{ $settings['clinic_email'] ?? 'info@riyadaclinic.com' }}</div>
                </div>
            </div>

            <!-- Social Media Links (Dynamic from Settings) -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-6 border-b border-blue-800/60 text-xs text-blue-200">
                <span class="font-medium">تابعنا عبر شبكات التواصل الاجتماعي لمركزنا:</span>
                <div class="flex items-center gap-3">
                    @if(!empty($settings['social_twitter']))
                        <a href="{{ $settings['social_twitter'] }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-blue-800/60 hover:bg-blue-600 text-blue-200 hover:text-white flex items-center justify-center transition" title="X (Twitter)">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    @endif
                    @if(!empty($settings['social_instagram']))
                        <a href="{{ $settings['social_instagram'] }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-blue-800/60 hover:bg-pink-600 text-blue-200 hover:text-white flex items-center justify-center transition" title="Instagram">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    @endif
                    @if(!empty($settings['social_facebook']))
                        <a href="{{ $settings['social_facebook'] }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-blue-800/60 hover:bg-blue-600 text-blue-200 hover:text-white flex items-center justify-center transition" title="Facebook">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    @endif
                    @if(!empty($settings['social_whatsapp']))
                        <a href="{{ $settings['social_whatsapp'] }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-blue-800/60 hover:bg-emerald-600 text-blue-200 hover:text-white flex items-center justify-center transition" title="WhatsApp">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        </a>
                    @endif
                </div>
            </div>
            <div class="pt-8 text-center text-xs text-blue-300">
                جميع الحقوق محفوظة &copy; {{ date('Y') }} - {{ $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان' }}.
            </div>
        </div>
    </footer>

</body>
</html>
