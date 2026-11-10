<footer class="bg-[#1E3A8A] text-white pt-16 pb-10 border-t border-blue-900 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- 4 Columns Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-blue-800/80">
            
            <!-- Col 1: About Clinic & Brand -->
            <div class="space-y-4">
                <div class="flex items-center space-x-3 space-x-reverse">
                    @if(!empty($settings['clinic_logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['clinic_logo']))
                        <img src="{{ asset('storage/' . $settings['clinic_logo']) }}" 
                             alt="{{ $settings['clinic_name'] ?? 'مركز ريادة' }}" 
                             class="w-12 h-12 rounded-2xl object-contain bg-white p-1 shadow-md">
                    @else
                        <div class="w-12 h-12 rounded-2xl bg-white text-blue-700 flex items-center justify-center font-black text-xl shadow-lg">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    @endif
                    <div>
                        <span class="text-lg font-black text-white block">{{ $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان' }}</span>
                        <span class="text-[11px] text-[#87A96B] font-extrabold uppercase tracking-wider block">RIYADA DENTAL CARE</span>
                    </div>
                </div>

                <p class="text-xs text-blue-200 leading-relaxed">
                    مركز رائد ومتخصص في تقديم أرقى علاجات وجراحات وتجميل الأسنان، مجهز بأحدث أجهزة التصوير ثلاثي الأبعاد وتقنيات التعقيم العالمية لتجربة علاجية بلا قلق.
                </p>

                <div class="pt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-800/80 text-blue-200 border border-blue-700">
                        <span class="w-2 h-2 rounded-full bg-[#87A96B]"></span>
                        ترخيص طبي معتمد
                    </span>
                </div>
            </div>

            <!-- Col 2: Quick Links (Multi-page navigation) -->
            <div>
                <h4 class="text-sm font-black text-white mb-5 pb-2 border-b border-blue-800/80 inline-block">
                    صفحات الموقع
                </h4>
                <ul class="space-y-2.5 text-xs font-semibold text-blue-200">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-white hover:underline transition flex items-center gap-1.5">
                            <span>›</span> الرئيسية
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="hover:text-white hover:underline transition flex items-center gap-1.5">
                            <span>›</span> من نحن ورؤيتنا
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services.index') }}" class="hover:text-white hover:underline transition flex items-center gap-1.5">
                            <span>›</span> الخدمات الطبية والتجميلية
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('doctors.index') }}" class="hover:text-white hover:underline transition flex items-center gap-1.5">
                            <span>›</span> فريق الأطباء والاستشاريين
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('appointments.book') }}" class="hover:text-white hover:underline transition flex items-center gap-1.5">
                            <span>›</span> حجز موعد إلكتروني
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="hover:text-white hover:underline transition flex items-center gap-1.5">
                            <span>›</span> تواصل معنا ومعلومات المركز
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Col 3: Working Hours -->
            <div>
                <h4 class="text-sm font-black text-white mb-5 pb-2 border-b border-blue-800/80 inline-block">
                    أوقات العمل والدوام
                </h4>
                <div class="space-y-3 text-xs text-blue-200">
                    <p class="leading-relaxed">
                        نستقبلكم طيلة أيام الأسبوع وفق الجدول المعتمد لمواعيد الاستشارات والعمليات:
                    </p>
                    <div class="p-3.5 rounded-2xl bg-blue-900/60 border border-blue-800/80 space-y-2">
                        <div class="flex items-center justify-between font-bold text-white">
                            <span>السبت - الخميس:</span>
                            <span class="text-[#87A96B]">09:00 ص - 09:00 م</span>
                        </div>
                        <div class="flex items-center justify-between font-bold text-slate-300 border-t border-blue-800/60 pt-1.5">
                            <span>الجمعة:</span>
                            <span class="text-rose-300">عطلة رسمية (طوارئ فقط)</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-blue-300">
                        * يرجى الحجز المسبق لضمان عدم الانتظار وبدء جلستك في موعدها الدقيق.
                    </p>
                </div>
            </div>

            <!-- Col 4: Contact Info -->
            <div>
                <h4 class="text-sm font-black text-white mb-5 pb-2 border-b border-blue-800/80 inline-block">
                    بيانات التواصل المباشر
                </h4>
                <ul class="space-y-3 text-xs text-blue-200">
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-[#87A96B] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ $settings['clinic_address'] ?? 'فلسطين - غزة - مفترق السرايا - برج الإتقان' }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#87A96B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <a href="tel:{{ $settings['clinic_phone'] ?? '0596482371' }}" class="hover:text-white font-bold transition">
                            {{ $settings['clinic_phone'] ?? '0596482371' }}
                        </a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#87A96B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:{{ $settings['clinic_email'] ?? 'infoclini@riyadaclinic.com' }}" class="hover:text-white transition">
                            {{ $settings['clinic_email'] ?? 'infoclini@riyadaclinic.com' }}
                        </a>
                    </li>
                </ul>

                @if(!empty($settings['social_whatsapp']))
                    <div class="mt-4">
                        <a href="{{ $settings['social_whatsapp'] }}" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            مراسلتنا عبر واتساب
                        </a>
                    </div>
                @endif
            </div>

        </div>

        <!-- Social Media Row & Legal -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-6 border-b border-blue-800/60 text-xs text-blue-200">
            <span class="font-medium">تابع مركز ريادة لطب الأسنان عبر قنوات التواصل:</span>
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
            </div>
        </div>

        <!-- Copyright -->
        <div class="pt-6 text-center text-xs text-blue-300">
            جميع الحقوق محفوظة &copy; {{ date('Y') }} - {{ $settings['clinic_name'] ?? 'مركز ريادة لطب وجراحة الأسنان' }}. نظام Multi-Page متكامل.
        </div>

    </div>
</footer>
