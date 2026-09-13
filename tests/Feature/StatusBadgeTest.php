<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class StatusBadgeTest extends TestCase
{
    public function test_status_badge_renders_success_category(): void
    {
        $html = Blade::render('<x-status-badge status="lunas" />');
        $this->assertStringContainsString('Lunas', $html);
        $this->assertStringContainsString('bg-emerald-50', $html);
        $this->assertStringContainsString('text-emerald-800', $html);

        $html2 = Blade::render('<x-status-badge status="aktif" />');
        $this->assertStringContainsString('Aktif', $html2);
        $this->assertStringContainsString('bg-emerald-50', $html2);
    }

    public function test_status_badge_renders_warning_category(): void
    {
        $html = Blade::render('<x-status-badge status="menunggu_pembayaran_dp" />');
        $this->assertStringContainsString('Menunggu Pembayaran DP', $html);
        $this->assertStringContainsString('bg-amber-50', $html);
        $this->assertStringContainsString('text-amber-800', $html);
        $this->assertStringContainsString('animate-pulse', $html);
    }

    public function test_status_badge_renders_danger_category(): void
    {
        $html = Blade::render('<x-status-badge status="ditolak" />');
        $this->assertStringContainsString('Ditolak', $html);
        $this->assertStringContainsString('bg-rose-50', $html);
        $this->assertStringContainsString('text-rose-800', $html);
    }

    public function test_status_badge_renders_neutral_category(): void
    {
        $html = Blade::render('<x-status-badge status="draft" />');
        $this->assertStringContainsString('Draft', $html);
        $this->assertStringContainsString('bg-slate-100', $html);
        $this->assertStringContainsString('text-slate-700', $html);
    }

    public function test_status_badge_supports_custom_label_and_dark_theme(): void
    {
        $html = Blade::render('<x-status-badge status="lunas" label="Telah Lunas Penuh" theme="dark" size="lg" />');
        $this->assertStringContainsString('Telah Lunas Penuh', $html);
        $this->assertStringContainsString('bg-emerald-500/20', $html);
        $this->assertStringContainsString('text-emerald-200', $html);
        $this->assertStringContainsString('text-sm', $html);
    }
}
