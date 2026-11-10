<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', ($settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان') . ' | الرعاية الطبية المتقدمة والابتسامة المشرقة')</title>

    <meta name="description" content="@yield('meta_description', 'مركز ريادة لطب وجراحة وتجميل الأسنان - بيئة علاجية مريحة ومجهزة بأحدث التقنيات الرقمية المتقدمة ونظام حجز إلكتروني مؤكد بالدقيقة.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <!-- Open Graph / Meta -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان')">
    <meta property="og:description" content="@yield('meta_description', 'مركز ريادة لطب وجراحة وتجميل الأسنان - أحدث التقنيات الرقمية المريحة للأسرة')">
    @if(!empty($settings['clinic_logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['clinic_logo']))
        <meta property="og:image" content="{{ asset('storage/' . $settings['clinic_logo']) }}">
    @endif

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
                        primary: '#1E40AF',
                        'primary-dark': '#1E3A8A',
                        'primary-light': '#3B82F6',
                        accent: '#87A96B',
                        'accent-dark': '#557143',
                        'accent-light': '#B6CFAC',
                        surface: '#FFFFFF',
                        background: '#F8FAFC',
                    },
                    boxShadow: {
                        'soft-shadow': '0 10px 30px -5px rgba(30, 64, 175, 0.08)',
                        'soft-shadow-hover': '0 20px 40px -10px rgba(30, 64, 175, 0.12)',
                        'blue-glow': '0 10px 25px -3px rgba(30, 64, 175, 0.25)',
                        'sage-glow': '0 10px 25px -3px rgba(135, 169, 107, 0.25)',
                    }
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
    @stack('styles')
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased selection:bg-[#1E40AF] selection:text-white flex flex-col min-h-screen overflow-x-hidden">

    <!-- Top Announcement Bar -->
    <div class="bg-blue-900 text-blue-100 text-xs py-2 px-4 border-b border-blue-800/80">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center space-x-3 space-x-reverse text-center sm:text-right">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#87A96B] text-white shadow-xs">عيادات معتمدة</span>
                <span class="font-medium">{{ $settings['announcement_bar'] ?? 'مركز ريادة لطب وجراحة وتجميل الأسنان: أحدث التقنيات الرقمية المريحة للأسرة' }}</span>
            </div>
            <div class="flex items-center space-x-4 space-x-reverse text-[11px] text-blue-200">
                <span>ساعات العمل: {{ $workingHoursText ?? 'السبت - الخميس (09:00 ص - 09:00 م)' }}</span>
                <span>•</span>
                <a href="tel:{{ $settings['clinic_phone'] ?? '0596482371' }}" class="text-white font-bold flex items-center gap-1 hover:text-[#87A96B] transition">
                    <svg class="w-3.5 h-3.5 text-[#87A96B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $settings['clinic_phone'] ?? '0596482371' }}
                </a>
            </div>
        </div>
    </div>

    <!-- Unified Navbar Component -->
    @include('layouts.partials.public-navbar')

    <!-- Flash Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold flex items-center justify-between mb-4 shadow-xs" role="alert">
                <div class="flex items-center space-x-2 space-x-reverse">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold flex items-center justify-between mb-4 shadow-xs" role="alert">
                <div class="flex items-center space-x-2 space-x-reverse">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">✕</button>
            </div>
        @endif

        @if (session('warning'))
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-bold flex items-center justify-between mb-4 shadow-xs" role="alert">
                <div class="flex items-center space-x-2 space-x-reverse">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('warning') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700">✕</button>
            </div>
        @endif

        @if (session('info'))
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-sm font-bold flex items-center justify-between mb-4 shadow-xs" role="alert">
                <div class="flex items-center space-x-2 space-x-reverse">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700">✕</button>
            </div>
        @endif
    </div>

    <!-- Page Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Unified Footer Component -->
    @include('layouts.partials.public-footer')

    @stack('scripts')
</body>
</html>
