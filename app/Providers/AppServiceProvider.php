<?php

namespace App\Providers;

use App\Services\Telegram\TelegramBotApi;
use App\Services\Telegram\TelegramBotApiContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TelegramBotApi::class, function () {
            return new TelegramBotApi(
                config('services.telegram.token'),
                config('services.telegram.chat_id'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(TelegramBotApiContract::class, TelegramBotApi::class);
    }
}
