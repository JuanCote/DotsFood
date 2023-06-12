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


class CompanyCallback
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
        $companyId = $this->getCompanyIdFromData($callbackData);
        $chatId = $callbackQuery->message->chat->id;
        $user = $this->userService->findUserByTelegramId($chatId);
        $this->addCompanyToOrder($companyId, $user);
        app(CategorySender::class)->handle($callbackQuery->message, $companyId);
    }

    private function getCompanyIdFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }
    private function addCompanyToOrder(string $companyId, User $user)
    {
        $this->orderService->updateOrder($user->order, [
            'company_id' => $companyId,
        ]);
    }
}
