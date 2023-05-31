<?php

namespace App\Services\Telegram\Callbacks;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CompanySender;
use App\Services\Users\UsersService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;


class CityCallback
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
        $chat_id = $callbackQuery->message->chat->id;
        $cityId = $this->getCityIdFromData($callbackData);
        $user = $this->userService->findUserByTelegramId($chat_id);
        // Change or add city_id to user`s order
        $this->addCityToOrder($cityId, $user);
        app(CompanySender::class)->handle($callbackQuery->message, $user, $cityId);


    }

    private function getCityIdFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }

    private function addCityToOrder(string $cityId, User $user)
    {
        $this->orderService->updateOrder($user->order, [
            'user_id' => $user->id,
            'city_id' => $cityId,
            'userName' => $user->name,
            'userPhone' => $user->phone
        ]);
    }
}
