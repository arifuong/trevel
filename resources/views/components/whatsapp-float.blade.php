@props(['phone' => '6281222222562'])

<aside aria-label="Layanan Konsultasi WhatsApp" class="fixed bottom-6 right-6 z-40">
    <a href="https://wa.me/{{ $phone }}?text=Assalamu%27alaikum%20PT.%20Zein%20Internasional,%20saya%20ingin%20konsultasi%20paket%20Umrah/Haji" 
       target="_blank"
       rel="noopener noreferrer"
       class="flex items-center gap-2.5 px-4.5 py-3 bg-[#17382E] text-white rounded-full shadow-lg hover:bg-[#102922] transition-colors border border-white/20"
       aria-label="Konsultasi WhatsApp">
        
        <!-- WhatsApp Icon SVG -->
        <svg class="w-5 h-5 fill-current text-emerald-300" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.99.54 1.761.819 2.796.819 3.18 0 5.767-2.587 5.767-5.766.001-3.18-2.585-5.766-5.767-5.766zm3.39 8.175c-.144.405-.837.774-1.17.824-.312.045-.634.072-1.025-.054-.374-.122-.857-.282-1.488-.564-1.391-.621-2.296-2.032-2.366-2.126-.07-.093-.564-.75-.564-1.429 0-.678.354-1.011.48-1.144.126-.134.275-.168.367-.168.092 0 .184.001.265.006.085.005.199-.033.31.235.115.277.392.955.426 1.025.034.07.057.151.011.242-.045.092-.068.149-.136.228-.068.079-.143.176-.205.236-.068.067-.14.14-.06.277.08.138.356.587.764.951.526.468.97.613 1.108.682.138.069.219.058.3-.035.08-.093.344-.402.436-.54.092-.138.184-.115.31-.069.126.046.804.379.942.448.138.069.23.103.264.161.034.057.034.333-.11.738z"/>
        </svg>

        <span class="text-xs font-bold tracking-wide">Konsultasi WhatsApp</span>
    </a>
</aside>
