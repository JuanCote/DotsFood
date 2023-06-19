<?php

namespace App\Services\Telegram\Callbacks\Addresses;

use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\Addresses\AddAddressSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\CallbackQuery;
use function Symfony\Component\Translation\t;


class AddAddressCallback
{
    public function __construct(
        UsersService $userService,
    ) {
        $this->userService = $userService;
    }
    public function handle(CallbackQuery $callbackQuery)
    {
        $telegramId = $callbackQuery->message->chat->id;
        $hasAddress = $this->checkAddress($telegramId);
        app(AddAddressSender::class)->handle($callbackQuery->message, $hasAddress);
    }
    private function checkAddress($telegramId): bool
    {
        $user = $this->userService->findUserByTelegramId($telegramId);
        if ($user->address){
            return true;
        }
        return false;
    }
}
