<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-black text-slate-900">إنشاء حساب مريض جديد</h2>
        <p class="text-xs text-slate-500 mt-1">سجل حسابك في مركز ريادة لطب وجراحة الأسنان</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">الاسم الكامل:</label>
            <input id="name" class="w-full px-5 py-3 transition-all duration-300 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="مثال: تركي محمد" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">البريد الإلكتروني:</label>
            <input id="email" class="w-full px-5 py-3 transition-all duration-300 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">كلمة المرور:</label>
            <input id="password" class="w-full px-5 py-3 transition-all duration-300 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">تأكيد كلمة المرور:</label>
            <input id="password_confirmation" class="w-full px-5 py-3 transition-all duration-300 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 outline-none text-xs font-bold text-slate-800" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-3">
            <button type="submit" class="inline-flex items-center justify-center w-full px-8 py-3.5 text-sm font-bold text-white transition-all duration-300 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full shadow-lg shadow-blue-700/20 hover:from-blue-700 hover:to-blue-800 hover:-translate-y-0.5">
                تأكيد وإنشاء الحساب
            </button>
        </div>

        <div class="text-center pt-5 border-t border-slate-100 text-xs text-slate-500">
            لديك حساب بالفعل؟ 
            <a href="{{ route('login') }}" class="font-bold text-blue-700 hover:text-blue-800">
                تسجيل الدخول
            </a>
        </div>
    </form>
</x-guest-layout>