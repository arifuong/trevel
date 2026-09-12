<div x-data="{
        toasts: [],
        add(toast) {
            const id = Date.now() + Math.random().toString(36).substring(2, 5);
            const newToast = {
                id: id,
                type: toast.type || 'info',
                message: toast.message,
                duration: toast.duration || 4000,
                timer: null,
                visible: true
            };

            this.toasts.push(newToast);

            if (newToast.duration > 0) {
                newToast.timer = setTimeout(() => {
                    this.remove(id);
                }, newToast.duration);
            }
        },
        remove(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        },
        init() {
            // Check session flash messages rendered at load time
            @if(session('success'))
                this.add({ type: 'success', message: @js(session('success')), duration: 4000 });
            @endif

            @if(session('error'))
                this.add({ type: 'error', message: @js(session('error')), duration: 5000 });
            @endif

            @if(session('info'))
                this.add({ type: 'info', message: @js(session('info')), duration: 4000 });
            @endif

            @if(session('warning'))
                this.add({ type: 'warning', message: @js(session('warning')), duration: 4500 });
            @endif
        }
    }"
    x-on:toast.window="add($event.detail)"
    class="fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0"
    role="region"
    aria-live="polite"
    aria-label="Notifikasi Sistem"
    x-cloak>

    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4 scale-95"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-250"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95 translate-x-4"
             class="pointer-events-auto w-full bg-white rounded-2xl shadow-xl border overflow-hidden p-4 flex items-start gap-3.5 transition-all"
             :class="{
                'border-emerald-200 shadow-emerald-950/10': toast.type === 'success',
                'border-red-200 shadow-red-950/10': toast.type === 'error',
                'border-amber-200 shadow-amber-950/10': toast.type === 'warning',
                'border-[#CCD8C7] shadow-black/10': toast.type === 'info'
             }">

            {{-- Icon by Type --}}
            <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center"
                 :class="{
                    'bg-emerald-100 text-emerald-700': toast.type === 'success',
                    'bg-red-100 text-red-700': toast.type === 'error',
                    'bg-amber-100 text-amber-700': toast.type === 'warning',
                    'bg-[#EFF3EB] text-[#1B3B2B]': toast.type === 'info'
                 }">

                {{-- Success Icon --}}
                <template x-if="toast.type === 'success'">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </template>

                {{-- Error Icon --}}
                <template x-if="toast.type === 'error'">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </template>

                {{-- Warning Icon --}}
                <template x-if="toast.type === 'warning'">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.007v.008H12v-.008z"/>
                    </svg>
                </template>

                {{-- Info Icon --}}
                <template x-if="toast.type === 'info'">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                </template>
            </div>

            {{-- Message Body --}}
            <div class="flex-1 min-w-0 pt-0.5">
                <p class="text-xs font-bold capitalize leading-tight mb-0.5"
                   :class="{
                        'text-emerald-900': toast.type === 'success',
                        'text-red-900': toast.type === 'error',
                        'text-amber-900': toast.type === 'warning',
                        'text-[#12271E]': toast.type === 'info'
                   }"
                   x-text="toast.type === 'success' ? 'Berhasil' : (toast.type === 'error' ? 'Pemberitahuan / Kendala' : (toast.type === 'warning' ? 'Peringatan' : 'Informasi'))">
                </p>
                <p class="text-xs text-[#526057] leading-relaxed break-words font-medium" x-text="toast.message"></p>
            </div>

            {{-- Close Button --}}
            <button type="button" 
                    @click="remove(toast.id)"
                    class="p-1 rounded-lg text-[#526057]/60 hover:text-[#12271E] hover:bg-[#EFF3EB] transition-colors cursor-pointer shrink-0"
                    aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
