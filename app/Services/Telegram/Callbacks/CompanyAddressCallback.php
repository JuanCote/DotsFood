<?php

namespace App\Services\Telegram\Callbacks;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CategorySender;
use App\Services\Telegram\Senders\DishSender;
use App\Services\Telegram\Senders\PaymentTypeSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;


class CompanyAddressCallback
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
        $companyAddress = $this->getCompanyAddressFromData($callbackData);
        $chatId = $callbackQuery->message->chat->id;
        $user = $this->userService->findUserByTelegramId($chatId);
        $this->addCompanyAddressToOrder($companyAddress, $user);
        app(PaymentTypeSender::class)->handle($callbackQuery->message, $user);
    }

    private function getCompanyAddressFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }
    private function addCompanyAddressToOrder(string $companyAddress_id, User $user)
    {
        $this->orderService->updateOrder($user->order, [
            'company_address' => $companyAddress_id
        ]);
    }
}
