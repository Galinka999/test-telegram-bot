<?php

use App\Http\Controllers\Api\TelegramCallbackController;
use Illuminate\Support\Facades\Route;


Route::post('/tgBot/callback', [TelegramCallbackController::class, 'callback']);
