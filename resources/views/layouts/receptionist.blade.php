<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'لوحة تحكم موظف الاستقبال') | مركز ريادة لطب وجراحة الأسنان</title>

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
                        secondary: '#87A96B',
                        surface: '#F8FAFC',
                    },
                    boxShadow: {
                        'soft-blue': '0 10px 30px -5px rgba(30, 64, 175, 0.06)',
                        'card-hover': '0 20px 40px -10px rgba(30, 64, 175, 0.12)',
                    }
                }
            }
        };

        function receptionistNotificationsMenu(initialItems) {
            const items = (Array.isArray(initialItems) ? initialItems : []).map(function(n) {
                return Object.assign({}, n, {
                    read: Boolean(n.read)
                });
            });

            return {
                notificationsOpen: false,
                notifications: items,
                get unreadCount() {
                    return this.notifications.filter(function(n) { return !n.read; }).length;
                },
                markAllAsRead() {
                    this.notifications.forEach(function(n) { n.read = true; });
                    try {
                        fetch('{{ route("receptionist.notifications.mark-all-read") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        }).catch(function() {});
                    } catch (e) {}

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'تم تحديد كافة الإشعارات كمقروءة وحفظها في قاعدة البيانات ✓', type: 'info' }
                    }));
                },
                markItemAsRead(id) {
                    var item = this.notifications.find(function(n) { return n.id === id; });
                    if (item) {
                        item.read = true;
                        try {
                            fetch('/receptionist/notifications/' + id + '/read', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            }).catch(function() {});
                        } catch (e) {}
                    }
                }
            };
        }
    </script>

    <style>
        body {
            font-family: 'Tajawal', 'Cairo', sans-serif;
        }
        /* Custom scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F8FAFC;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .custom-sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.35);
            border-radius: 9999px;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(135, 169, 107, 0.7);
        }
        .custom-sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(59, 130, 246, 0.35) transparent;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased selection:bg-[#1E40AF] selection:text-white min-h-screen"
      x-data="{
          sidebarOpen: false,
          sidebarCollapsed: JSON.parse(localStorage.getItem('sidebarCollapsed') || localStorage.getItem('sidebarCollapsedReceptionist') || 'false'),
          activeTooltip: null,
          tooltipTop: 0,
          showTooltip(el, text) {
              if (!this.sidebarCollapsed) return;
              const rect = el.getBoundingClientRect();
              this.tooltipTop = rect.top + (rect.height / 2);
              this.activeTooltip = text;
          },
          hideTooltip() {
              this.activeTooltip = null;
          }
      }"
      x-effect="localStorage.setItem('sidebarCollapsed', JSON.stringify(sidebarCollapsed))">

    <!-- Floating Sidebar Tooltip (Visible ONLY on Desktop when sidebar is Collapsed) -->
    <div x-show="sidebarCollapsed && activeTooltip"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-x-2"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-2"
         :style="'top: ' + tooltipTop + 'px'"
         class="fixed right-[86px] -translate-y-1/2 z-[100] pointer-events-none hidden lg:block"
         style="display: none;">
        <div class="relative bg-[#172554] text-white text-xs font-bold px-3 py-1.5 rounded-xl shadow-2xl shadow-blue-950/70 border border-blue-500/40 whitespace-nowrap">
            <span x-text="activeTooltip"></span>
            <span class="absolute top-1/2 -right-1 -translate-y-1/2 w-2 h-2 bg-[#172554] rotate-45 border-r border-t border-blue-500/40"></span>
        </div>
    </div>

    <div class="min-h-screen flex">

        <!-- ======================================================== -->
        <!-- 1. القائمة الجانبية (Sidebar: Deep Medical Blue #172554)    -->
        <!-- ======================================================== -->
        
        <!-- Mobile Overlay / Backdrop -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden" 
             style="display: none;"></div>

        <!-- Sidebar Container -->
        <aside :class="{
                   'translate-x-0': sidebarOpen,
                   'translate-x-full lg:translate-x-0': !sidebarOpen,
                   'lg:w-20': sidebarCollapsed,
                   'lg:w-72': !sidebarCollapsed
               }"
               class="fixed inset-y-0 right-0 z-50 w-72 bg-[#172554] text-white flex flex-col h-screen py-5 transition-all duration-300 ease-in-out shadow-2xl lg:shadow-none border-l border-blue-950"
               :class="sidebarCollapsed ? 'lg:px-2' : 'lg:px-3 px-3'">

            <!-- الشعار وزر الطي المكتبي -->
            <div class="flex-shrink-0 flex items-center pb-4 border-b border-blue-900/60 transition-all duration-300"
                 :class="sidebarCollapsed ? 'flex-col gap-3 justify-center px-1' : 'justify-between px-2'">
                
                <!-- الشعار والأيقونة -->
                <div class="flex items-center overflow-hidden transition-all duration-300"
                     :class="sidebarCollapsed ? 'justify-center w-full' : 'space-x-3 space-x-reverse'">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#1E40AF] to-blue-500 border border-blue-400/40 flex items-center justify-center text-white shadow-lg shadow-blue-900/40 flex-shrink-0"
                         :title="sidebarCollapsed ? 'مركز ريادة لطب وجراحة الأسنان' : ''">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M18.8 4.5c-1.3-1.4-3.2-1.9-5-1.4-.6.2-1.2.5-1.8.8-.6-.3-1.2-.6-1.8-.8-1.8-.5-3.7 0-5 1.4-1.8 1.9-2.2 4.9-1 7.2.9 1.8 2.2 3.8 3.3 6.1.7 1.4 1.4 2.9 2.2 4.4.4.7 1.1 1.2 1.9 1.2s1.5-.5 1.9-1.2c.8-1.5 1.5-3 2.2-4.4 1.1-2.3 2.4-4.3 3.3-6.1 1.1-2.3.8-5.3-1-7.2zm-4.3 14.8c-.7 1.3-1.3 2.7-2 3.9-.2.3-.4.3-.5 0-.7-1.2-1.3-2.6-2-3.9-1.1-2.2-2.3-4.1-3.2-5.8-.9-1.8-.7-4.2.7-5.7 1-1 2.4-1.4 3.7-1 .8.2 1.6.7 2.2 1.4.3.4.8.6 1.3.6s1-.2 1.3-.6c.6-.7 1.4-1.2 2.2-1.4 1.3-.4 2.7 0 3.7 1 1.4 1.5 1.6 3.9.7 5.7-.9 1.7-2.1 3.6-3.2 5.8z"/>
                        </svg>
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition class="overflow-hidden whitespace-nowrap">
                        <span class="text-base font-black text-white tracking-wide block truncate">مركز ريادة</span>
                        <span class="text-[10px] text-[#87A96B] font-bold tracking-wider block -mt-0.5">قسم الاستقبال والحجوزات</span>
                    </div>
                </div>

                <!-- زر طي/توسيع القائمة في شاشات الكمبيوتر -->
                <button @click="sidebarCollapsed = !sidebarCollapsed" 
                        class="hidden lg:flex p-1.5 rounded-xl text-blue-300 hover:text-white hover:bg-blue-800/50 transition focus:outline-none" 
                        :class="sidebarCollapsed ? 'mx-auto' : ''"
                        :title="sidebarCollapsed ? 'توسيع القائمة' : 'طي القائمة'">
                    <svg class="w-5 h-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- زر إغلاق القائمة في الشاشات الصغيرة -->
                <button @click="sidebarOpen = false" 
                        class="lg:hidden p-2 rounded-xl text-blue-300 hover:text-white hover:bg-blue-800/60 transition focus:outline-none"
                        title="إغلاق القائمة">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- حاوية التمرير لعناصر القائمة -->
            <div class="flex-1 overflow-y-auto overflow-x-hidden my-3 py-1 custom-sidebar-scroll"
                 :class="sidebarCollapsed ? 'px-0' : 'px-1'">
                
                <nav class="space-y-1.5">
                    
                    <!-- 1. لوحة التحكم -->
                    <a href="{{ route('receptionist.dashboard') }}" 
                       @mouseenter="showTooltip($el, 'لوحة التحكم')"
                       @mouseleave="hideTooltip()"
                       @focus="showTooltip($el, 'لوحة التحكم')"
                       @blur="hideTooltip()"
                       class="flex items-center rounded-2xl font-bold transition duration-200 {{ request()->routeIs('receptionist.dashboard') ? 'bg-blue-800/60 text-white border-r-4 border-[#87A96B] shadow-inner' : 'text-blue-200 hover:bg-blue-800/40 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3 text-center' : 'space-x-3.5 space-x-reverse px-3.5 py-3 text-xs'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('receptionist.dashboard') ? 'text-[#87A96B]' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">لوحة التحكم</span>
                    </a>

                    <!-- 2. إدارة المرضى -->
                    <a href="{{ route('receptionist.patients.index') }}" 
                       @mouseenter="showTooltip($el, 'إدارة المرضى')"
                       @mouseleave="hideTooltip()"
                       @focus="showTooltip($el, 'إدارة المرضى')"
                       @blur="hideTooltip()"
                       class="flex items-center rounded-2xl font-bold transition duration-200 {{ request()->routeIs('receptionist.patients.*') ? 'bg-blue-800/60 text-white border-r-4 border-[#87A96B] shadow-inner' : 'text-blue-200 hover:bg-blue-800/40 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3 text-center' : 'space-x-3.5 space-x-reverse px-3.5 py-3 text-xs'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('receptionist.patients.*') ? 'text-[#87A96B]' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">إدارة المرضى</span>
                    </a>

                    <!-- 3. الحجوزات والمواعيد -->
                    <a href="{{ route('receptionist.appointments.index') }}" 
                       @mouseenter="showTooltip($el, 'الحجوزات والمواعيد')"
                       @mouseleave="hideTooltip()"
                       @focus="showTooltip($el, 'الحجوزات والمواعيد')"
                       @blur="hideTooltip()"
                       class="flex items-center rounded-2xl font-bold transition duration-200 {{ request()->routeIs('receptionist.appointments.index') || request()->routeIs('receptionist.appointments.edit') ? 'bg-blue-800/60 text-white border-r-4 border-[#87A96B] shadow-inner' : 'text-blue-200 hover:bg-blue-800/40 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3 text-center' : 'space-x-3.5 space-x-reverse px-3.5 py-3 text-xs'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('receptionist.appointments.index') || request()->routeIs('receptionist.appointments.edit') ? 'text-[#87A96B]' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">الحجوزات والمواعيد</span>
                    </a>

                    <!-- 4. حجز موعد جديد -->
                    <a href="{{ route('receptionist.appointments.create') }}" 
                       @mouseenter="showTooltip($el, 'حجز موعد جديد')"
                       @mouseleave="hideTooltip()"
                       @focus="showTooltip($el, 'حجز موعد جديد')"
                       @blur="hideTooltip()"
                       class="flex items-center rounded-2xl font-bold transition duration-200 {{ request()->routeIs('receptionist.appointments.create') ? 'bg-blue-800/60 text-white border-r-4 border-[#87A96B] shadow-inner' : 'text-blue-200 hover:bg-blue-800/40 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3 text-center' : 'space-x-3.5 space-x-reverse px-3.5 py-3 text-xs'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('receptionist.appointments.create') ? 'text-[#87A96B]' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">حجز موعد جديد</span>
                    </a>

                    <!-- 5. جدول مواعيد الأطباء -->
                    <a href="{{ route('receptionist.appointments.calendar') }}" 
                       @mouseenter="showTooltip($el, 'جدول مواعيد الأطباء')"
                       @mouseleave="hideTooltip()"
                       @focus="showTooltip($el, 'جدول مواعيد الأطباء')"
                       @blur="hideTooltip()"
                       class="flex items-center rounded-2xl font-bold transition duration-200 {{ request()->routeIs('receptionist.appointments.calendar') ? 'bg-blue-800/60 text-white border-r-4 border-[#87A96B] shadow-inner' : 'text-blue-200 hover:bg-blue-800/40 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3 text-center' : 'space-x-3.5 space-x-reverse px-3.5 py-3 text-xs'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('receptionist.appointments.calendar') ? 'text-[#87A96B]' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">جدول مواعيد الأطباء</span>
                    </a>

                    <!-- 6. الإشعارات والتنبيهات -->
                    <a href="{{ route('receptionist.notifications.index') }}" 
                       @mouseenter="showTooltip($el, 'الإشعارات والتنبيهات')"
                       @mouseleave="hideTooltip()"
                       @focus="showTooltip($el, 'الإشعارات والتنبيهات')"
                       @blur="hideTooltip()"
                       class="flex items-center rounded-2xl font-bold transition duration-200 {{ request()->routeIs('receptionist.notifications.*') ? 'bg-blue-800/60 text-white border-r-4 border-[#87A96B] shadow-inner' : 'text-blue-200 hover:bg-blue-800/40 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3 text-center' : 'space-x-3.5 space-x-reverse px-3.5 py-3 text-xs'">
                        <div class="relative flex-shrink-0">
                            <svg class="w-5 h-5 {{ request()->routeIs('receptionist.notifications.*') ? 'text-[#87A96B]' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if(($unreadNotificationsCount ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-[#87A96B] rounded-full ring-2 ring-[#172554]"></span>
                            @endif
                        </div>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap flex-1">الإشعارات</span>
                        @if(($unreadNotificationsCount ?? 0) > 0)
                            <span x-show="!sidebarCollapsed" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#87A96B] text-white">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </a>

                    <!-- 7. الملف الشخصي -->
                    <a href="{{ route('receptionist.profile.edit') }}" 
                       @mouseenter="showTooltip($el, 'الملف الشخصي')"
                       @mouseleave="hideTooltip()"
                       @focus="showTooltip($el, 'الملف الشخصي')"
                       @blur="hideTooltip()"
                       class="flex items-center rounded-2xl font-bold transition duration-200 {{ request()->routeIs('receptionist.profile.*') ? 'bg-blue-800/60 text-white border-r-4 border-[#87A96B] shadow-inner' : 'text-blue-200 hover:bg-blue-800/40 hover:text-white' }}"
                       :class="sidebarCollapsed ? 'justify-center p-3 text-center' : 'space-x-3.5 space-x-reverse px-3.5 py-3 text-xs'">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('receptionist.profile.*') ? 'text-[#87A96B]' : 'text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">الملف الشخصي</span>
                    </a>

                </nav>
            </div>

            <!-- أسفل القائمة: الانتقال للموقع وتسجيل الخروج -->
            <div class="flex-shrink-0 pt-3 border-t border-blue-900/60 space-y-1">
                
                <!-- الانتقال إلى واجهة العيادة -->
                <a href="{{ route('home') }}" 
                   target="_blank"
                   @mouseenter="showTooltip($el, 'واجهة العيادة العامة')"
                   @mouseleave="hideTooltip()"
                   @focus="showTooltip($el, 'واجهة العيادة العامة')"
                   @blur="hideTooltip()"
                   class="flex items-center rounded-2xl text-blue-200 hover:text-white hover:bg-blue-800/40 font-bold transition duration-200"
                   :class="sidebarCollapsed ? 'justify-center p-3' : 'space-x-3.5 space-x-reverse px-3.5 py-2.5 text-xs'">
                    <svg class="w-5 h-5 text-blue-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">واجهة العيادة</span>
                </a>

                <!-- تسجيل الخروج -->
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" 
                            @mouseenter="showTooltip($el, 'تسجيل الخروج')"
                            @mouseleave="hideTooltip()"
                            @focus="showTooltip($el, 'تسجيل الخروج')"
                            @blur="hideTooltip()"
                            class="w-full flex items-center rounded-2xl text-rose-300 hover:text-white hover:bg-rose-600/30 font-bold transition duration-200 cursor-pointer"
                            :class="sidebarCollapsed ? 'justify-center p-3' : 'space-x-3.5 space-x-reverse px-3.5 py-2.5 text-xs'">
                        <svg class="w-5 h-5 text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">تسجيل الخروج</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ======================================================== -->
        <!-- المحتوى المركزي (Topbar + Page Content Yield)            -->
        <!-- ======================================================== -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 mr-0 lg:mr-72"
             :class="{ 'lg:!mr-20': sidebarCollapsed, 'lg:!mr-72': !sidebarCollapsed }">

            <!-- الشريط العلوي (Topbar) -->
            <header class="bg-white/90 backdrop-blur-md h-20 px-6 sm:px-8 border-b border-slate-100 shadow-[0_4px_20px_rgba(30,64,175,0.03)] sticky top-0 z-30 flex items-center justify-between gap-4">

                <!-- زر فتح القائمة للشاشات الصغيرة -->
                <div class="flex items-center space-x-3 space-x-reverse lg:hidden">
                    <button @click="sidebarOpen = true" 
                            class="p-2.5 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-[#1E40AF] transition border border-slate-200/80 focus:outline-none focus:ring-2 focus:ring-[#1E40AF]/20"
                            title="فتح القائمة الرئيسية">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <span class="font-black text-slate-900 text-sm">مركز ريادة</span>
                </div>

                <!-- مربع البحث المباشر للريسبشن -->
                <div class="flex-1 max-w-lg mx-auto hidden md:block"
                     x-data="{
                         query: '',
                         results: { patients: [], appointments: [], services: [] },
                         open: false,
                         loading: false,
                         searchTimer: null,
                         get hasResults() {
                             return this.results.patients.length > 0 ||
                                    this.results.appointments.length > 0 ||
                                    this.results.services.length > 0;
                         },
                         doSearch() {
                             clearTimeout(this.searchTimer);
                             if (this.query.trim().length < 2) {
                                 this.results = { patients: [], appointments: [], services: [] };
                                 this.open = false;
                                 return;
                             }
                             this.loading = true;
                             this.searchTimer = setTimeout(() => {
                                 fetch('{{ route('receptionist.search') }}?q=' + encodeURIComponent(this.query), {
                                     headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                                 })
                                 .then(r => r.json())
                                 .then(data => {
                                     this.results = data;
                                     this.open = true;
                                 })
                                 .catch(() => {})
                                 .finally(() => { this.loading = false; });
                             }, 300);
                         }
                     }"
                     @click.outside="open = false">
                    <div class="relative">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <svg x-show="loading" class="w-4 h-4 animate-spin text-[#1E40AF]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </span>
                        <input type="text"
                               x-model="query"
                               @input="doSearch()"
                               @focus="if(hasResults) open = true"
                               placeholder="ابحث عن مريض، موعد، خدمة..."
                               class="w-full pr-11 pl-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-full focus:bg-white focus:border-[#1E40AF] focus:ring-2 focus:ring-[#1E40AF]/15 outline-none transition placeholder:text-slate-400 text-slate-800">

                        <!-- نتائج البحث -->
                        <div x-show="open && hasResults"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute top-full right-0 mt-2 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden max-h-80 overflow-y-auto"
                             style="display: none;">

                            <!-- المرضى -->
                            <template x-if="results.patients.length > 0">
                                <div>
                                    <div class="px-4 py-2 bg-slate-50 border-b border-slate-100">
                                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">المرضى</span>
                                    </div>
                                    <template x-for="p in results.patients" :key="p.id">
                                        <a :href="'{{ url('receptionist/patients') }}/' + p.id"
                                           class="flex items-center gap-3 px-4 py-2.5 hover:bg-blue-50 transition group">
                                            <div class="w-7 h-7 rounded-full bg-[#87A96B]/15 text-[#557143] flex items-center justify-center text-xs font-black flex-shrink-0" x-text="p.name.charAt(0)"></div>
                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-slate-900 truncate" x-text="p.name"></div>
                                                <div class="text-[10px] text-slate-400 truncate" x-text="p.phone || p.email"></div>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>

                            <!-- الحجوزات والمواعيد -->
                            <template x-if="results.appointments.length > 0">
                                <div>
                                    <div class="px-4 py-2 bg-slate-50 border-b border-slate-100">
                                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">الحجوزات</span>
                                    </div>
                                    <template x-for="apt in results.appointments" :key="apt.id">
                                        <a :href="'{{ url('receptionist/appointments') }}/' + apt.id + '/edit'"
                                           class="flex items-center justify-between gap-3 px-4 py-2.5 hover:bg-blue-50 transition group">
                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-slate-900 truncate">
                                                    موعد رقم #<span x-text="apt.id"></span> - <span x-text="apt.patient ? apt.patient.name : 'مريض'"></span>
                                                </div>
                                                <div class="text-[10px] text-slate-400 truncate" x-text="apt.appointment_date + ' | د. ' + (apt.dentist ? apt.dentist.name : '')"></div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                  :class="apt.status === 'confirmed' ? 'bg-blue-100 text-blue-700' : (apt.status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700')"
                                                  x-text="apt.status"></span>
                                        </a>
                                    </template>
                                </div>
                            </template>

                            <!-- الخدمات -->
                            <template x-if="results.services.length > 0">
                                <div>
                                    <div class="px-4 py-2 bg-slate-50 border-b border-slate-100">
                                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">الخدمات</span>
                                    </div>
                                    <template x-for="s in results.services" :key="s.id">
                                        <div class="flex items-center justify-between gap-3 px-4 py-2.5">
                                            <div class="text-xs font-bold text-slate-900 truncate" x-text="s.name"></div>
                                            <div class="text-[10px] font-bold text-[#87A96B]" x-text="s.price ? s.price + ' ر.س' : ''"></div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- الجانب الأيسر: الإشعارات وملف موظف الاستقبال -->
                <div class="flex items-center space-x-4 space-x-reverse">

                    @php
                        $formattedNotifications = ($adminNotifications ?? collect())->map(function($a) {
                            if (is_array($a)) {
                                return [
                                    'id'    => (string) ($a['id'] ?? ''),
                                    'title' => $a['title'] ?? 'إشعار الاستقبال',
                                    'type'  => $a['type'] ?? 'appointment',
                                    'text'  => $a['message'] ?? ($a['text'] ?? ''),
                                    'time'  => $a['date'] ?? ($a['time'] ?? ''),
                                    'link'  => $a['link'] ?? route('receptionist.appointments.index'),
                                    'read'  => (bool) ($a['read'] ?? false),
                                ];
                            }
                            return [
                                'id'    => (string) $a->id,
                                'title' => $a->title ?? 'إشعار الاستقبال',
                                'type'  => $a->type ?? 'appointment',
                                'text'  => $a->message ?? ($a->text ?? ''),
                                'time'  => $a->date ?? '',
                                'link'  => $a->link ?? route('receptionist.appointments.index'),
                                'read'  => (bool) ($a->read ?? false),
                            ];
                        })->values();
                    @endphp

                    <!-- قائمة الإشعارات المنسدلة -->
                    <div class="relative"
                         x-data="receptionistNotificationsMenu(@js($formattedNotifications))"
                         @click.outside="notificationsOpen = false">

                        <button @click="notificationsOpen = !notificationsOpen"
                                class="relative p-2.5 rounded-2xl bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-[#1E40AF] transition border border-slate-200/80 focus:outline-none focus:ring-2 focus:ring-[#1E40AF]/20"
                                :class="notificationsOpen ? 'bg-blue-50 text-[#1E40AF] border-blue-200 ring-2 ring-[#1E40AF]/15' : ''"
                                title="الإشعارات والتنبيهات">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span x-show="unreadCount > 0" class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#87A96B] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#87A96B] ring-2 ring-white"></span>
                            </span>
                        </button>

                        <div x-show="notificationsOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute left-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden"
                             style="display: none;">

                            <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center space-x-2.5 space-x-reverse">
                                    <h3 class="text-xs font-black text-slate-900">إشعارات الاستقبال</h3>
                                    <span x-show="unreadCount > 0"
                                          class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#87A96B]/15 text-[#557143] border border-[#87A96B]/30"
                                          x-text="unreadCount + ' جديدة'"></span>
                                    <span x-show="unreadCount === 0"
                                          class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">الكل مقروء ✓</span>
                                </div>
                                <button x-show="unreadCount > 0"
                                        @click="markAllAsRead()"
                                        class="text-[11px] font-bold text-blue-700 hover:text-blue-900 hover:underline cursor-pointer transition">
                                    تحديد الكل كمقروء
                                </button>
                            </div>

                            <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="py-10 text-center">
                                        <svg class="w-10 h-10 mx-auto text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <p class="text-xs text-slate-400 font-semibold">لا توجد إشعارات جديدة</p>
                                    </div>
                                </template>

                                <template x-for="notif in notifications" :key="notif.id">
                                    <a :href="notif.link"
                                       @click="markItemAsRead(notif.id)"
                                       class="flex items-start gap-3.5 p-4 transition duration-150 group relative"
                                       :class="notif.read ? 'bg-white opacity-70 hover:opacity-100 hover:bg-slate-50' : 'bg-blue-50/40 hover:bg-blue-50/70'">
                                        <div x-show="!notif.read"
                                             class="absolute top-4 left-3 w-2 h-2 rounded-full"
                                             :class="notif.type === 'cancel' ? 'bg-rose-500' : 'bg-[#87A96B]'"></div>

                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition"
                                             :class="{
                                                 'bg-[#87A96B]/15 text-[#557143]': notif.type === 'appointment',
                                                 'bg-blue-50 text-[#1E40AF]': notif.type === 'success',
                                                 'bg-rose-50 text-rose-600': notif.type === 'cancel'
                                             }">
                                            <template x-if="notif.type === 'appointment'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </template>
                                            <template x-if="notif.type === 'success'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </template>
                                            <template x-if="notif.type === 'cancel'">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                            </template>
                                        </div>

                                        <div class="flex-1 min-w-0 pr-0.5">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-black truncate"
                                                      :class="notif.type === 'cancel' ? 'text-rose-700' : 'text-slate-900'"
                                                      x-text="notif.title"></span>
                                                <span class="text-[10px] text-slate-400 pl-3 flex-shrink-0" x-text="notif.time"></span>
                                            </div>
                                            <p class="text-[11px] text-slate-600 leading-relaxed" x-text="notif.text"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>

                            <div class="p-3 bg-slate-50/80 border-t border-slate-100 text-center">
                                <a href="{{ route('receptionist.notifications.index') }}"
                                   class="inline-flex items-center justify-center text-xs font-bold text-[#1E40AF] hover:text-blue-800 transition">
                                    مركز الإشعارات الشامل ←
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>

                    <!-- ملف المستخدم الحالي -->
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#1E40AF] to-[#87A96B] border-2 border-white flex items-center justify-center text-white font-black text-sm shadow-md overflow-hidden">
                                <span>{{ mb_substr(Auth::user()->name ?? 'م', 0, 1) }}</span>
                            </div>
                            <span class="absolute bottom-0 left-0 w-2.5 h-2.5 bg-[#87A96B] rounded-full ring-2 ring-white"></span>
                        </div>

                        <div class="text-right hidden sm:block">
                            <span class="block text-xs font-bold text-slate-900 leading-tight">
                                {{ Auth::user()->name ?? 'موظف الاستقبال' }}
                            </span>
                            <span class="block text-[11px] text-[#87A96B] font-bold">
                                (موظف استقبال)
                            </span>
                        </div>
                    </div>

                </div>

            </header>

            <!-- 3. منطقة المحتوى (Main Content Area: bg-[#F8FAFC]) -->
            <main class="flex-1 bg-[#F8FAFC] p-6 sm:p-8">
                @yield('content')
            </main>

        </div>

    </div>

    <!-- نظام الإشعارات المنبثقة التفاعلي (Toast System) -->
    <div x-data="{
            show: false,
            message: '',
            type: 'success',
            timer: null,
            openToast(msg, toastType = 'success') {
                this.message = msg;
                this.type = toastType;
                this.show = true;
                clearTimeout(this.timer);
                this.timer = setTimeout(() => {
                    this.show = false;
                }, 4000);
            }
         }"
         x-init="
            @if(session('success'))
                $nextTick(() => openToast(@js(session('success')), 'success'));
            @elseif(session('info'))
                $nextTick(() => openToast(@js(session('info')), 'info'));
            @elseif(session('error'))
                $nextTick(() => openToast(@js(session('error')), 'error'));
            @elseif(session('warning'))
                $nextTick(() => openToast(@js(session('warning')), 'error'));
            @endif

            window.addEventListener('toast', e => {
                openToast(e.detail.message, e.detail.type || 'success');
            });
         "
         class="fixed bottom-5 left-5 z-50 pointer-events-none"
         dir="rtl">

        <div x-show="show"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-4 opacity-0 scale-95"
             x-transition:enter-end="translate-y-0 opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="pointer-events-auto flex items-center gap-3.5 px-5 py-4 rounded-2xl shadow-[0_10px_30px_-5px_rgba(0,0,0,0.15)] text-white max-w-md border border-white/10"
             :class="{
                 'bg-[#87A96B]': type === 'success',
                 'bg-[#172554] border-blue-400/40': type === 'info',
                 'bg-rose-600': type === 'error'
             }"
             style="display: none;">

            <template x-if="type === 'success'">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </template>

            <template x-if="type === 'info'">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
            </template>

            <template x-if="type === 'error'">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </template>

            <div class="flex-1 text-xs font-bold leading-relaxed" x-text="message"></div>

            <button @click="show = false" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

</body>
</html>
