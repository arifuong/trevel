<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @php
        $pageTitle = $title ?? 'PT. Zein Internasional — Penyelenggara Umrah & Haji Khusus Resmi';
        $pageDesc = $metaDescription ?? 'Penyelenggara Perjalanan Ibadah Umrah (PPIU) dan Haji Khusus resmi berizin Kemenag RI. Bimbingan ibadah murni sesuai Sunnah dengan kenyamanan terpercaya.';
        $pageImage = $ogImage ?? asset('images/hero-1200.webp');
        $pageType = $ogType ?? 'website';
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDesc }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#1B3B2B">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Open Graph / Facebook / WhatsApp Preview Meta -->
    <meta property="og:site_name" content="PT. Zein Internasional (Zeintour)">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:type" content="{{ $pageType }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:image:alt" content="{{ $pageTitle }}">

    <!-- Twitter Card Meta -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    <!-- Resource Hints -->
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
    <link rel="dns-prefetch" href="https://images.unsplash.com">
    <link rel="preconnect" href="https://img.youtube.com">
    <link rel="dns-prefetch" href="https://img.youtube.com">

    <!-- Preload Critical Typography Font -->
    <link rel="preload" href="{{ asset('fonts/playfair-display-700.woff2') }}" as="font" type="font/woff2" crossorigin>

    <!-- Preload Critical LCP Hero Image with Responsive Srcset Matching Device DPR -->
    <link rel="preload" as="image" 
          href="{{ asset('images/hero-1200.webp') }}" 
          imagesrcset="{{ asset('images/hero-480.webp') }} 480w, {{ asset('images/hero-640.webp') }} 640w, {{ asset('images/hero-1200.webp') }} 1200w, {{ asset('images/hero-1920.webp') }} 1920w" 
          imagesizes="100vw" 
          type="image/webp" 
          fetchpriority="high">

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

    @vite(['resources/css/app.css'])
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

    <!-- Toast Notification Container -->
    <x-toast-notification />

    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>
