<?php

namespace App\Services\Telegram\Callbacks\MainMenu;

use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\MainMenu\ActiveOrdersSender;
use App\Services\Users\UsersService;
use Telegram\Bot\Objects\CallbackQuery;


class ActiveOrdersCallback
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
        $telegramId = $callbackQuery->message->chat->id;
        $user = $this->userService->findUserByTelegramId($telegramId);
        $activeOrders = $this->dotsService->userActiveOrders($user->dotsUserId);
        app(ActiveOrdersSender::class)->handle($callbackQuery->message, $activeOrders);
    }
}
