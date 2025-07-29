<?php

namespace App\Providers;

use App\Services\ChatbotServices;
use Illuminate\Support\ServiceProvider;
use App\Services\AuthServices;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthServices::class, function ($app) {
        return new AuthServices();
        });
        $this->app->singleton(ChatbotServices::class, function ($app) {
        return new ChatbotServices();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
