@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-200 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 rounded-2xl shadow-xs transition-all duration-200 text-xs font-bold text-slate-800']) }}>