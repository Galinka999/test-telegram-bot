<?php

declare(strict_types=1);

namespace App\Services;

use App\Exports\UsersExport;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

final class UserService
{
    public static function getWorkers(): Collection|array
    {
        return User::query()->orderBy('name')->get(['id', 'name', 'position', 'phone', 'birthday', 'created_at']);
    }

    public static function getFiredWorkers(): Collection|array
    {
        return User::onlyTrashed()->orderBy('name')->get(['id', 'name', 'position', 'phone', 'birthday', 'deleted_at']);
    }

    public static function destroy(string $id): bool
    {
        $userDelete = User::destroy($id);
        return isset($userDelete);
    }

    public static function restore(string $id): bool
    {
        $userRestore = User::withTrashed()->where('id', $id)->restore();
        return isset($userRestore);
    }

    public static function store(array $data): bool
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'position' => $data['position'],
            'phone' => $data['phone'],
            'birthday' => $data['birthday'],
        ]);
        return isset($user);
    }

    public static function export(): bool
    {
        return Excel::store(new UsersExport, 'users.xls', 'public');
    }
}
