<?php


namespace App\Services\Telegram\Senders;


use App\Services\Dots\DotsService;
use App\Services\Users\UsersService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CitySender
{
    private $dotsService;

    public function __construct(
        DotsService $dotsService,
    ) {
        $this->dotsService = $dotsService;
    }

    public function handle(Message $message)
    {
        $telegram_id = $message->from->id;
        $keyboard = $this->getCitiesKeyboard();
        Telegram::sendMessage([
            'chat_id' => $telegram_id,
            'text' => "Оберіть місто у якому бажаєте створити замовлення",
            'reply_markup' => $keyboard,
        ]);
    }

    private function getCitiesKeyboard(): Keyboard
    {
        $inline_keyboard = [];
        $cities = $this->dotsService->getCities();
        foreach ($cities['items'] as $city){
            $inline_keyboard[] = [
                'text' => $city['name'],
                'callback_data' => 'city_' . $city['id']
            ];
        }
        $inline_keyboard = array_chunk($inline_keyboard, 4);
        return $reply_markup = new Keyboard([
            'inline_keyboard' => $inline_keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

    }

}
