<?php

namespace App\Services\Telegram\Callbacks\MainMenu;

use App\Services\AddressesStates\AddressStateService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\MainMenu\MainMenuSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\CallbackQuery;


class MainMenuCallback
{
    public function __construct(
        UsersService $userService,
        OrdersService $orderService,
        AddressStateService $addressStateService
    ) {
        $this->userService = $userService;
        $this->orderService = $orderService;
        $this->addressStateService = $addressStateService;
    }
    public function handle(CallbackQuery $callbackQuery)
    {
        $message = $callbackQuery->message;
        app(MainMenuSender::class)->handle($message, true);
    }
}
