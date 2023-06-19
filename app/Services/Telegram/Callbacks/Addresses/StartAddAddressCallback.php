<?php

namespace App\Services\Telegram\Callbacks\Addresses;

use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\Addresses\AddAddressSender;
use App\Services\Telegram\Senders\Addresses\CityAddressSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\CallbackQuery;
use function Symfony\Component\Translation\t;


class StartAddAddressCallback
{
    public function handle(CallbackQuery $callbackQuery)
    {
        app(CityAddressSender::class)->handle($callbackQuery->message);
    }
}
