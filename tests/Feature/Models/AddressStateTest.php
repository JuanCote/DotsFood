<?php

namespace Tests\Feature\Models;

use App\Models\AddressState;
use App\Models\User;
use App\Services\AddressesStates\Repositories\EloquentAddressStateRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressStateTest extends TestCase
{
    use RefreshDatabase;

    public function testFindByUserId()
    {
        $user = User::factory()->create();
        $addressState = AddressState::factory()->create([
            'user_id' => $user->id,
        ]);

        $addressStateRepository = new EloquentAddressStateRepository();
        $foundAddressState = $addressStateRepository->findByUserId($user->id);

        $this->assertEquals($addressState->id, $foundAddressState->id);
        $this->assertEquals($addressState->user_id, $foundAddressState->user_id);
    }

    public function testCreateFromArray()
    {
        $user = User::factory()->create();
        $addressStateRepository = new EloquentAddressStateRepository();
        $data = [
            'user_id' => $user->id,
            'state' => 'street',
        ];

        $addressState = $addressStateRepository->createFromArray($data);

        $this->assertDatabaseHas('addresses_states', ['id' => $addressState->id]);
        $this->assertEquals($data['user_id'], $addressState->user_id);
        $this->assertEquals($data['state'], $addressState->state);
    }

    public function testUpdateFromArray()
    {
        $user = User::factory()->create();
        $addressState = AddressState::factory()->create(['user_id' => $user->id]);
        $addressStateRepository = new EloquentAddressStateRepository();
        $newData = [
            'state' => 'flat',
        ];

        $updatedAddressState = $addressStateRepository->updateFromArray($addressState, $newData);

        $this->assertDatabaseHas('addresses_states', [
            'id' => $updatedAddressState->id,
            'state' => $newData['state'],
        ]);

        $this->assertEquals($newData['state'], $updatedAddressState->state);
    }
}
