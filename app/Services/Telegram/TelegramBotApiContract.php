<?php

declare(strict_types=1);

namespace App\Services\Telegram;

interface TelegramBotApiContract
{
    public function sendMessage(string $text): bool;
    public function setWebhook(string $url);
    public function sendDocument(string $fileName);
}
