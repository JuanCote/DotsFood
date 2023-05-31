<?php

namespace App\Services\Telegram\Callbacks;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CategorySender;
use App\Services\Telegram\Senders\DishSender;
use App\Services\Users\UsersService;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;


class DishCallback
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
        $dish_id = $this->getDishFromData($callbackData);
        $chat_id = $callbackQuery->message->chat->id;
        $user = $this->userService->findUserByTelegramId($chat_id);
        $this->addDishToOrder($dish_id, $user);
    }

    private function getDishFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }

    private function addDishToOrder($dish_id, $user)
    {
        $items = $user->order->items;
        $items[] = $dish_id;

        $this->orderService->updateOrder($user->order, [
            'items' => $items
        ]);
    }

}
