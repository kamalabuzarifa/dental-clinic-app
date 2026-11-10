<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'مركز ريادة لطب وجراحة الأسنان') }}</title>

        <!-- Google Fonts: Tajawal -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">

        <!-- Tailwind CSS & Alpine.js -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Tajawal', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                blue: {
                                    50: '#EFF6FF',
                                    100: '#DBEAFE',
                                    600: '#2563EB',
                                    700: '#1E40AF',
                                    800: '#1E3A8A',
                                },
                                sage: {
                                    50: '#F4F7F2',
                                    500: '#87A96B',
                                    600: '#6F8F56',
                                    700: '#567042',
                                },
                            },
                            primary: '#1E40AF',
                            accent: '#87A96B',
                            surface: '#FFFFFF',
                        },
                        boxShadow: {
                            'soft-shadow': '0 10px 30px -5px rgba(30, 64, 175, 0.08)',
                        }
                    }
                }
            }
        </script>

        <style>
            body { font-family: 'Tajawal', sans-serif; }
        </style>
    </head>
    <body class="font-sans text-slate-800 antialiased bg-[#F8FAFC] selection:bg-[#1E40AF] selection:text-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 px-4">
            <div class="mb-6">
                <a href="/" class="flex flex-col items-center space-y-3 group">
                    <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-blue-700 to-blue-600 flex items-center justify-center text-white shadow-xl shadow-blue-700/25 group-hover:scale-105 transition duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-center">
                        <span class="text-2xl font-black text-slate-900 block group-hover:text-blue-700 transition">مركز ريادة لطب الأسنان</span>
                        <span class="text-xs text-[#87A96B] font-extrabold tracking-widest uppercase block -mt-0.5">RIYADA DENTAL CARE</span>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white border border-slate-100 shadow-[0_10px_30px_-5px_rgba(30,64,175,0.08)] rounded-3xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>