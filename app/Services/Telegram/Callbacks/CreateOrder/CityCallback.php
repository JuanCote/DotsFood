<?php

namespace App\Services\Telegram\Callbacks\CreateOrder;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CreateOrder\CompanySender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\CallbackQuery;


class CityCallback
{
    public function __construct(
        UsersService $userService,
        OrdersService $orderService,
    ) {
        $this->userService = $userService;
        $this->orderService = $orderService;
    }
    public function handle(CallbackQuery $callbackQuery)
    {
        $callbackData = $callbackQuery->getData();
        $chatId = $callbackQuery->message->chat->id;
        $cityId = $this->getCityIdFromData($callbackData);
        $user = $this->userService->findUserByTelegramId($chatId);
        // Change or add cityId to user`s order
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
