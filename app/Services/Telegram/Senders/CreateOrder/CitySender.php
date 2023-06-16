<?php


namespace App\Services\Telegram\Senders\CreateOrder;


use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Users\UsersService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

// Class for sending a list of cities to the user
class CitySender
{
    private $dotsService;
    private $userService;
    private $ordersService;

    public function __construct(
        OrdersService $ordersService,
        DotsService $dotsService,
        UsersService $usersService,
    ) {
        $this->dotsService = $dotsService;
        $this->usersService = $usersService;
        $this->ordersService = $ordersService;
    }

    public function handle(Message $message)
    {
        $telegramId = $message->chat->id;
        $keyboard = $this->generateCitiesKeyboard();
        $text = "Choose the city in which you want to create an order";
        Telegram::editMessageText([
            'chat_id' => $telegramId,
            'message_id' => $message->message_id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateCitiesKeyboard(): Keyboard
    {
        $inlineKeyboard = [];
        $cities = $this->dotsService->getCities();
        foreach ($cities['items'] as $city) {
            $inlineKeyboard[] = [
                'text' => $city['name'],
                'callback_data' => 'city_' . $city['id']
            ];
        }
        $inlineKeyboard = array_chunk($inlineKeyboard, 2);
        $inlineKeyboard[] = [['text' => 'Decline', 'callback_data' => '/decline']];
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

    }

}
