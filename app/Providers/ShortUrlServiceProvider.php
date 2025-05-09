<?php

namespace App\Providers;

use App\Services\ShortUrlService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ShortUrlServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(ShortUrlService::class, function ($app) {

            $account = config('short-url.username');

            return new ShortUrlService(
                account: config('short-url.username'),
                password: config('short-url.password'),
                cookieFile: config('short-url.cookie_file', storage_path("app/public/short-url-{$account}.json"))
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [ShortUrlService::class];
    }
} 