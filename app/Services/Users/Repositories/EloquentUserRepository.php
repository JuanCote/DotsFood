<?php

namespace App\Services\Users\Repositories;


use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EloquentUserRepository
{
    public function findByTelegramId(int $telegramUserId): ?User
    {
        return User::where('telegram_id', $telegramUserId)
            ->first();
    }

    public function createFromArray(array $data): User
    {
        return User::create($data);
    }

    public function updateFromArray(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }
}
