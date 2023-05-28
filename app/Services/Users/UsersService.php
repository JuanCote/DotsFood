<?php

namespace App\Services\Users;


use App\Models\User;
use App\Services\Users\Handlers\CreateUserHandler;
use App\Services\Users\Handlers\UpdateUserHandler;
use App\Services\Users\Repositories\EloquentUserRepository;
use Illuminate\Database\Eloquent\Collection;

class UsersService
{
    private $createUserHandler;
    private $updateUserHandler;
    private $userRepository;

    public function __construct(
        CreateUserHandler $createUserHandler,
        UpdateUserHandler $updateUserHandler,
        EloquentUserRepository $userRepository
    )
    {
        $this->createUserHandler = $createUserHandler;
        $this->updateUserHandler = $updateUserHandler;
        $this->userRepository = $userRepository;
    }

    public function findUserByTelegramId(int $telegramUserId): ?User
    {
        return $this->userRepository->findByTelegramId($telegramUserId);
    }

    public function createUser(array $data): User
    {
        return $this->createUserHandler->handle($data);
    }

    public function updateUser(User $user, array $data): User
    {
        return $this->updateUserHandler->handle($user, $data);
    }

}
