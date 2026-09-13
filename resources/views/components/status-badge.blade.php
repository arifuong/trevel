@props([
    'status' => 'draft',
    'label' => null,
    'size' => 'sm',
    'theme' => 'light',
    'showIcon' => true,
    'pulse' => null,
])

@php
    $normalized = strtolower(trim((string) $status));

    // Pemetaan Status Semantik Sistem Informasi Travel Umrah & Haji PT Zein International
    $mapping = [
        // 1. Success (Hijau: Selesai / Valid / Diterima / Lunas / Aktif)
        'lunas' => ['category' => 'success', 'label' => 'Lunas', 'icon' => 'shield_check', 'pulse' => false],
        'valid' => ['category' => 'success', 'label' => 'Valid', 'icon' => 'check', 'pulse' => false],
        'disetujui' => ['category' => 'success', 'label' => 'Disetujui', 'icon' => 'check', 'pulse' => false],
        'approved' => ['category' => 'success', 'label' => 'Disetujui', 'icon' => 'check', 'pulse' => false],
        'aktif' => ['category' => 'success', 'label' => 'Aktif', 'icon' => 'check', 'pulse' => false],
        'terverifikasi' => ['category' => 'success', 'label' => 'Terverifikasi', 'icon' => 'check', 'pulse' => false],
        'jamaah' => ['category' => 'success', 'label' => 'Calon Jamaah Resmi', 'icon' => 'badge_check', 'pulse' => false],
        'calon_jamaah_resmi' => ['category' => 'success', 'label' => 'Calon Jamaah Resmi', 'icon' => 'badge_check', 'pulse' => false],
        'berangkat' => ['category' => 'success', 'label' => 'Siap Berangkat', 'icon' => 'airplane', 'pulse' => true],
        'selesai' => ['category' => 'success', 'label' => 'Selesai', 'icon' => 'sparkles', 'pulse' => false],

        // 2. Warning / Pending (Amber/Kuning: Menunggu Tindakan / Sedang Proses)
        'menunggu_verifikasi' => ['category' => 'warning', 'label' => 'Menunggu Verifikasi', 'icon' => 'clock', 'pulse' => true],
        'menunggu' => ['category' => 'warning', 'label' => 'Menunggu Verifikasi', 'icon' => 'clock', 'pulse' => true],
        'pending' => ['category' => 'warning', 'label' => 'Menunggu Tindakan', 'icon' => 'clock', 'pulse' => true],
        'menunggu_verifikasi_dokumen' => ['category' => 'warning', 'label' => 'Menunggu Verifikasi Dokumen', 'icon' => 'doc_search', 'pulse' => true],
        'menunggu_pembayaran_dp' => ['category' => 'warning', 'label' => 'Menunggu Pembayaran DP', 'icon' => 'banknotes', 'pulse' => true],
        'dokumen_disetujui' => ['category' => 'warning', 'label' => 'Menunggu Pembayaran DP', 'icon' => 'banknotes', 'pulse' => true],
        'menunggu_verifikasi_pembayaran_dp' => ['category' => 'warning', 'label' => 'Menunggu Verifikasi DP', 'icon' => 'refresh', 'pulse' => true],
        'menunggu_kelengkapan_keberangkatan' => ['category' => 'warning', 'label' => 'Kelengkapan Dokumen Keberangkatan', 'icon' => 'doc_search', 'pulse' => true],
        'cicilan_pelunasan' => ['category' => 'warning', 'label' => 'Proses Pelunasan', 'icon' => 'trending_up', 'pulse' => true],

        // 3. Danger / Urgent (Merah: Ditolak / Dibatalkan / Perhatian Khusus)
        'ditolak' => ['category' => 'danger', 'label' => 'Ditolak', 'icon' => 'x_circle', 'pulse' => false],
        'rejected' => ['category' => 'danger', 'label' => 'Ditolak', 'icon' => 'x_circle', 'pulse' => false],
        'dibatalkan' => ['category' => 'danger', 'label' => 'Dibatalkan', 'icon' => 'x_circle', 'pulse' => false],
        'batal' => ['category' => 'danger', 'label' => 'Dibatalkan', 'icon' => 'x_circle', 'pulse' => false],
        'urgent' => ['category' => 'danger', 'label' => 'Perlu Perhatian', 'icon' => 'alert', 'pulse' => true],

        // 4. Neutral / Inactive (Abu-Abu: Draft / Nonaktif / Belum Mulai)
        'draft' => ['category' => 'neutral', 'label' => 'Draft', 'icon' => 'minus', 'pulse' => false],
        'nonaktif' => ['category' => 'neutral', 'label' => 'Nonaktif', 'icon' => 'minus', 'pulse' => false],
        'belum_mulai' => ['category' => 'neutral', 'label' => 'Belum Mulai', 'icon' => 'minus', 'pulse' => false],
        'sold_out' => ['category' => 'neutral', 'label' => 'Sold Out', 'icon' => 'ban', 'pulse' => false],
        'arsip' => ['category' => 'neutral', 'label' => 'Diarsipkan', 'icon' => 'archive', 'pulse' => false],
    ];

    $config = $mapping[$normalized] ?? [
        'category' => 'neutral',
        'label' => ucwords(str_replace(['_', '-'], ' ', $normalized)),
        'icon' => 'info',
        'pulse' => false,
    ];

    $displayLabel = $label ?? $config['label'];
    $category = $config['category'];
    $isPulsing = $pulse !== null ? $pulse : $config['pulse'];

    // Ukuran Font, Padding, & Ikon
    $sizeClasses = match($size) {
        'xs' => ['pill' => 'px-2 py-0.5 text-[10px] gap-1', 'icon' => 'w-2.5 h-2.5', 'dot' => 'w-1 h-1'],
        'md' => ['pill' => 'px-3.5 py-1.5 text-xs gap-2', 'icon' => 'w-4 h-4', 'dot' => 'w-2 h-2'],
        'lg' => ['pill' => 'px-4 py-2 text-sm gap-2.5', 'icon' => 'w-4.5 h-4.5', 'dot' => 'w-2.5 h-2.5'],
        default => ['pill' => 'px-2.5 py-1 text-[11px] gap-1.5', 'icon' => 'w-3 h-3', 'dot' => 'w-1.5 h-1.5'],
    };

    // Styling Semantik (Light Theme vs Dark Theme)
    if ($theme === 'dark') {
        $colorClasses = match($category) {
            'success' => 'bg-emerald-500/20 text-emerald-200 border-emerald-400/30',
            'warning' => 'bg-amber-500/20 text-amber-200 border-amber-400/30',
            'danger' => 'bg-rose-500/20 text-rose-300 border-rose-400/30',
            default => 'bg-slate-500/20 text-slate-300 border-slate-400/30',
        };
        $dotColor = match($category) {
            'success' => 'bg-emerald-400',
            'warning' => 'bg-amber-400',
            'danger' => 'bg-rose-400',
            default => 'bg-slate-400',
        };
    } else {
        $colorClasses = match($category) {
            'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200 shadow-2xs',
            'warning' => 'bg-amber-50 text-amber-800 border-amber-200 shadow-2xs',
            'danger' => 'bg-rose-50 text-rose-800 border-rose-200 shadow-2xs',
            default => 'bg-slate-100 text-slate-700 border-slate-200 shadow-2xs',
        };
        $dotColor = match($category) {
            'success' => 'bg-emerald-500',
            'warning' => 'bg-amber-500',
            'danger' => 'bg-rose-500',
            default => 'bg-slate-400',
        };
    }
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center font-semibold rounded-full border tracking-wide ' . $sizeClasses['pill'] . ' ' . $colorClasses]) }}>
    @if($isPulsing)
        <span class="{{ $sizeClasses['dot'] }} rounded-full {{ $dotColor }} animate-pulse shrink-0"></span>
    @endif

    @if($showIcon)
        @if($config['icon'] === 'check' || $config['icon'] === 'badge_check')
            <svg class="{{ $sizeClasses['icon'] }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.25">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
        @elseif($config['icon'] === 'shield_check')
            <svg class="{{ $sizeClasses['icon'] }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
        @elseif($config['icon'] === 'clock')
            <svg class="{{ $sizeClasses['icon'] }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        @elseif($config['icon'] === 'doc_search')
            <svg class="{{ $sizeClasses['icon'] }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
            </svg>
        @elseif($config['icon'] === 'banknotes')
            <svg class="{{ $sizeClasses['icon'] }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6H2.25m0 0v8.25m0 0a60.07 60.07 0 0015.797 2.101c.727.198 1.453-.342 1.453-1.096V6a.75.75 0 00-.75-.75h-.75m-15.75 0H21"/>
            </svg>
        @elseif($config['icon'] === 'refresh')
            <svg class="{{ $sizeClasses['icon'] }} shrink-0 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
            </svg>
        @elseif($config['icon'] === 'trending_up')
            <svg class="{{ $sizeClasses['icon'] }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
            </svg>
        @elseif($config['icon'] === 'airplane')
            <svg class="{{ $sizeClasses['icon'] }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
            </svg>
        @elseif($config['icon'] === 'sparkles')
            <svg class="{{ $sizeClasses['icon'] }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
        @elseif($config['icon'] === 'x_circle')
            <svg class="{{ $sizeClasses['icon'] }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        @else
            <svg class="{{ $sizeClasses['icon'] }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
        @endif
    @endif

    <span class="truncate">{{ $displayLabel }}</span>
</span>
