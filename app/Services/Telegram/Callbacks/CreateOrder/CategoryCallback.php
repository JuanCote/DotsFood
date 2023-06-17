<?php

namespace App\Services\Telegram\Callbacks\CreateOrder;

use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CreateOrder\DishSender;
use App\Services\Users\UsersService;
use Telegram\Bot\Objects\CallbackQuery;


class CategoryCallback
{
    public function __construct(
        UsersService $userService,
    ) {
        $this->userService = $userService;
    }
    public function handle(CallbackQuery $callbackQuery)
    {
        $callbackData = $callbackQuery->getData();
        $categoryId = $this->getCategoryFromData($callbackData);
        $chatId = $callbackQuery->message->chat->id;
        $user = $this->userService->findUserByTelegramId($chatId);

        app(DishSender::class)->handle($callbackQuery->message, $categoryId, $user);
    }

    private function getCategoryFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }
}
