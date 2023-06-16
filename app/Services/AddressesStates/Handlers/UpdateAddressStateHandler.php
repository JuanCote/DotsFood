<?php


namespace App\Services\AddressesStates\Handlers;


use App\Models\AddressState;
use App\Services\AddressesStates\Repositories\EloquentAddressStateRepository;

class UpdateAddressStateHandler
{
    private $addressStateRepository;

    public function __construct(
        EloquentAddressStateRepository $addressStateRepository
    )
    {
        $this->addressStateRepository = $addressStateRepository;
    }

    public function handle(AddressState $addressState, array $data): AddressState
    {
        return $this->addressStateRepository->updateFromArray($addressState, $data);
    }
}
