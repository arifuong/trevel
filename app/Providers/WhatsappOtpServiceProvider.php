<?php

namespace App\Providers;

use App\Services\WhatsappOtpInterface;
use App\Services\DummyWhatsappOtp;
use Illuminate\Support\ServiceProvider;

class WhatsappOtpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WhatsappOtpInterface::class, DummyWhatsappOtp::class);
    }

    public function boot(): void
    {
        //
    }
}
