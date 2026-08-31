<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — PT. Zein Internasional</title>
    <meta name="robots" content="noindex, nofollow">

    @php
        $manifestPath = public_path('build/manifest.json');
        $compiledCssContent = '';
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true) ?: [];
            $cssEntry = $manifest['resources/css/app.css']['file'] ?? null;
            if ($cssEntry && file_exists(public_path('build/' . $cssEntry))) {
                $compiledCssContent = file_get_contents(public_path('build/' . $cssEntry));
            }
        }
    @endphp

    @if(!empty($compiledCssContent))
        <style>{!! $compiledCssContent !!}</style>
    @else
        @vite(['resources/css/app.css'])
    @endif

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
</head>
<body class="font-sans antialiased bg-[#122B1F] min-h-screen flex items-center justify-center p-4 sm:p-6" 
      x-data="{ showPassword: false, isSubmitting: false }">

    <div class="w-full max-w-[420px]">

        {{-- Card Login Admin --}}
        <div class="bg-white rounded-3xl border border-white/20 shadow-2xl shadow-black/40 overflow-hidden transition-all {{ $errors->any() || session('error') ? 'animate-shake' : 'animate-authIn' }}">

            {{-- Header --}}
            <div class="pt-8 sm:pt-10 pb-6 px-7 sm:px-9 text-center bg-gradient-to-b from-[#EFF3EB]/60 to-white border-b border-[#E0E7DC]/60">
                <div class="w-14 h-14 rounded-2xl bg-[#1B3B2B] text-white flex items-center justify-center mx-auto mb-4 shadow-lg ring-4 ring-[#EAF1E8]">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-[#12271E] tracking-tight">Panel Administrasi</h1>
                <p class="text-xs text-[#526057] mt-1.5 font-medium">PT. Zein Internasional — Travel Umrah & Haji</p>
            </div>

            {{-- Form Section --}}
            <div class="px-7 sm:px-9 py-7 sm:py-8">

                {{-- Error Alert --}}
                @if(session('error'))
                    <div class="mb-5 p-3.5 rounded-2xl bg-red-50 border border-red-200/80 text-xs text-red-800 flex items-start gap-2.5 shadow-2xs" role="alert">
                        <svg class="w-4 h-4 shrink-0 mt-0.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                        <span class="font-medium leading-relaxed">{{ session('error') }}</span>
                    </div>
                @endif

                <form method="POST" 
                      action="{{ route('admin.login') }}" 
                      @submit="if(isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true;" 
                      class="space-y-4.5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-[#12271E] mb-1.5">
                            Email Administrator <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#526057]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl text-xs sm:text-sm text-[#12271E] placeholder-[#526057]/45 bg-white border {{ $errors->has('email') ? 'border-red-400 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 bg-red-50/20' : 'border-[#CCD8C7] hover:border-[#1B3B2B]/40 focus:border-[#1B3B2B] focus:ring-4 focus:ring-[#1B3B2B]/10' }} transition-all outline-none"
                                   placeholder="admin@zeintour.com">
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-[11px] text-red-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.007v.008H12v-.008z"/></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs font-bold text-[#12271E] mb-1.5">
                            Password Administrator <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#526057]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   class="w-full pl-10 pr-11 py-3 rounded-xl text-xs sm:text-sm text-[#12271E] placeholder-[#526057]/45 bg-white border {{ $errors->has('password') ? 'border-red-400 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 bg-red-50/20' : 'border-[#CCD8C7] hover:border-[#1B3B2B]/40 focus:border-[#1B3B2B] focus:ring-4 focus:ring-[#1B3B2B]/10' }} transition-all outline-none"
                                   placeholder="••••••••">
                            
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-[#526057] hover:text-[#1B3B2B] focus:outline-none transition-colors cursor-pointer" 
                                    :aria-label="showPassword ? 'Sembunyikan password' : 'Lihat password'"
                                    tabindex="-1">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <svg x-show="showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-[11px] text-red-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.007v.008H12v-.008z"/></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="pt-2">
                        <button type="submit"
                                :class="isSubmitting ? 'pointer-events-none opacity-80' : ''"
                                class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#1B3B2B] hover:bg-[#132E22] active:scale-[0.98] transition-all duration-200 shadow-md shadow-[#1B3B2B]/20 cursor-pointer focus-visible:ring-4 focus-visible:ring-[#1B3B2B]/20 focus:outline-none">
                            
                            <svg x-show="isSubmitting" 
                                 class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" 
                                 xmlns="http://www.w3.org/2000/svg" 
                                 fill="none" 
                                 viewBox="0 0 24 24" 
                                 style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            <span x-text="isSubmitting ? 'Memverifikasi...' : 'Masuk Panel Admin'"></span>

                            <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="text-xs text-[#526057] hover:text-[#1B3B2B] hover:underline transition-colors inline-flex items-center gap-1">
                        &larr; Kembali ke Beranda Website
                    </a>
                </div>

            </div>

        </div>

        {{-- Footer --}}
        <p class="mt-6 text-center text-[11px] text-white/50">&copy; {{ date('Y') }} PT. Zein Internasional • All rights reserved.</p>

    </div>

    @vite(['resources/js/app.js'])
</body>
</html>
