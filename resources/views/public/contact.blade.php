@extends('layouts.public')

@section('title', $seo['title'] ?? ('تواصل معنا ومعلومات المركز | ' . ($settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان')))
@section('meta_description', $seo['description'] ?? 'تواصل مع مركز ريادة لطب وجراحة الأسنان، العنوان في غزة، أرقام الهاتف، البريد الإلكتروني، وساعات العمل مع إمكانية إرسال رسالتك أو استفسارك المباشر.')
@section('meta_keywords', $seo['keywords'] ?? 'تواصل مع العيادة, عنوان عيادة الأسنان, رقم هاتف المركز')
@section('canonical', route('contact'))

@section('content')

    <!-- Breadcrumb & Header -->
    <section class="bg-gradient-to-b from-blue-50/50 to-white border-b border-slate-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 space-x-reverse text-xs font-semibold text-slate-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-blue-700 transition">الرئيسية</a>
                <span>/</span>
                <span class="text-blue-700 font-bold">تواصل معنا</span>
            </nav>

            <div class="max-w-3xl">
                <span class="inline-block text-xs font-black uppercase tracking-widest text-[#557143] bg-[#87A96B]/15 px-3.5 py-1 rounded-full border border-[#87A96B]/30 mb-3">
                    خدمة المرضى والاستفسارات
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 leading-tight mb-4">
                    نحن هنا للإجابة على استفساراتكم وخدمتكم بكل سرور
                </h1>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                    يسعد فريق الاستقبال وخدمة المرضى في مركز ريادة باستقبال اتصالاتكم ورسائلكم والإجابة على كافة التساؤلات المتعلقة بالخدمات الطبية والتجميلية وأوقات الدوام.
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Info Cards & Message Form -->
    <section class="py-16 bg-[#F8FAFC]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- 4 Quick Contact Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-14">
                
                <!-- Address -->
                <div class="p-7 bg-white rounded-3xl border border-slate-100 shadow-xs text-right">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-xs font-bold text-slate-400 mb-1">مقر المركز والعيادات</h3>
                    <p class="text-sm font-black text-slate-900 leading-relaxed">
                        {{ $settings['clinic_address'] ?? 'فلسطين - غزة - مفترق السرايا - برج الإتقان' }}
                    </p>
                </div>

                <!-- Phone -->
                <div class="p-7 bg-white rounded-3xl border border-slate-100 shadow-xs text-right">
                    <div class="w-12 h-12 rounded-2xl bg-[#87A96B]/20 text-[#557143] flex items-center justify-center text-lg font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <h3 class="text-xs font-bold text-slate-400 mb-1">الهاتف المباشر</h3>
                    <a href="tel:{{ $settings['clinic_phone'] ?? '0596482371' }}" class="text-sm font-black text-blue-700 hover:underline block">
                        {{ $settings['clinic_phone'] ?? '0596482371' }}
                    </a>
                </div>

                <!-- Email -->
                <div class="p-7 bg-white rounded-3xl border border-slate-100 shadow-xs text-right">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xs font-bold text-slate-400 mb-1">البريد الإلكتروني</h3>
                    <a href="mailto:{{ $settings['clinic_email'] ?? 'infoclini@riyadaclinic.com' }}" class="text-xs font-black text-slate-900 hover:text-blue-700 block truncate">
                        {{ $settings['clinic_email'] ?? 'infoclini@riyadaclinic.com' }}
                    </a>
                </div>

                <!-- Working Hours -->
                <div class="p-7 bg-white rounded-3xl border border-slate-100 shadow-xs text-right">
                    <div class="w-12 h-12 rounded-2xl bg-[#87A96B]/20 text-[#557143] flex items-center justify-center text-lg font-bold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xs font-bold text-slate-400 mb-1">أوقات العمل</h3>
                    <p class="text-xs font-black text-slate-900 leading-relaxed">
                        {{ $workingHoursText ?? 'السبت - الخميس (09:00 ص - 09:00 م)' }}
                    </p>
                </div>

            </div>

            <!-- Form & Location Map Section (2 Columns) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                
                <!-- Contact Form (Col 7) -->
                <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-100 shadow-md p-8 sm:p-10">
                    <h2 class="text-xl font-black text-slate-900 mb-2">أرسل لنا رسالة أو استفساراً</h2>
                    <p class="text-xs text-slate-500 mb-8 leading-relaxed">
                        يرجى ملء النموذج أدناه وسيقوم فريق الاستقبال الطبي بالتواصل معك في أقرب وقت عبر الهاتف أو البريد.
                    </p>

                    <form method="POST" action="{{ route('contact.submit') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-xs font-bold text-slate-800 mb-2">
                                    الاسم الكامل: <span class="text-blue-700">*</span>
                                </label>
                                <input type="text" name="name" id="name" required value="{{ old('name', auth()->user()?->name) }}" 
                                       placeholder="أدخل اسمك الكريم"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 text-xs font-medium text-slate-800 outline-none transition">
                                @error('name')
                                    <p class="text-rose-600 text-[11px] mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-xs font-bold text-slate-800 mb-2">
                                    رقم الهاتف / الجوال: <span class="text-blue-700">*</span>
                                </label>
                                <input type="text" name="phone" id="phone" required value="{{ old('phone', auth()->user()?->phone) }}" 
                                       placeholder="مثال: 059XXXXXXX"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 text-xs font-medium text-slate-800 outline-none transition">
                                @error('phone')
                                    <p class="text-rose-600 text-[11px] mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-800 mb-2">
                                    البريد الإلكتروني (اختياري):
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email', auth()->user()?->email) }}" 
                                       placeholder="name@example.com"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 text-xs font-medium text-slate-800 outline-none transition">
                                @error('email')
                                    <p class="text-rose-600 text-[11px] mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block text-xs font-bold text-slate-800 mb-2">
                                    موضوع الرسالة: <span class="text-blue-700">*</span>
                                </label>
                                <select name="subject" id="subject" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 text-xs font-bold text-slate-800 outline-none transition">
                                    <option value="استشارة طبية عامة" {{ old('subject') == 'استشارة طبية عامة' ? 'selected' : '' }}>استشارة طبية عامة</option>
                                    <option value="استفسار عن أسعار وخدمات المركز" {{ old('subject') == 'استفسار عن أسعار وخدمات المركز' ? 'selected' : '' }}>استفسار عن أسعار وخدمات المركز</option>
                                    <option value="تنسيق موعد أو عملية جراحية" {{ old('subject') == 'تنسيق موعد أو عملية جراحية' ? 'selected' : '' }}>تنسيق موعد أو عملية جراحية</option>
                                    <option value="اقتراح أو ملاحظة" {{ old('subject') == 'اقتراح أو ملاحظة' ? 'selected' : '' }}>اقتراح أو ملاحظة</option>
                                    <option value="أخرى" {{ old('subject') == 'أخرى' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('subject')
                                    <p class="text-rose-600 text-[11px] mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-xs font-bold text-slate-800 mb-2">
                                نص الرسالة: <span class="text-blue-700">*</span>
                            </label>
                            <textarea name="message" id="message" rows="4" required placeholder="اكتب استفسارك أو رسالتك بالتفصيل هنا..." 
                                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 text-xs font-medium text-slate-800 outline-none transition">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-rose-600 text-[11px] mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center px-8 py-3.5 rounded-full text-xs font-black text-white bg-blue-700 hover:bg-blue-800 shadow-md transition">
                            إرسال الرسالة الآن ✓
                        </button>
                    </form>
                </div>

                <!-- Location & Direct Map Details (Col 5) -->
                <div class="lg:col-span-5 space-y-6">
                    
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-md p-8 text-right space-y-6">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                                📍
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">موقع العيادة وسهولة الوصول</h3>
                                <p class="text-[11px] text-slate-400">موقع مركزي يسهل الوصول إليه</p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            تقع عيادات مركز ريادة في أرقى أبراج الخدمات الطبية في مدينة غزة، مع توافر مواقف سيارات ومصاعد حديثة مجهزة لاستقبال كافة الفئات العمرية وذوي الاحتياجات الخاصة.
                        </p>

                        <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100 space-y-2 text-xs">
                            <div class="font-bold text-blue-900">العنوان بالتفصيل:</div>
                            <div class="text-blue-800">{{ $settings['clinic_address'] ?? 'فلسطين - غزة - مفترق السرايا - برج الإتقان' }}</div>
                        </div>

                        @if(!empty($settings['clinic_map_iframe']))
                            <div class="rounded-2xl overflow-hidden h-44 border border-slate-200">
                                <iframe src="{{ $settings['clinic_map_iframe'] }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        @endif

                        <div class="pt-2">
                            @if(!empty($settings['social_whatsapp']))
                                <a href="{{ $settings['social_whatsapp'] }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 py-3.5 rounded-full text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md transition">
                                    تواصل مباشر عبر واتساب
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 bg-gradient-to-r from-blue-900 to-blue-800 text-white rounded-3xl shadow-sm text-center">
                        <h4 class="text-sm font-black mb-1">ترغب بحجز موعد مباشر وفوري؟</h4>
                        <p class="text-xs text-blue-200 mb-4">استخدم نظام الحجز الإلكتروني لتأكيد موعدك بالدقيقة دون الحاجة للانتظار.</p>
                        <a href="{{ route('appointments.book') }}" class="inline-block px-6 py-2.5 rounded-full text-xs font-black text-blue-900 bg-white hover:bg-blue-50 transition shadow-xs">
                            حجز موعد إلكتروني ←
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </section>

@endsection
