<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramBotApiContract;
use Illuminate\Console\Command;

class SetWebhookTelegramBotCommand extends Command
{

    protected $signature = 'telegram-bot:set-webhook';

    protected $description = 'Install webhook for telegram bot';

    public function handle(TelegramBotApiContract $service): void
    {
        $url = config('services.telegram.webhook');

        $response = $service->setWebhook($url);

        if(!$response) {
            $this->alert('Вебхук не установлен. Попробуйте еще раз.');
            return;
        }

        $this->alert('Вебхук успешно установлен.');
    }
}
