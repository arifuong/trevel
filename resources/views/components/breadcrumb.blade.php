@props(['items' => []])

<nav aria-label="Breadcrumb" class="py-4">
    <ol class="flex items-center space-x-2 text-xs sm:text-sm text-gray-500">
        <li>
            <a href="{{ route('home') }}" class="hover:text-[#065F46] flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span>Beranda</span>
            </a>
        </li>

        @foreach($items as $item)
            <li class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                @if(!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="hover:text-[#065F46]">{{ $item['title'] }}</a>
                @else
                    <span class="font-bold text-[#065F46]">{{ $item['title'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
