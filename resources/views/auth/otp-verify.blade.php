<x-layouts.main :title="'Verifikasi OTP — PT. Zein Internasional'">

    <section class="min-h-[calc(100vh-108px)] lg:min-h-[calc(100vh-118px)] flex items-center justify-center bg-gradient-to-br from-[#EFF3EB] via-white to-[#EFF3EB] py-10 sm:py-16 px-4 sm:px-6">
        
        <div class="w-full max-w-[440px]" x-data="otpForm()">

            {{-- Main Authentication Card --}}
            <div class="bg-white rounded-3xl border border-[#E0E7DC] shadow-xl shadow-[#1B3B2B]/5 overflow-hidden transition-all {{ $errors->any() || session('error') ? 'animate-shake' : 'animate-authIn' }}">

                {{-- Header Bagian Atas --}}
                <div class="pt-8 sm:pt-10 pb-6 px-7 sm:px-9 text-center border-b border-[#E0E7DC]/60 bg-gradient-to-b from-[#EFF3EB]/50 to-white">
                    
                    {{-- WhatsApp Icon Badge --}}
                    <div class="w-14 h-14 rounded-2xl bg-[#EAF1E8] border border-[#CCD8C7] flex items-center justify-center mx-auto mb-4 shadow-xs">
                        <svg class="w-7 h-7 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>

                    <h1 class="text-2xl sm:text-[1.75rem] font-bold text-[#12271E] tracking-tight leading-tight">
                        Verifikasi Kode OTP
                    </h1>
                    <p class="text-xs text-[#526057] mt-1.5 leading-relaxed max-w-xs mx-auto">
                        Kode 6 digit telah dikirimkan ke WhatsApp Anda di <strong class="text-[#12271E]">{{ substr($phone, 0, 4) }}****{{ substr($phone, -4) }}</strong>
                    </p>
                </div>

                {{-- Form Section --}}
                <div class="px-7 sm:px-9 py-7 sm:py-8">

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-xs text-emerald-800 flex items-start gap-2.5 shadow-2xs" role="alert">
                            <svg class="w-4 h-4 shrink-0 mt-0.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium leading-relaxed">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mb-5 p-3.5 rounded-2xl bg-amber-50 border border-amber-200/80 text-xs text-amber-800 flex items-start gap-2.5 shadow-2xs" role="alert">
                            <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                            <span class="font-medium leading-relaxed">{{ session('warning') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-5 p-3.5 rounded-2xl bg-red-50 border border-red-200/80 text-xs text-red-800 flex items-start gap-2.5 shadow-2xs" role="alert">
                            <svg class="w-4 h-4 shrink-0 mt-0.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                            </svg>
                            <span class="font-medium leading-relaxed">{{ session('error') }}</span>
                        </div>
                    @endif

                    <form method="POST" 
                          action="{{ route('otp.verify') }}" 
                          @submit="if(isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true;">
                        @csrf

                        {{-- OTP Input: 6 Kotak Individual --}}
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-[#12271E] mb-3 text-center">
                                Masukkan 6 Digit Kode OTP
                            </label>
                            <div class="flex justify-center gap-2 sm:gap-2.5">
                                <template x-for="(digit, index) in digits" :key="index">
                                    <input type="text"
                                           maxlength="1"
                                           inputmode="numeric"
                                           x-model="digits[index]"
                                           @input="onInput($event, index)"
                                           @keydown.backspace="onBackspace($event, index)"
                                           @paste="onPaste($event)"
                                           :x-ref="'otp' + index"
                                           class="w-11 sm:w-12 h-14 text-center text-lg sm:text-xl font-bold text-[#12271E] rounded-xl border-2 transition-all outline-none"
                                           :class="digits[index] ? 'border-[#1B3B2B] bg-[#EFF3EB]/60 ring-2 ring-[#1B3B2B]/10' : 'border-[#CCD8C7] bg-white focus:border-[#1B3B2B] focus:ring-4 focus:ring-[#1B3B2B]/10'"
                                           aria-label="Digit OTP">
                                </template>
                            </div>
                            <input type="hidden" name="otp" :value="digits.join('')">
                            @error('otp')
                                <p class="mt-2 text-[11px] text-red-600 font-medium text-center flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.007v.008H12v-.008z"/></svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        {{-- Tombol Submit --}}
                        <div>
                            <button type="submit"
                                    :class="(digits.join('').length < 6 || isSubmitting) ? 'pointer-events-none opacity-60' : ''"
                                    class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all duration-200 shadow-md shadow-[#1B3B2B]/15 cursor-pointer focus-visible:ring-4 focus-visible:ring-[#1B3B2B]/20 focus:outline-none">
                                
                                <svg x-show="isSubmitting" 
                                     class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" 
                                     xmlns="http://www.w3.org/2000/svg" 
                                     fill="none" 
                                     viewBox="0 0 24 24" 
                                     style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>

                                <span x-text="isSubmitting ? 'Memverifikasi...' : 'Verifikasi Akun'"></span>

                                <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                        </div>
                    </form>

                    {{-- Resend Section --}}
                    <div class="mt-6 text-center border-t border-[#E0E7DC] pt-5">
                        <p class="text-xs text-[#526057] mb-2">Tidak menerima kode di WhatsApp?</p>
                        <form method="POST" action="{{ route('otp.resend') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-[#1B3B2B] hover:text-[#132E22] hover:underline cursor-pointer transition-colors">
                                Kirim Ulang Kode OTP &rarr;
                            </button>
                        </form>
                    </div>

                    {{-- Info Development Mode --}}
                    @if(config('app.debug'))
                    <div class="mt-5 p-3 rounded-2xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-800">
                        <p class="font-bold mb-0.5">🔧 Mode Development</p>
                        <p class="text-amber-700">Kode OTP dapat dilihat langsung pada file <code class="px-1 py-0.5 bg-amber-100 rounded text-[10px] font-mono">storage/logs/laravel.log</code></p>
                    </div>
                    @endif

                </div>
            </div>

            {{-- Trust Badge --}}
            <div class="mt-6 text-center">
                <p class="text-[11px] text-[#526057] flex items-center justify-center gap-1.5 font-medium">
                    <svg class="w-4 h-4 text-[#1B3B2B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                    <span>Verifikasi Resmi PT. Zein Internasional</span>
                </p>
            </div>

        </div>
    </section>

    @push('styles')
    <style>
        @keyframes authIn {
            0% {
                opacity: 0;
                transform: translateY(16px) scale(0.98);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        .animate-authIn {
            animation: authIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .animate-shake {
            animation: shake 0.45s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }

        @media (prefers-reduced-motion: reduce) {
            .animate-authIn,
            .animate-shake {
                animation: none !important;
                transform: none !important;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function otpForm() {
            return {
                digits: ['', '', '', '', '', ''],
                isSubmitting: false,
                onInput(event, index) {
                    const value = event.target.value.replace(/\D/g, '');
                    this.digits[index] = value.charAt(0) || '';
                    event.target.value = this.digits[index];
                    if (this.digits[index] && index < 5) {
                        this.$nextTick(() => {
                            const next = this.$root.querySelectorAll('input[maxlength="1"]')[index + 1];
                            if (next) next.focus();
                        });
                    }
                },
                onBackspace(event, index) {
                    if (!this.digits[index] && index > 0) {
                        const prev = this.$root.querySelectorAll('input[maxlength="1"]')[index - 1];
                        if (prev) prev.focus();
                    }
                },
                onPaste(event) {
                    event.preventDefault();
                    const paste = (event.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                    for (let i = 0; i < 6; i++) {
                        this.digits[i] = paste.charAt(i) || '';
                    }
                }
            }
        }
    </script>
    @endpush

</x-layouts.main>
