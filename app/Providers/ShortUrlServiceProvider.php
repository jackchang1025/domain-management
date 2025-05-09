<?php

namespace App\Providers;

use App\Services\ShortUrlService;
use Illuminate\Support\ServiceProvider;

class ShortUrlServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(ShortUrlService::class, function ($app) {

            $account = config('short-url.username');
            $password = config('short-url.password');
            $cookieFile = config('short-url.cookie_file', storage_path("app/public/short-url-{$account}.json"));

            if (!$account || !$password) {
                throw new \Exception('Short URL account or password is not set');
            }

            return new ShortUrlService(
                account: $account,
                password: $password,
                cookieFile: $cookieFile
            );
        });
    }
} 