<?php


namespace App\Services\Users\Handlers;


use App\Models\User;
use App\Services\Users\Repositories\EloquentUserRepository;

class UpdateUserHandler
{

    private $userRepository;

    public function __construct(
        EloquentUserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function handle(User $user, array $data): User
    {
        return $this->userRepository->updateFromArray($user, $data);
    }
}
