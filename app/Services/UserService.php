<?php

declare(strict_types=1);

namespace App\Services;

use App\Exports\UsersExport;
use App\Models\TelegramState;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

final class UserService
{
    public function getWorkers(): Collection|array
    {
        $users = User::query()->orderBy('name')->get(['id', 'name', 'position', 'phone', 'birthday', 'created_at']);
        return $users;
    }

    public function destroy(string $id): bool
    {
        $userDelete = User::destroy($id);

        if($userDelete) return true;
        return false;
    }

    public function getFiredWorkers(): Collection|array
    {
        $users = User::onlyTrashed()->orderBy('name')->get(['id', 'name', 'position', 'phone', 'birthday', 'deleted_at']);
        return $users;
    }

    public function restore(string $id): bool
    {
        $userRestore = User::withTrashed()->where('id', $id)->restore();

        if($userRestore) return true;
        return false;
    }

    public function store(): bool
    {
        $state = TelegramState::query()->orderBy('id', 'desc')->first();
        $data = $state->data;

        $user = User::query()->create([
            'name' => $data['name'],
            'position' => $data['position'],
            'phone' => $data['phone'],
            'birthday' => $data['birthday'],
        ]);
        if($user) return true;
        return false;
    }

    public function export(): bool
    {
        return Excel::store(new UsersExport, 'users.xls', 'public');
    }
}
