<!-- ======================================================== -->
<!-- مكون تأكيد الحذف العام (Global Reusable Delete Modal)     -->
<!-- ======================================================== -->
<div x-data="{
        showModal: false,
        deleteUrl: '',
        modalTitle: 'تأكيد الحذف',
        modalMessage: 'هل أنت متأكد من رغبتك في حذف هذا العنصر؟ لا يمكن التراجع عن هذا الإجراء بعد تنفيذه.',
        open(url, title, message) {
            this.deleteUrl = url;
            if (title) this.modalTitle = title;
            if (message) this.modalMessage = message;
            this.showModal = true;
        },
        close() {
            this.showModal = false;
        }
     }"
     @open-delete-modal.window="open($event.detail.url, $event.detail.title, $event.detail.message)"
     x-init="
        window.confirmDelete = (url, title, message) => {
            window.dispatchEvent(new CustomEvent('open-delete-modal', {
                detail: { url, title, message }
            }));
        };
     "
     x-show="showModal"
     @keydown.escape.window="close()"
     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     style="display: none;"
     dir="rtl">

    <!-- خلفية الضغط للإغلاق (Backdrop click) -->
    <div @click="close()" class="fixed inset-0"></div>

    <!-- صندوق المودال (Modal Box) -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
         class="relative w-full max-w-md mx-4 bg-white rounded-3xl shadow-2xl p-6 sm:p-8 text-center z-10 border border-slate-100">

        <!-- الأيقونة التحذيرية في المنتصف -->
        <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-5 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>

        <!-- عنوان الرسالة التفاعلي -->
        <h3 class="text-lg font-bold text-slate-900 mb-2" x-text="modalTitle">تأكيد عملية الحذف</h3>

        <!-- نص الرسالة التوضيحي -->
        <p class="text-xs text-slate-500 leading-relaxed mb-6 px-2" x-text="modalMessage">
            هل أنت متأكد من رغبتك في حذف هذا السجل نهائياً من قاعدة البيانات؟ لن تتمكن من استعادته مجدداً.
        </p>

        <!-- نموذج الحذف الفعلي (Form) والأزرار -->
        <form :action="deleteUrl" method="POST" class="flex items-center justify-center space-x-3 space-x-reverse">
            @csrf
            @method('DELETE')

            <!-- زر الإلغاء -->
            <button type="button" 
                    @click="close()" 
                    class="px-6 py-2.5 rounded-full text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition">
                إلغاء التراجع
            </button>

            <!-- زر تأكيد الحذف النهائي -->
            <button type="submit" 
                    class="px-6 py-2.5 rounded-full text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 transition hover:-translate-y-0.5">
                نعم، احذف الآن
            </button>
        </form>

    </div>

</div>
