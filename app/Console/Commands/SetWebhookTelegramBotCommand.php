<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramBotApiContract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SetWebhookTelegramBotCommand extends Command
{

    protected $signature = 'telegram-bot:set-webhook';

    protected $description = 'Install webhook for telegram bot';

    public function handle(TelegramBotApiContract $service): void
    {
        try {
            $url = config('services.telegram.webhook');

            $service->setWebhook($url);
            $this->alert('Вебхук успешно установлен.');

            return;
        } catch (\Throwable $e) {
            Log::debug($e->getMessage());
            $this->alert('Вебхук не установлен. Попробуйте еще раз.');
        }

    }
}
