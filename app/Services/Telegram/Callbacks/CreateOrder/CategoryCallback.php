<?php

namespace App\Services\Telegram\Callbacks\CreateOrder;

use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CreateOrder\DishSender;
use App\Services\Users\UsersService;
use Telegram\Bot\Objects\CallbackQuery;


class CategoryCallback
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
