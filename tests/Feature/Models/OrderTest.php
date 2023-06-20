<?php

namespace Tests\Feature\Models;

use App\Models\Order;
use App\Models\User;
use App\Services\Orders\Repositories\EloquentOrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function testFindByUserId()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
        ]);

        $orderRepository = new EloquentOrderRepository();

        $foundOrder = $orderRepository->findByUserId($user->id);

        $this->assertEquals($order->id, $foundOrder->id);
        $this->assertEquals($order->user_id, $foundOrder->user_id);
    }
    public function testCreateFromArray()
    {
        $user = User::factory()->create();

        $orderRepository = new EloquentOrderRepository();

        $data = [
            'user_id' => $user->id,
            'userName' => 'Viktor',
            'userPhone' => '380821223235'
        ];

        $order = $orderRepository->createFromArray($data);

        Log::info($order);

        $this->assertDatabaseHas('orders', ['id' => $order->id]);

        $this->assertEquals($data['user_id'], $order->user_id);
        $this->assertEquals($data['userName'], $order->userName);
    }

    public function testUpdateFromArray()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $orderRepository = new EloquentOrderRepository();

        $newData = [
            'userName' => 'Viktor',
        ];

        $updatedOrder = $orderRepository->updateFromArray($order, $newData);

        $this->assertDatabaseHas('orders', [
            'id' => $updatedOrder->id,
            'userName' => $newData['userName'],
        ]);

        $this->assertEquals($newData['userName'], $updatedOrder->userName);
    }
}
