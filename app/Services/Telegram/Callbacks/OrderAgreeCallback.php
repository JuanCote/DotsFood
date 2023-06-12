<?php

namespace App\Services\Telegram\Callbacks;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CategorySender;
use App\Services\Telegram\Senders\DishSender;
use App\Services\Telegram\Senders\SuccessStoreOrderSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;


class OrderAgreeCallback
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
