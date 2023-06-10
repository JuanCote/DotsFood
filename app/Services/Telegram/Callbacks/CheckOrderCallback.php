<?php

namespace App\Services\Telegram\Callbacks;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CategorySender;
use App\Services\Telegram\Senders\CheckOrderSender;
use App\Services\Telegram\Senders\DishSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;


class CheckOrderCallback
{

    private $userService;
    private $orderService;
    private $dotsService;
    public function __construct(
        UsersService $userService,
        OrdersService $orderService,
        DotsService $dotsService,
    ) {
        $this->userService = $userService;
        $this->orderService = $orderService;
        $this->dotsService = $dotsService;
    }
    public function handle(CallbackQuery $callbackQuery)
    {
        $callbackData = $callbackQuery->getData();
        $order_id = $this->getOrderIdFromData($callbackData);
        $order_info = $this->dotsService->checkOrder($order_id);
        app(CheckOrderSender::class)->handle($callbackQuery->message, $order_info);
    }

    private function getOrderIdFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }

}
