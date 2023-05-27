<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    use Exportable;

    public function headings(): array
    {
        return [
            '#',
            'ФИО',
            'Должность',
            'Телефон',
            'День рождения',
            'Принят на работу',
            'Уволен',
        ];
    }

    public function collection(): Collection|\Illuminate\Support\Collection
    {
        return User::withTrashed()->get([
            'id',
            'name',
            'position',
            'phone',
            'birthday',
            'created_at',
            'deleted_at',
        ]);
    }
}
