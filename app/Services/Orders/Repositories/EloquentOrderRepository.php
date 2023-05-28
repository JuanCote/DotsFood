<?php

namespace App\Services\Orders\Repositories;


use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EloquentOrderRepository
{
    public function findByUserId(int $userId): ?Order
    {
        return Order::where('user_id', $userId)
            ->first();
    }

    public function createFromArray(array $data): Order
    {
        return Order::create($data);
    }

    public function updateFromArray(Order $order, array $data): Order
    {
        $order->update($data);
        return $order;
    }
}
