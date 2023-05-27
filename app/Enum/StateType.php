<?php

declare(strict_types=1);

namespace App\Enum;

enum StateType: string
{
    case NAME = 'name';
    case PHONE = 'phone';
    case POSITION = 'position';
    case BIRTHDAY = 'birthday';
}
