<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\StateType;
use Carbon\Carbon;

final class TelegramStateService
{
    public static function saveName(string $name, $state): void
    {
        $array['name'] = $name;
        $state->data = $array;
        $state->state = StateType::POSITION;
        $state->save();
    }

    public static function savePosition(string $position, $state): void
    {
        $array = $state->data;
        $array['position'] = $position;
        $state->data = $array;
        $state->state = StateType::PHONE;
        $state->save();
    }

    public static function savePhone(string $phone, $state): void
    {
        $array = $state->data;
        $array['phone'] = $phone;
        $state->data = $array;
        $state->state = StateType::BIRTHDAY;
        $state->save();
    }

    public static function saveBirthday(string $birthday, $state): void
    {
        $array = $state->data;
        $array['birthday'] = Carbon::create($birthday)->format('d.m.Y');
        $state->data = $array;
        $state->save();
    }
}
