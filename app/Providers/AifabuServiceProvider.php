<?php

namespace App\Providers;

use App\Services\AifabuService;
use Illuminate\Support\ServiceProvider;

class AifabuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AifabuService::class, function ($app) {

            return new AifabuService(
                token: config('aifabu.token'),
            );
        });
    }
} 