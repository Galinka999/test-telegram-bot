<?php

namespace App\Models;

use App\Enum\StateType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramState extends Model
{
    use HasFactory;

    protected $fillable = [
        'state',
        'is_active',
        'data',
    ];

    protected $casts = [
        'state' => StateType::class,
        'data' => 'array',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
