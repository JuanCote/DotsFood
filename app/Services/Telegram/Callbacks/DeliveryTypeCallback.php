<?php

namespace App\Services\Telegram\Callbacks;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CategorySender;
use App\Services\Telegram\Senders\DeliveryTypesSender;
use App\Services\Telegram\Senders\DishSender;
use App\Services\Telegram\Senders\PaymentTypeSender;
use App\Services\Users\UsersService;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;


class DeliveryTypeCallback
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
        (int)$delivery_type = $this->getDeliveryTypeFromData($callbackData);
        $user = $this->userService->findUserByTelegramId($callbackQuery->message->chat->id);
        $this->addDeliveryTypeToOrder($delivery_type, $user);
        app(PaymentTypeSender::class)->handle($callbackQuery->message, $user);
    }

    private function getDeliveryTypeFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }
    private function addDeliveryTypeToOrder(int $delivery_type, User $user)
    {
        $this->orderService->updateOrder($user->order, [
            'delivery_type' => $delivery_type
        ]);
    }
}
