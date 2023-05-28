<?php

namespace App\Services\Telegram\Callbacks;

use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;


class CityCallback
{
    public function handle(CallbackQuery $callbackQuery)
    {
        $callbackData = $callbackQuery->getData();
        $message_id = $callbackQuery->message->message_id;
        $chat_id = $callbackQuery->message->chat->id;
        $city_id = $this->getCityIdFromData($callbackData);



        Telegram::editMessageText([
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => 'letsgo'
        ]);

    }

    private function getCityIdFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }
}
