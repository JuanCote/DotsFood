<?php

namespace App\Services\Telegram\Callbacks;



use App\Services\Telegram\Senders\CitySender;
use Telegram\Bot\Objects\CallbackQuery;

class DeclineCallback
{
    public function handle(CallbackQuery $callbackQuery)
    {

        app(CitySender::class)->handle($callbackQuery->message,true);
    }
}
