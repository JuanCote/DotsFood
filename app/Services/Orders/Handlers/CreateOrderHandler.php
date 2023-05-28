<?php

namespace App\Services\Orders\Handlers;


use App\Models\Order;
use App\Models\User;
use App\Services\Orders\Repositories\EloquentOrderRepository;

class CreateOrderHandler
{
    private $orderRepository;

    public function __construct(
        EloquentOrderRepository $orderRepository
    )
    {
        $this->orderRepository = $orderRepository;
    }

    public function handle(array $data): Order
    {
        return $this->orderRepository->createFromArray($data);
    }
}
