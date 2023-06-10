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
        $company_address = $this->getCompanyAddressFromData($callbackData);
        $chat_id = $callbackQuery->message->chat->id;
        $user = $this->userService->findUserByTelegramId($chat_id);
        $this->addCompanyAddressToOrder($company_address, $user);
        app(PaymentTypeSender::class)->handle($callbackQuery->message, $user);
    }

    private function getCompanyAddressFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }
    private function addCompanyAddressToOrder(string $company_address_id, User $user)
    {
        $this->orderService->updateOrder($user->order, [
            'company_address' => $company_address_id
        ]);
    }
}
