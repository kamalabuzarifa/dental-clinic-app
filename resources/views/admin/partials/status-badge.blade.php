{{--
    Status Badge Partial
    Usage: @include('admin.partials.status-badge', ['status' => $apt->status])
    Supported: pending | confirmed | completed | cancelled | no_show
--}}
@switch($status ?? '')
    @case('confirmed')
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-[#87A96B] text-white shadow-sm">
            <span class="w-1.5 h-1.5 rounded-full bg-white/70"></span> مؤكد
        </span>
        @break
    @case('pending')
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 border border-amber-200">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> انتظار
        </span>
        @break
    @case('completed')
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-[#87A96B]/15 text-[#557143]">
            <span class="w-1.5 h-1.5 rounded-full bg-[#87A96B]"></span> مكتمل
        </span>
        @break
    @case('cancelled')
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 border border-rose-200">
            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> ملغي
        </span>
        @break
    @case('no_show')
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-200">
            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> غياب
        </span>
        @break
    @default
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-600">
            {{ $status ?? '-' }}
        </span>
@endswitch
