<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'PT. Zein Internasional — Penyelenggara Umrah & Haji Khusus Resmi' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Penyelenggara Perjalanan Ibadah Umrah (PPIU) dan Haji Khusus resmi berizin Kemenag RI. Bimbingan ibadah murni sesuai Sunnah dengan kenyamanan terpercaya.' }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#1B3B2B">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Open Graph / Meta -->
    <meta property="og:title" content="{{ $title ?? 'PT. Zein Internasional — Travel Umrah & Haji Khusus Resmi' }}">
    <meta property="og:description" content="Penyelenggara Perjalanan Ibadah Umrah dan Haji Khusus resmi berizin Kemenag RI.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Resource Hints -->
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
    <link rel="dns-prefetch" href="https://images.unsplash.com">

    <!-- Preload Critical Typography Font -->
    <link rel="preload" href="{{ asset('fonts/playfair-display-700.woff2') }}" as="font" type="font/woff2" crossorigin>

    <!-- Preload Critical LCP Hero Image (Media targeted for Instant LCP) -->
    <link rel="preload" as="image" href="{{ asset('images/hero-1920.webp') }}" media="(min-width: 1025px)" type="image/webp" fetchpriority="high">
    <link rel="preload" as="image" href="{{ asset('images/hero-640.webp') }}" media="(min-width: 641px) and (max-width: 1024px)" type="image/webp" fetchpriority="high">
    <link rel="preload" as="image" href="{{ asset('images/hero-480.webp') }}" media="(max-width: 640px)" type="image/webp" fetchpriority="high">

    <!-- Structured Data (JSON-LD) for Rich SEO & Crawlers -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "TravelAgency",
        "name": "{{ $company['name'] ?? 'PT. ZEIN INTERNASIONAL' }}",
        "alternateName": "ZEIN TOUR",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo-zein.webp') }}",
        "image": "{{ asset('images/hero-1200.webp') }}",
        "description": "{{ $metaDescription ?? 'Penyelenggara Perjalanan Ibadah Umrah (PPIU) dan Haji Khusus resmi berizin Kemenag RI.' }}",
        "telephone": "+6282121483337",
        "address": {
            "@@type": "PostalAddress",
            "streetAddress": "Jl. Cihanjuang Kp. Karangsari No.15",
            "addressLocality": "Bandung Barat",
            "postalCode": "40559",
            "addressCountry": "ID"
        },
        "priceRange": "Rp 24.900.000 - Rp 265.000.000"
    }
    </script>

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
    @stack('styles')
</head>
<body class="font-sans antialiased text-[#526057] bg-white flex flex-col min-h-screen selection:bg-[#1B3B2B] selection:text-white" x-data="{ mobileMenuOpen: false }">
    
    <!-- Navbar Component (Top Utility Bar + Main White Navbar) -->
    <x-navbar :company="$company ?? null" />

    <!-- Main Content Slot -->
    <main class="flex-grow pt-[108px] lg:pt-[118px]">
        {{ $slot }}
    </main>

    <!-- Footer Component -->
    <x-footer :company="$company ?? null" />

    <!-- Floating WhatsApp CTA -->
    <x-whatsapp-float :phone="$company['whatsapp'] ?? '6281222222562'" />

    <!-- Scroll To Top Button -->
    <button id="scroll-top" 
            aria-label="Kembali ke atas"
            class="fixed bottom-6 left-6 z-40 p-2.5 rounded-full bg-white text-[#1B3B2B] shadow-md border border-[#E0E7DC] hover:bg-[#EFF3EB] transition-all opacity-0 pointer-events-none translate-y-2 cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
