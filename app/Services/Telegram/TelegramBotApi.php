<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;
use App\Services\Telegram\Exceptions\TelegramBotApiException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TelegramBotApi implements TelegramBotApiContract
{
    protected string $token;
    protected int $chatId;
    public const HOST= 'https://api.telegram.org/bot';

    public function __construct(string $token, int $chatId)
    {
        $this->token = $token;
        $this->chatId = $chatId;
    }

    public static function fake()
    {
        return app()->instance(TelegramBotApiContract::class, TelegramBotApiFake::class);
    }

    public function sendMessage(string $text, string $buttons = null): bool
    {
        try {
            $data = [
                'chat_id' => $this->chatId,
                'text' => $text,
                'parse_mode' => 'html'
            ];

            if($buttons) {
                $markup = [
                    'reply_markup' => $buttons,
                ];
                $data = array_merge($data, $markup);
            }
            $response = Http::post(self::HOST . $this->token . '/sendMessage', $data)->throw()->json();

            return $response['ok'] ?? false;

        } catch (Throwable $e) {
            report(new TelegramBotApiException($e->getMessage()));
            return false;
        }
    }

    public function sendDocument(string $fileName)
    {
        try {
            return Http::attach('document', Storage::get("/public/$fileName"), 'workers.xls')
                ->post(self::HOST . $this->token . '/sendDocument', ['chat_id' => $this->chatId])
                ->json();

        } catch (Throwable $e) {
            report(new TelegramBotApiException($e->getMessage()));
            return false;
        }
    }

    public function setWebhook(string $url)
    {
        try {
            $response = Http::get(self::HOST . $this->token . '/setwebhook', [
                'url' => $url,
            ])->throw()->json();

            return $response['ok'] ?? false;

        } catch (Throwable $e) {
            report(new TelegramBotApiException($e->getMessage()));
            return false;
        }
    }
}
