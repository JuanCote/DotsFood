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
        Telegram::answerCallbackQuery([
            'callback_query_id' => $callbackQuery->id,
            'text' => 'Додано у корзину',
            'show_alert' => true,
        ]);
    }

    private function getDishFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }

    private function addDishToOrder(string $dish_id, User $user)
    {
        $items = $user->order->items;
        $items[] = [
            'id' => $dish_id,
            'count' => 1
        ];

        $this->orderService->updateOrder($user->order, [
            'items' => $items
        ]);
    }

}
