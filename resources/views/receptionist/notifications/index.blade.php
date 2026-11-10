@extends('layouts.receptionist')

@section('title', 'مركز الإشعارات والتنبيهات')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- الترويسة -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgba(30,64,175,0.03)]">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#87A96B]"></span>
                <span class="text-xs font-black text-[#87A96B] uppercase tracking-wider">سجل التنبيهات</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">مركز الإشعارات والتنبيهات</h1>
            <p class="text-xs text-slate-500 mt-1">متابعة إشعارات الحجوزات، تحديث الحالات، والتنبيهات التشغيلية لمكتب الاستقبال.</p>
        </div>

        @if($unreadCount > 0)
            <form method="POST" action="{{ route('receptionist.notifications.mark-all-read') }}">
                @csrf
                <button type="submit" 
                        class="px-5 py-2.5 rounded-2xl text-xs font-bold bg-blue-50 text-[#1E40AF] hover:bg-blue-100 transition border border-blue-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>تحديد الكل كمقروء</span>
                </button>
            </form>
        @endif
    </div>

    <!-- شريط التبويب / الفلترة -->
    <div class="flex items-center gap-3">
        <a href="{{ route('receptionist.notifications.index', ['filter' => 'all']) }}" 
           class="px-4 py-2 rounded-2xl text-xs font-bold transition {{ $filter === 'all' ? 'bg-[#1E40AF] text-white shadow-md shadow-blue-900/20' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            جميع الإشعارات ({{ $totalCount }})
        </a>

        <a href="{{ route('receptionist.notifications.index', ['filter' => 'unread']) }}" 
           class="px-4 py-2 rounded-2xl text-xs font-bold transition {{ $filter === 'unread' ? 'bg-[#87A96B] text-white shadow-md shadow-[#87A96B]/20' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
            غير المقروءة ({{ $unreadCount }})
        </a>
    </div>

    <!-- قائمة الإشعارات -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-[0_4px_25px_rgba(30,64,175,0.03)] overflow-hidden divide-y divide-slate-100">
        @if($notifications->isEmpty())
            <div class="py-16 text-center">
                <div class="w-16 h-16 rounded-3xl bg-slate-50 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-700">لا توجد إشعارات لعرضها حالياً</p>
                <p class="text-xs text-slate-400 mt-1">ستصلك هنا تنبيهات فورية عند إضافة أو تعديل أي موعد بالعيادة.</p>
            </div>
        @else
            @foreach($notifications as $notif)
                @php
                    $isUnread = is_null($notif->read_at);
                    $data = $notif->data;
                    $title = $data['title'] ?? 'إشعار إداري';
                    $message = $data['message'] ?? '';
                    $link = $data['link'] ?? route('receptionist.appointments.index');
                    $type = $data['icon'] ?? 'appointment';
                @endphp
                <div class="p-5 flex items-start justify-between gap-4 transition duration-150 {{ $isUnread ? 'bg-blue-50/30' : 'hover:bg-slate-50/60' }}">
                    <div class="flex items-start gap-4 min-w-0">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 {{ $isUnread ? 'bg-blue-100 text-[#1E40AF]' : 'bg-slate-100 text-slate-500' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs font-black text-slate-900">{{ $title }}</h3>
                                @if($isUnread)
                                    <span class="w-2 h-2 rounded-full bg-[#87A96B]"></span>
                                @endif
                                <span class="text-[10px] text-slate-400">{{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $message }}</p>
                            
                            <div class="mt-2 flex items-center gap-3">
                                <a href="{{ $link }}" class="text-[11px] font-bold text-[#1E40AF] hover:underline">
                                    معاينة الموعد المرتبط ←
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($isUnread)
                            <button type="button" 
                                    onclick="markRead('{{ $notif->id }}', this)" 
                                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-[11px] font-bold transition">
                                تحديد كمقروء
                            </button>
                        @else
                            <span class="text-[11px] font-bold text-slate-400">مقروء ✓</span>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- الترقيم -->
            <div class="p-4 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</div>

<script>
function markRead(id, btn) {
    fetch('/receptionist/notifications/' + id + '/read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    }).then(r => r.json()).then(data => {
        if (data.success) {
            btn.outerHTML = '<span class="text-[11px] font-bold text-slate-400">مقروء ✓</span>';
        }
    }).catch(e => {});
}
</script>
@endsection
