<?php

namespace App\Services\Telegram\Callbacks;



use App\Services\Telegram\Senders\CitySender;
use App\Services\Telegram\Senders\MainMenuSender;
use Telegram\Bot\Objects\CallbackQuery;

class DeclineCallback
{
    public function handle(CallbackQuery $callbackQuery)
    {

        app(MainMenuSender::class)->handle($callbackQuery->message,true);
    }
}
