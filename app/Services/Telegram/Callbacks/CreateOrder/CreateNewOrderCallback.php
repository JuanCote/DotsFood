<?php

namespace App\Services\Telegram\Callbacks\CreateOrder;

use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CreateOrder\CitySender;
use App\Services\Users\UsersService;
use Telegram\Bot\Objects\CallbackQuery;


class CreateNewOrderCallback
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
        app(CitySender::class)->handle($callbackQuery->message);
    }
}
