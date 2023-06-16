<?php

namespace App\Services\Telegram\Callbacks\CreateOrder;

use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CreateOrder\CheckOrderSender;
use App\Services\Users\UsersService;
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
        $orderId = $this->getOrderIdFromData($callbackData);
        $orderInfo = $this->dotsService->checkOrder($orderId);
        app(CheckOrderSender::class)->handle($callbackQuery->message, $orderInfo);
    }

    private function getOrderIdFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }

}
