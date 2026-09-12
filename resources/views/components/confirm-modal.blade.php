@props([
    'name',
    'title' => 'Konfirmasi Tindakan',
    'type' => 'danger',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'maxWidth' => 'sm:max-w-md'
])

@php
    $typeColors = match($type) {
        'danger' => [
            'icon_bg' => 'bg-red-100 text-red-600',
            'button' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500 shadow-xs'
        ],
        'warning' => [
            'icon_bg' => 'bg-amber-100 text-amber-600',
            'button' => 'bg-amber-600 hover:bg-amber-700 text-white focus:ring-amber-500 shadow-xs'
        ],
        default => [
            'icon_bg' => 'bg-[#EFF3EB] text-[#1B3B2B]',
            'button' => 'bg-[#1B3B2B] hover:bg-[#12271E] text-white focus:ring-[#1B3B2B] shadow-xs'
        ],
    };
@endphp

<div x-data="{ 
        open: false,
        openModal() {
            this.open = true;
            document.body.classList.add('overflow-hidden');
        },
        closeModal() {
            this.open = false;
            document.body.classList.remove('overflow-hidden');
        }
     }"
     x-on:open-modal.window="if ($event.detail === '{{ $name }}' || (typeof $event.detail === 'object' && $event.detail.name === '{{ $name }}')) { openModal(); }"
     x-on:close-modal.window="if ($event.detail === '{{ $name }}' || (typeof $event.detail === 'object' && $event.detail.name === '{{ $name }}')) { closeModal(); }"
     x-on:keydown.escape.window="if (open) { closeModal(); }"
     x-cloak>

    <template x-teleport="body">
        <div x-show="open" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
             style="display: none;"
             role="dialog"
             aria-modal="true"
             aria-labelledby="modal-title-{{ md5($name) }}">

            {{-- 1. Backdrop Overlay --}}
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-[#122B1F]/60 backdrop-blur-xs transition-opacity"
                 @click="closeModal()"></div>

            {{-- 2. Modal Window Card --}}
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative w-full {{ $maxWidth }} bg-white rounded-2xl shadow-2xl border border-[#E0E7DC] overflow-hidden transform transition-all z-10 my-auto"
                 @click.outside="closeModal()">

                <div class="bg-white p-6">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl {{ $typeColors['icon_bg'] }} sm:mx-0">
                            @if(isset($icon))
                                {{ $icon }}
                            @else
                                @if($type === 'danger')
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                @elseif($type === 'warning')
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                    </svg>
                                @else
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                                    </svg>
                                @endif
                            @endif
                        </div>

                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-[#122B1F]" id="modal-title-{{ md5($name) }}">
                                {{ $title }}
                            </h3>
                            <div class="mt-2 text-xs sm:text-sm text-[#526057] space-y-2">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($footer))
                    <div class="bg-[#F8FAF7] px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-[#E0E7DC]">
                        {{ $footer }}
                    </div>
                @endif

            </div>
        </div>
    </template>
</div>
