<?php

namespace Tests\Feature\Models;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testModelCreation()
    {
        $user = User::factory()->state([
            'name' => 'Nikita',
            'phone' => '380731112924',
            'telegram_id' => 192830192,
            'dotsUserId' => 'qasdq_1221'
        ])->create();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
