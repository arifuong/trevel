<?php

namespace App\Providers;

use App\Contracts\WhatsAppNotificationInterface;
use App\Services\DummyWhatsAppNotification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');

        $this->app->bind(WhatsAppNotificationInterface::class, DummyWhatsAppNotification::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }
    }
}
