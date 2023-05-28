<?php

namespace App\Services\Orders;


use App\Models\Order;
use App\Services\Orders\Handlers\CreateOrderHandler;
use App\Services\Orders\Handlers\UpdateOrderHandler;
use App\Services\Orders\Repositories\EloquentOrderRepository;
use Illuminate\Database\Eloquent\Collection;

class OrdersService
{
    private $createOrderHandler;
    private $updateOrderHandler;
    private $orderRepository;

    public function __construct(
        CreateOrderHandler      $createOrderHandler,
        UpdateOrderHandler      $updateOrderHandler,
        EloquentOrderRepository $orderRepository
    )
    {
        $this->createOrderHandler = $createOrderHandler;
        $this->updateOrderHandler = $updateOrderHandler;
        $this->orderRepository = $orderRepository;
    }

    public function findOrderByUserId(int $userId): ?Order
    {
        return $this->orderRepository->findByUserId($userId);
    }

    public function createOrder(array $data): Order
    {
        return $this->createOrderHandler->handle($data);
    }

    public function updateOrder(Order $order, array $data): Order
    {
        return $this->updateOrderHandler->handle($order, $data);
    }

}
