<?php

namespace App\Services\Telegram\Callbacks;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\ActiveOrdersSender;
use App\Services\Telegram\Senders\CategorySender;
use App\Services\Telegram\Senders\DishSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;


class AddAddressCallback
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
        $telegramId = $callbackQuery->message->chat->id;
        $user = $this->userService->findUserByTelegramId($telegramId);
        $activeOrders = $this->dotsService->userActiveOrders($user->dotsUserId);
        app(ActiveOrdersSender::class)->handle($callbackQuery->message, $activeOrders);
    }
}
