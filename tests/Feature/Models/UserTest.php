<?php

namespace Tests\Feature\Models;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Services\Users\Repositories\EloquentUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    public function testFindByTelegramId()
    {
        $user = User::factory()->create([
            'telegram_id' => 123456789,
        ]);

        $userRepository = new EloquentUserRepository();

        $foundUser = $userRepository->findByTelegramId(123456789);

        $this->assertEquals($user->id, $foundUser->id);
        $this->assertEquals($user->telegram_id, $foundUser->telegram_id);
    }
    public function testCreateFromArray()
    {

        $userRepository = new EloquentUserRepository();

        $data = [
            'name' => 'John Doe',
            'telegram_id' => 987654321,
        ];

        $user = $userRepository->createFromArray($data);

        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($data['name'], $user->name);
        $this->assertEquals($data['telegram_id'], $user->telegram_id);
    }
    public function testUpdateFromArray()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'telegram_id' => 123456789,
        ]);

        $userRepository = new EloquentUserRepository();

        $data = [
            'name' => 'Jane Smith',
            'telegram_id' => 987654321,
        ];

        $updatedUser = $userRepository->updateFromArray($user, $data);

        $this->assertEquals($user->id, $updatedUser->id);
        $this->assertEquals($data['name'], $updatedUser->name);
        $this->assertEquals($data['telegram_id'], $updatedUser->telegram_id);
    }
}
