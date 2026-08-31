@props([
    'id' => null,
    'badge' => null,
    'title' => null,
    'subtitle' => null,
    'dark' => false,
    'bg' => 'bg-[#FAFAF8]',
    'class' => '',
    'center' => false,
])

<section @if($id) id="{{ $id }}" @endif class="py-20 sm:py-28 {{ $dark ? 'bg-[#112620] text-white' : $bg . ' text-[#27272A]' }} {{ $class }} relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        @if($title || $badge)
            <div data-reveal class="mb-14 sm:mb-16 {{ $center ? 'text-center max-w-3xl mx-auto' : 'max-w-2xl' }}">
                @if($badge)
                    <div class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-widest text-[#1B4D3E] mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C2A264]"></span>
                        <span>{{ $badge }}</span>
                    </div>
                @endif

                @if($title)
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight {{ $dark ? 'text-white' : 'text-[#18181B]' }} leading-tight mb-4">
                        {!! $title !!}
                    </h2>
                @endif

                @if($subtitle)
                    <p class="text-sm sm:text-base {{ $dark ? 'text-zinc-400' : 'text-[#71717A]' }} leading-relaxed font-normal">
                        {!! $subtitle !!}
                    </p>
                @endif
            </div>
        @endif

        {{ $slot }}
    </div>
</section>
