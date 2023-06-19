<?php


namespace App\Services\Telegram\Senders\Addresses;


use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CityAddressSender
{
    public function __construct(
        DotsService $dotsService,
    ) {
        $this->dotsService = $dotsService;
    }

    public function handle(Message $message)
    {
        $telegramId = $message->chat->id;

        $text = "Select your city";

        $cities = $this->dotsService->getCities();
        $keyboard = $this->generateKeyboard($cities);

        Telegram::editMessageText([
            'chat_id' => $telegramId,
            'message_id' => $message->message_id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateKeyboard(array $cities): Keyboard
    {
        $inlineKeyboard = [];

        foreach ($cities['items'] as $city) {
            $inlineKeyboard[] = [
                'text' => $city['name'],
                'callback_data' => 'address_city_' . $city['id']
            ];
        }
        $inlineKeyboard = array_chunk($inlineKeyboard, 2);
        $inlineKeyboard[] = [['text' => 'Back to main menu', 'callback_data' => '/decline']];

        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
