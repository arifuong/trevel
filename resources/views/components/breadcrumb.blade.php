@props(['items' => []])

<nav aria-label="Breadcrumb" class="py-2.5 sm:py-3">
    <ol class="flex items-center flex-wrap gap-1.5 sm:gap-2 text-[11px] sm:text-xs text-[#526057]">
        <li>
            <a href="{{ route('home') }}" class="hover:text-[#12271E] transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-[#526057]/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span>Beranda</span>
            </a>
        </li>

        @foreach($items as $item)
            <li class="flex items-center gap-1.5 sm:gap-2">
                <svg class="w-3 h-3 text-[#526057]/40 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
                @if(!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="hover:text-[#12271E] transition-colors">{{ $item['title'] }}</a>
                @else
                    <span class="font-medium text-[#1B3B2B] truncate max-w-[200px] sm:max-w-none">{{ $item['title'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
