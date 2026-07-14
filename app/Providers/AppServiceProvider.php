<?php

namespace App\Providers;

use App\Mail\Transport\GmailApiTransport;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Mail::extend('gmail-api', function (array $config = []) {
            return new GmailApiTransport(
                http: app(HttpFactory::class),
                clientId: (string) ($config['client_id'] ?? ''),
                clientSecret: (string) ($config['client_secret'] ?? ''),
                refreshToken: (string) ($config['refresh_token'] ?? ''),
                timeout: (int) ($config['timeout'] ?? 20),
            );
        });
    }
}
