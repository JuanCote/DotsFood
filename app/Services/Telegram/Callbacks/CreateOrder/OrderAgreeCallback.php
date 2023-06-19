<?php

namespace App\Services\Telegram\Callbacks\CreateOrder;

use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CreateOrder\SuccessStoreOrderSender;
use App\Services\Users\UsersService;
use Telegram\Bot\Objects\CallbackQuery;


class OrderAgreeCallback
{
    public function __construct(
        UsersService $userService,
        DotsService $dotsService,
    ) {
        $this->userService = $userService;
        $this->dotsService = $dotsService;
    }
    public function handle(CallbackQuery $callbackQuery)
    {
        $message = $callbackQuery->message;
        $telegramId = $message->chat->id;
        $orderResult = $this->createOrder($telegramId);
        app(SuccessStoreOrderSender::class)->handle($message, $orderResult);
    }
    private function createOrder(int $telegramId): array
    {
        $user = $this->userService->findUserByTelegramId($telegramId);
        return $this->dotsService->createOrder($user);
    }
}
