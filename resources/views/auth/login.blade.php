<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-black text-slate-900">تسجيل الدخول إلى حسابك</h2>
        <p class="text-xs text-slate-500 mt-1">أهلاً بك مجدداً في مركز ريادة لطب وجراحة الأسنان</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">البريد الإلكتروني:</label>
            <input id="email" class="w-full px-5 py-3 transition-all duration-300 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold text-slate-700">كلمة المرور:</label>
                @if (Route::has('password.request'))
                    <a class="text-[11px] font-bold text-blue-700 hover:text-blue-800" href="{{ route('password.request') }}">
                        نسيت كلمة المرور؟
                    </a>
                @endif
            </div>
            <input id="password" class="w-full px-5 py-3 transition-all duration-300 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-700 shadow-xs focus:ring-blue-600" name="remember">
                <span class="mr-2 text-xs font-semibold text-slate-600">تذكر تسجيل دخولي</span>
            </label>
        </div>

        <div class="pt-3">
            <button type="submit" class="inline-flex items-center justify-center w-full px-8 py-3.5 text-sm font-bold text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full shadow-lg shadow-blue-700/20 hover:from-blue-700 hover:to-blue-800 hover:-translate-y-0.5">
                تسجيل الدخول
            </button>
        </div>

        <div class="text-center pt-5 border-t border-slate-100 text-xs text-slate-500">
            ليس لديك حساب بعد؟ 
            <a href="{{ route('register') }}" class="font-bold text-blue-700 hover:text-blue-800">
                إنشاء حساب مريض جديد
            </a>
        </div>
    </form>
</x-guest-layout>