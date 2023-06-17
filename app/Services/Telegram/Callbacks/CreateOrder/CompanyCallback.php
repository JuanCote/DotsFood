<?php

namespace App\Services\Telegram\Callbacks\CreateOrder;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CreateOrder\CategorySender;
use App\Services\Users\UsersService;
use Telegram\Bot\Objects\CallbackQuery;


class CompanyCallback
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
