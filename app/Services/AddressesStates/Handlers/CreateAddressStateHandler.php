<?php

namespace App\Services\AddressesStates\Handlers;

use App\Models\AddressState;
use App\Services\AddressesStates\Repositories\EloquentAddressStateRepository;

class CreateAddressStateHandler
{
    private $addressStateRepository;

    public function __construct(
        EloquentAddressStateRepository $addressStateRepository
    )
    {
        $this->addressStateRepository = $addressStateRepository;
    }

    public function handle(array $data): AddressState
    {
        return $this->addressStateRepository->createFromArray($data);
    }
}
