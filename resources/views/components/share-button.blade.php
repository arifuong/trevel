@props([
    'url' => '',
    'title' => '',
    'summary' => '',
    'text' => '',
    'variant' => 'card', // 'card', 'button', 'full'
    'align' => 'right',  // 'left', 'right', 'center'
    'placement' => 'top' // 'auto', 'top', 'bottom'
])

@php
    $shareUrl = $url ?: url()->current();
    $shareTitle = $title ?: 'Paket Umrah & Haji Khusus — PT. Zein Internasional';
    // Gunakan info ringkas yang sudah tersedia tanpa mengarang deskripsi baru
    $shareSummary = $summary ?: ($text ?: '');
@endphp

<div class="relative inline-block"
     :class="{ 'z-40': open, 'z-10': !open }"
     x-data="{
         open: false,
         copied: false,
         url: @js($shareUrl),
         title: @js($shareTitle),
         summary: @js($shareSummary),

         // Format WhatsApp sesuai spesifikasi:
         // Halo, saya menemukan paket ini:
         // [Nama Paket]
         // [Informasi paket yang sudah tersedia]
         // [URL Paket]
         get whatsappUrl() {
             let msg = 'Halo, saya menemukan paket ini:\n*' + this.title + '*';
             if (this.summary) {
                 msg += '\n' + this.summary;
             }
             msg += '\n\n' + this.url;
             return 'https://api.whatsapp.com/send?text=' + encodeURIComponent(msg);
         },

         // Facebook Share Dialog resmi
         get facebookUrl() {
             return 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(this.url);
         },

         // Telegram Share URL: URL + nama paket sebagai text
         get telegramUrl() {
             const t = this.title + (this.summary ? ' - ' + this.summary : '');
             return 'https://t.me/share/url?url=' + encodeURIComponent(this.url) + '&text=' + encodeURIComponent(t);
         },

         // X / Twitter Share Intent: text = nama paket, url = URL paket
         get twitterUrl() {
             return 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(this.title) + '&url=' + encodeURIComponent(this.url);
         },

         // Pemicu aksi Share: Utamakan Native Share jika di perangkat mobile & browser mendukung Web Share API
         share() {
             const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) 
                           || (window.matchMedia && window.matchMedia('(max-width: 768px)').matches && 'ontouchstart' in window);

             if (isMobile && navigator.share) {
                 const shareData = {
                     title: this.title,
                     text: this.title + (this.summary ? '\n' + this.summary : ''),
                     url: this.url
                 };

                 try {
                     if (!navigator.canShare || navigator.canShare(shareData)) {
                         navigator.share(shareData).catch(err => {
                             // Jangan error jika user membatalkan (AbortError)
                             if (err && err.name !== 'AbortError') {
                                 this.open = true;
                             }
                         });
                         return;
                     }
                 } catch (e) {
                     // Fallback jika terjadi exception
                     this.open = true;
                     return;
                 }
             }

             // Fallback atau Desktop: Buka menu share
             this.open = !this.open;
         },

         // Copy Link ke clipboard dengan feedback toast
         copyLink() {
             const onSuccess = () => {
                 this.copied = true;
                 window.dispatchEvent(new CustomEvent('toast', {
                     detail: {
                         type: 'success',
                         message: 'Link berhasil disalin'
                     }
                 }));
                 setTimeout(() => {
                     this.copied = false;
                     this.open = false;
                 }, 1200);
             };

             if (navigator.clipboard && window.isSecureContext) {
                 navigator.clipboard.writeText(this.url)
                     .then(onSuccess)
                     .catch(() => this.fallbackCopy(this.url, onSuccess));
             } else {
                 this.fallbackCopy(this.url, onSuccess);
             }
         },

         fallbackCopy(text, cb) {
             const textArea = document.createElement('textarea');
             textArea.value = text;
             textArea.style.position = 'fixed';
             textArea.style.left = '-999999px';
             textArea.style.opacity = '0';
             document.body.appendChild(textArea);
             textArea.focus();
             textArea.select();
             try {
                 document.execCommand('copy');
                 if (cb) cb();
             } catch (err) {
                 console.error('Fallback copy failed', err);
             }
             document.body.removeChild(textArea);
         }
     }"
     @click.outside="open = false"
     @keydown.escape.window="open = false">

    {{-- 1. TOMBOL PEMICU SHARE BERDASARKAN VARIAN --}}
    @if($variant === 'card')
        <button type="button" 
                @click="share()" 
                class="h-11 w-11 rounded-xl bg-[#EFF3EB] hover:bg-[#E0E7DC] text-[#1B3B2B] active:scale-95 flex items-center justify-center transition-all duration-200 cursor-pointer shrink-0 group/share"
                aria-label="Bagikan paket"
                :aria-expanded="open.toString()">
            <svg class="w-4 h-4 text-[#1B3B2B] transition-transform duration-200 group-hover/share:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
            </svg>
        </button>

    @elseif($variant === 'button')
        <button type="button" 
                @click="share()" 
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-[#E5E9E2] bg-white hover:bg-[#EFF3EB] text-[#1B3B2B] hover:text-[#12271E] font-semibold text-xs active:scale-95 transition-all duration-200 cursor-pointer shadow-2xs hover:shadow-xs group/share"
                aria-label="Bagikan paket"
                :aria-expanded="open.toString()">
            <svg class="w-3.5 h-3.5 text-[#1B3B2B] transition-transform duration-200 group-hover/share:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
            </svg>
            <span>Bagikan</span>
        </button>

    @elseif($variant === 'full')
        <button type="button" 
                @click="share()" 
                class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-semibold text-[#1B3B2B] bg-[#EFF3EB] hover:bg-[#E0E7DC] transition-colors border border-[#E0E7DC] cursor-pointer"
                aria-label="Bagikan paket"
                :aria-expanded="open.toString()">
            <svg class="w-3.5 h-3.5 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
            </svg>
            <span>Bagikan Paket Ini</span>
        </button>
    @endif

    {{-- 2. DESKTOP POPOVER MENU (Dekat tombol share) --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
         class="hidden sm:block absolute z-50 w-64 rounded-2xl bg-white border border-[#E5E9E2] shadow-[0_8px_24px_rgba(0,0,0,0.12)] p-2 text-left space-y-0.5
                {{ $placement === 'top' || ($placement === 'auto' && $variant === 'card') ? 'bottom-full mb-2' : 'top-full mt-2' }}
                {{ $align === 'right' ? 'right-0' : ($align === 'center' ? 'left-1/2 -translate-x-1/2' : 'left-0') }}"
         style="display: none;"
         role="menu"
         aria-orientation="vertical">

        <div class="px-3 py-2 border-b border-[#E8EDE5]">
            <span class="text-[10px] font-bold uppercase tracking-wider text-[#C2A264] block">
                Bagikan Paket
            </span>
            <p class="text-[11px] text-[#12271E] font-semibold truncate mt-0.5" x-text="title"></p>
        </div>

        {{-- 1. WhatsApp --}}
        <a :href="whatsappUrl" 
           target="_blank" 
           rel="noopener noreferrer"
           @click="open = false"
           class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-semibold text-[#12271E] hover:bg-[#EFF3EB] hover:text-[#1B3B2B] transition-colors"
           role="menuitem"
           aria-label="Bagikan ke WhatsApp">
            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.072-1.895-.449-1.612-.665-2.651-2.318-2.733-2.427-.082-.109-.652-.867-.652-1.654 0-.786.413-1.172.56-1.332.146-.16.321-.2.428-.2.106 0 .213.002.307.01.099.007.23-.038.361.275.137.327.468 1.144.509 1.228.041.084.068.183.014.293-.054.11-.082.179-.164.275-.082.096-.172.215-.246.289-.082.082-.168.172-.072.336.096.164.426.702.915 1.137.629.56 1.16.733 1.324.815.164.082.26.069.356-.041.096-.11.413-.48.523-.645.11-.164.22-.137.369-.082.151.055.955.451 1.119.533.164.082.274.123.315.191.041.07.041.403-.103.808z"/>
                </svg>
            </div>
            <span>WhatsApp</span>
        </a>

        {{-- 2. Facebook --}}
        <a :href="facebookUrl" 
           target="_blank" 
           rel="noopener noreferrer"
           @click="open = false"
           class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-semibold text-[#12271E] hover:bg-[#EFF3EB] hover:text-[#1B3B2B] transition-colors"
           role="menuitem"
           aria-label="Bagikan ke Facebook">
            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </div>
            <span>Facebook</span>
        </a>

        {{-- 3. Telegram --}}
        <a :href="telegramUrl" 
           target="_blank" 
           rel="noopener noreferrer"
           @click="open = false"
           class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-semibold text-[#12271E] hover:bg-[#EFF3EB] hover:text-[#1B3B2B] transition-colors"
           role="menuitem"
           aria-label="Bagikan ke Telegram">
            <div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.05-.2-.07-.06-.17-.04-.24-.02-.11.02-1.78 1.13-5.03 3.32-.48.33-.91.49-1.3.48-.43-.01-1.25-.24-1.86-.44-.75-.24-1.34-.37-1.29-.78.03-.21.32-.43.87-.66 3.41-1.48 5.69-2.46 6.84-2.95 3.26-1.36 3.94-1.6 4.38-1.6.1 0 .32.02.46.14.12.1.15.24.16.34-.01.07.01.22 0 .28z"/>
                </svg>
            </div>
            <span>Telegram</span>
        </a>

        {{-- 4. X / Twitter --}}
        <a :href="twitterUrl" 
           target="_blank" 
           rel="noopener noreferrer"
           @click="open = false"
           class="flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-semibold text-[#12271E] hover:bg-[#EFF3EB] hover:text-[#1B3B2B] transition-colors"
           role="menuitem"
           aria-label="Bagikan ke X Twitter">
            <div class="w-7 h-7 rounded-lg bg-zinc-100 text-zinc-900 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
            </div>
            <span>X (Twitter)</span>
        </a>

        {{-- 5. Copy Link --}}
        <button type="button" 
                @click="copyLink()"
                class="w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl text-xs font-semibold text-[#12271E] hover:bg-[#EFF3EB] hover:text-[#1B3B2B] transition-colors cursor-pointer"
                role="menuitem"
                aria-label="Salin tautan paket">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-colors"
                 :class="copied ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-[#12271E]'">
                <template x-if="!copied">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.232 5.241l-.847 1.693a4.5 4.5 0 01-6.03 2.01l-.472-.236a4.5 4.5 0 01-2.01-6.03l.847-1.693a4.5 4.5 0 015.24-1.232m2.476-4.839a4.5 4.5 0 01-1.232-5.241l.847-1.693a4.5 4.5 0 016.03-2.01l.472.236a4.5 4.5 0 012.01 6.03l-.847 1.693a4.5 4.5 0 01-5.24 1.232" />
                    </svg>
                </template>
                <template x-if="copied">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </template>
            </div>
            <span x-text="copied ? 'Link berhasil disalin' : 'Copy Link'"></span>
        </button>
    </div>

    {{-- 3. MOBILE BOTTOM SHEET MODAL (Nyaman disentuh, Touch Target >= 44px) --}}
    <template x-teleport="body">
        <div x-show="open" 
             x-cloak
             class="sm:hidden fixed inset-0 z-50 flex items-end justify-center"
             role="dialog"
             aria-modal="true"
             aria-labelledby="share-bottomsheet-title">

            {{-- Backdrop --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="open = false"
                 class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity">
            </div>

            {{-- Bottom Sheet Container --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0 opacity-100"
                 x-transition:leave-end="translate-y-full opacity-0"
                 @click.outside="open = false"
                 class="relative w-full max-w-lg bg-white rounded-t-3xl shadow-2xl p-5 z-10 pb-8 safe-bottom">

                {{-- Grab handle --}}
                <div class="w-10 h-1 bg-zinc-200 rounded-full mx-auto mb-3"></div>

                {{-- Header --}}
                <div class="flex items-center justify-between pb-3 border-b border-[#E8EDE5]">
                    <div class="pr-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#C2A264] block">
                            Bagikan Paket
                        </span>
                        <h4 id="share-bottomsheet-title" class="text-sm font-bold text-[#12271E] truncate max-w-[260px]" x-text="title"></h4>
                    </div>
                    <button type="button" 
                            @click="open = false" 
                            class="p-2 rounded-full hover:bg-[#EFF3EB] text-zinc-400 hover:text-[#12271E] transition-colors cursor-pointer"
                            aria-label="Tutup menu">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Daftar Opsi Share Mobile (Touch Target >= 44px) --}}
                <div class="pt-2 space-y-1">
                    {{-- 1. WhatsApp --}}
                    <a :href="whatsappUrl" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       @click="open = false"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-2xl text-sm font-semibold text-[#12271E] hover:bg-[#EFF3EB] active:bg-[#E0E7DC] transition-colors min-h-[48px]"
                       role="menuitem"
                       aria-label="Bagikan ke WhatsApp">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.072-1.895-.449-1.612-.665-2.651-2.318-2.733-2.427-.082-.109-.652-.867-.652-1.654 0-.786.413-1.172.56-1.332.146-.16.321-.2.428-.2.106 0 .213.002.307.01.099.007.23-.038.361.275.137.327.468 1.144.509 1.228.041.084.068.183.014.293-.054.11-.082.179-.164.275-.082.096-.172.215-.246.289-.082.082-.168.172-.072.336.096.164.426.702.915 1.137.629.56 1.16.733 1.324.815.164.082.26.069.356-.041.096-.11.413-.48.523-.645.11-.164.22-.137.369-.082.151.055.955.451 1.119.533.164.082.274.123.315.191.041.07.041.403-.103.808z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span>WhatsApp</span>
                            <span class="text-[11px] text-[#526057] font-normal">Kirim langsung ke pesan atau grup</span>
                        </div>
                    </a>

                    {{-- 2. Facebook --}}
                    <a :href="facebookUrl" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       @click="open = false"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-2xl text-sm font-semibold text-[#12271E] hover:bg-[#EFF3EB] active:bg-[#E0E7DC] transition-colors min-h-[48px]"
                       role="menuitem"
                       aria-label="Bagikan ke Facebook">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span>Facebook</span>
                            <span class="text-[11px] text-[#526057] font-normal">Bagikan tautan di linimasa Facebook</span>
                        </div>
                    </a>

                    {{-- 3. Telegram --}}
                    <a :href="telegramUrl" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       @click="open = false"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-2xl text-sm font-semibold text-[#12271E] hover:bg-[#EFF3EB] active:bg-[#E0E7DC] transition-colors min-h-[48px]"
                       role="menuitem"
                       aria-label="Bagikan ke Telegram">
                        <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-500 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.05-.2-.07-.06-.17-.04-.24-.02-.11.02-1.78 1.13-5.03 3.32-.48.33-.91.49-1.3.48-.43-.01-1.25-.24-1.86-.44-.75-.24-1.34-.37-1.29-.78.03-.21.32-.43.87-.66 3.41-1.48 5.69-2.46 6.84-2.95 3.26-1.36 3.94-1.6 4.38-1.6.1 0 .32.02.46.14.12.1.15.24.16.34-.01.07.01.22 0 .28z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span>Telegram</span>
                            <span class="text-[11px] text-[#526057] font-normal">Kirim via obrolan Telegram</span>
                        </div>
                    </a>

                    {{-- 4. X / Twitter --}}
                    <a :href="twitterUrl" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       @click="open = false"
                       class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-2xl text-sm font-semibold text-[#12271E] hover:bg-[#EFF3EB] active:bg-[#E0E7DC] transition-colors min-h-[48px]"
                       role="menuitem"
                       aria-label="Bagikan ke X Twitter">
                        <div class="w-9 h-9 rounded-xl bg-zinc-100 text-zinc-900 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span>X (Twitter)</span>
                            <span class="text-[11px] text-[#526057] font-normal">Posting postingan di linimasa X</span>
                        </div>
                    </a>

                    {{-- 5. Copy Link --}}
                    <button type="button" 
                            @click="copyLink()"
                            class="w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-2xl text-sm font-semibold text-[#12271E] hover:bg-[#EFF3EB] active:bg-[#E0E7DC] transition-colors min-h-[48px] cursor-pointer text-left"
                            role="menuitem"
                            aria-label="Salin tautan paket">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                             :class="copied ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-[#12271E]'">
                            <template x-if="!copied">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.232 5.241l-.847 1.693a4.5 4.5 0 01-6.03 2.01l-.472-.236a4.5 4.5 0 01-2.01-6.03l.847-1.693a4.5 4.5 0 015.24-1.232m2.476-4.839a4.5 4.5 0 01-1.232-5.241l.847-1.693a4.5 4.5 0 016.03-2.01l.472.236a4.5 4.5 0 012.01 6.03l-.847 1.693a4.5 4.5 0 01-5.24 1.232" />
                                </svg>
                            </template>
                            <template x-if="copied">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                            </template>
                        </div>
                        <div class="flex flex-col">
                            <span x-text="copied ? 'Link berhasil disalin' : 'Copy Link'"></span>
                            <span class="text-[11px] text-[#526057] font-normal" x-text="copied ? 'Tautan siap dibagikan' : 'Salin URL paket ke papan klip'"></span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>