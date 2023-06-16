<?php

namespace App\Services\AddressesStates;


use App\Models\AddressState;
use App\Services\AddressesStates\Handlers\CreateAddressStateHandler;
use App\Services\AddressesStates\Handlers\UpdateAddressStateHandler;
use App\Services\AddressesStates\Repositories\EloquentAddressStateRepository;

class AddressStateService
{
    private $createAddressStateHandler;
    private $updateAddressStateHandler;
    private $addressStateRepository;

    public function __construct(
        CreateAddressStateHandler $createAddressStateHandler,
        UpdateAddressStateHandler $updateAddressStateHandler,
        EloquentAddressStateRepository $addressStateRepository
    )
    {
        $this->createAddressStateHandler = $createAddressStateHandler;
        $this->updateAddressStateHandler = $updateAddressStateHandler;
        $this->addressStateRepository = $addressStateRepository;
    }

    public function findAddressStateByUserId(int $userId): ?AddressState
    {
        return $this->addressStateRepository->findByUserId($userId);
    }

    public function createAddressState(array $data): AddressState
    {
        return $this->createAddressStateHandler->handle($data);
    }

    public function updateAddressState(AddressState $addressState, array $data): AddressState
    {
        return $this->updateAddressStateHandler->handle($addressState, $data);
    }

}
