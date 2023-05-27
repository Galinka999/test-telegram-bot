<?php

use App\Http\Controllers\Api\TelegramCallbackController;
use Illuminate\Support\Facades\Route;


Route::get('/users', [TelegramCallbackController::class, 'index']);

Route::post('/tgBot/callback', [TelegramCallbackController::class, 'callback']);
