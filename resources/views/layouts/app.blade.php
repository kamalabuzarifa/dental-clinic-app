<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'مركز ريادة لطب وتجميل الأسنان') }}</title>

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
                            sans: ['Tajawal', 'sans-serif'],
                            cairo: ['Cairo', 'sans-serif'],
                        },
                        colors: {
                            'brand-blue': {
                                DEFAULT: '#1E40AF',
                                50: '#eff6ff',
                                100: '#dbeafe',
                                200: '#bfdbfe',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                800: '#1e40af',
                                900: '#1e3a8a',
                            },
                            'brand-sage': {
                                DEFAULT: '#87A96B',
                                50: '#f4f8f3',
                                100: '#e5eee3',
                                200: '#cdddca',
                                500: '#87A96B',
                                600: '#739556',
                                700: '#5c7844',
                            },
                            primary: {
                                DEFAULT: '#1E40AF',
                                50: '#eff6ff',
                                100: '#dbeafe',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                800: '#1e40af',
                                900: '#1e3a8a',
                            },
                            accent: {
                                DEFAULT: '#87A96B',
                                light: '#F4F8F3',
                                dark: '#5C7844',
                                50: '#f4f8f3',
                                100: '#e5eee3',
                                500: '#87A96B',
                                600: '#739556',
                            },
                            surface: '#FFFFFF',
                            sage: {
                                DEFAULT: '#87A96B',
                                50: '#f4f8f3',
                                100: '#e5eee3',
                                500: '#87A96B',
                                600: '#739556',
                            },
                        },
                        boxShadow: {
                            'soft-shadow': '0 10px 30px -5px rgba(30, 64, 175, 0.08), 0 4px 12px -2px rgba(0, 0, 0, 0.03)',
                            'soft-shadow-hover': '0 20px 40px -8px rgba(30, 64, 175, 0.14), 0 8px 20px -4px rgba(0, 0, 0, 0.05)',
                            'luxury': '0 10px 30px -5px rgba(30, 64, 175, 0.08), 0 4px 12px -2px rgba(0, 0, 0, 0.03)',
                            'luxury-hover': '0 20px 40px -8px rgba(30, 64, 175, 0.14), 0 8px 20px -4px rgba(0, 0, 0, 0.05)',
                            'blue-glow': '0 10px 25px -3px rgba(30, 64, 175, 0.25)',
                            'sage-glow': '0 10px 25px -3px rgba(135, 169, 107, 0.3)',
                        },
                        keyframes: {
                            'fade-in-up': {
                                '0%': { opacity: '0', transform: 'translateY(24px)' },
                                '100%': { opacity: '1', transform: 'translateY(0)' },
                            },
                            'float': {
                                '0%, 100%': { transform: 'translateY(0px)' },
                                '50%': { transform: 'translateY(-10px)' },
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
            body {
                font-family: 'Tajawal', sans-serif;
            }
            .glass-panel {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
            }
            .blue-gradient-text {
                background: linear-gradient(135deg, #1E40AF 0%, #2563EB 50%, #87A96B 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .medical-gradient {
                background: linear-gradient(135deg, #1E40AF 0%, #2563EB 60%, #3B82F6 100%);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#F8FAFC] text-slate-800 selection:bg-[#1E40AF] selection:text-white min-h-screen flex flex-col">
        <div class="flex-1 flex flex-col">
            @include('layouts.navigation')

            <!-- Global Notifications -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-6">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 flex items-center justify-between p-4 bg-white border border-[#86A789]/30 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-9 h-9 rounded-full bg-[#86A789]/15 text-[#86A789] flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">تمت العملية بنجاح</h4>
                                <p class="text-xs text-slate-600 mt-0.5">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="text-slate-400 hover:text-slate-600 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 flex items-center justify-between p-4 bg-white border border-rose-200 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-9 h-9 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">تنبيه النظام</h4>
                                <p class="text-xs text-slate-600 mt-0.5">{{ session('error') }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="text-slate-400 hover:text-slate-600 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show" class="mb-4 p-5 bg-white border border-rose-200 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                        <div class="flex items-center space-x-2 space-x-reverse mb-2 font-bold text-xs text-rose-700">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>يرجى مراجعة وتصحيح المدخلات التالية:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs text-slate-600 space-y-1 pr-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Header (if exists) -->
            @isset($header)
                <header class="bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-16 z-30 transition-all">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Medical Modern Footer -->
            <footer class="bg-[#1E3A8A] text-white border-t border-blue-800/60 mt-20 pt-12 pb-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-blue-800/40">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-10 h-10 rounded-2xl bg-white text-[#1E40AF] flex items-center justify-center font-black shadow-lg shadow-blue-950/20">
                                ✦
                            </div>
                            <div>
                                <span class="text-base font-black text-white tracking-wide block">مركز ريادة لطب وتجميل الأسنان</span>
                                <span class="text-[11px] text-[#87A96B] font-bold block">رعاية تخصصية بمعايير عالمية</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-8 space-x-reverse text-xs text-blue-100/80 font-medium">
                            <a href="{{ route('home') }}" class="hover:text-white transition">الرئيسية</a>
                            <a href="{{ route('home') }}#services" class="hover:text-white transition">خدماتنا</a>
                            <a href="{{ route('home') }}#dentists" class="hover:text-white transition">كادر الأطباء</a>
                            <span class="text-blue-400">|</span>
                            <span class="text-white font-bold">هاتف الاستفسار والمواعيد: 0501234567</span>
                        </div>
                    </div>

                    <div class="pt-6 text-center text-[11px] text-blue-200/70 flex flex-col sm:flex-row items-center justify-between gap-2">
                        <p>جميع الحقوق محفوظة &copy; {{ date('Y') }} مركز ريادة لطب وتجميل الأسنان.</p>
                        <p class="text-blue-200/60">نظام حجز عيادات الأسنان المريح والذكي</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
