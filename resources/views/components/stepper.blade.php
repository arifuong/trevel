@props(['steps' => []])

<div class="relative space-y-0" data-reveal-stagger="80">
    @foreach($steps as $index => $step)
        <div data-reveal-child class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-6 items-start {{ $index > 0 ? 'pt-6 mt-6 border-t border-[#E0E7DC]' : '' }}">
            
            <!-- Angka Nomor Besar (2 kolom di sm+, block di mobile) -->
            <div class="sm:col-span-2 shrink-0 flex items-center sm:block">
                <span class="font-serif text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#1B3B2B]/60 tracking-tighter leading-none block" aria-hidden="true">
                    {{ $step['step'] }}
                </span>
            </div>

            <!-- Konten Tahap (10 kolom di sm+) -->
            <div class="sm:col-span-10">
                <h3 class="font-serif text-base sm:text-lg font-bold text-[#12271E] mb-1.5 leading-snug block">
                    {{ $step['title'] }}
                </h3>
                <p class="text-xs sm:text-sm text-[#526057] leading-relaxed max-w-2xl">
                    {{ $step['desc'] }}
                </p>
            </div>
        </div>
    @endforeach
</div>
