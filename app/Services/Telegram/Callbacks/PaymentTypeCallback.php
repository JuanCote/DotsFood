<?php

namespace App\Services\Telegram\Callbacks;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CategorySender;
use App\Services\Telegram\Senders\DishSender;
use App\Services\Telegram\Senders\SuccessStoreOrderSender;
use App\Services\Users\UsersService;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;
use function Webmozart\Assert\Tests\StaticAnalysis\integer;


class PaymentTypeCallback
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
        (int)$payment_type = $this->getPaymentTypeFromData($callbackData);
        $chat_id = $callbackQuery->message->chat->id;
        $user = $this->userService->findUserByTelegramId($chat_id);
        $this->addPaymentTypeToOrder($payment_type, $user);
        $orderResult = $this->createOrder($chat_id);
        app(SuccessStoreOrderSender::class)->handle($callbackQuery->message, $orderResult);
    }

    private function getPaymentTypeFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }

    private function addPaymentTypeToOrder(int $payment_type, User $user)
    {
        $this->orderService->updateOrder($user->order, [
            'payment_type' => $payment_type
        ]);
    }
    private function createOrder(int $telegramId): array
    {
        $user = $this->userService->findUserByTelegramId($telegramId);
        return $this->dotsService->createOrder($user);
    }
}
