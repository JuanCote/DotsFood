<?php

namespace App\Services\Telegram\Callbacks;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CartCheckoutSender;
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
        (int)$paymentType = $this->getPaymentTypeFromData($callbackData);
        $chatId = $callbackQuery->message->chat->id;
        $user = $this->userService->findUserByTelegramId($chatId);
        $this->addPaymentTypeToOrder($paymentType, $user);
        app(CartCheckoutSender::class)->handle($callbackQuery->message);
    }

    private function getPaymentTypeFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }

    private function addPaymentTypeToOrder(int $paymentType, User $user)
    {
        $this->orderService->updateOrder($user->order, [
            'payment_type' => $paymentType
        ]);
    }
}
