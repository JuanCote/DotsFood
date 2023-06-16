<?php

namespace App\Services\AddressesStates\Repositories;



use App\Models\AddressState;

class EloquentAddressStateRepository
{
    public function findByUserId(int $userId): ?AddressState
    {
        return AddressState::where('user_id', $userId)
            ->first();
    }

    public function createFromArray(array $data): AddressState
    {
        return AddressState::create($data);
    }

    public function updateFromArray(AddressState $addressState, array $data): AddressState
    {
        $addressState->update($data);
        return $addressState;
    }
}
