<?php

namespace App\Services\Telegram\Callbacks\CreateOrder;

use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CreateOrder\CitySender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\CallbackQuery;


class CreateNewOrderCallback
{
    public function handle(CallbackQuery $callbackQuery)
    {
        app(CitySender::class)->handle($callbackQuery->message);
    }
}
