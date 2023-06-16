<?php


namespace App\Services\Telegram\Senders\Addresses;


use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class StreetAddressSender
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
        $this->userService = $usersService;
        $this->ordersService = $ordersService;
    }

    public function handle(Message $message)
    {
        $telegramId = $message->chat->id;

        $text = "Write your street";

        $keyboard = $this->generateKeyboard();

        Telegram::editMessageText([
            'chat_id' => $telegramId,
            'message_id' => $message->message_id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateKeyboard(): Keyboard
    {
        $inlineKeyboard = [];
        $inlineKeyboard[] = [['text' => 'Back to main menu', 'callback_data' => '/decline']];

        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
