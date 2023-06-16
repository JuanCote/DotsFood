<?php

namespace App\Services\Telegram\Callbacks\CreateOrder;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
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
        $dishId = $this->getDishFromData($callbackData);
        $chatId = $callbackQuery->message->chat->id;
        $user = $this->userService->findUserByTelegramId($chatId);
        $this->addDishToOrder($dishId, $user);
        Telegram::answerCallbackQuery([
            'callback_query_id' => $callbackQuery->id,
            'text' => 'Added to cart',
            'show_alert' => true,
        ]);
    }

    private function getDishFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }

    private function addDishToOrder(string $dishId, User $user)
    {
        $items = $user->order->items;
        $items[] = [
            'id' => $dishId,
            'count' => 1
        ];

        $this->orderService->updateOrder($user->order, [
            'items' => $items
        ]);
    }

}
