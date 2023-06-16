<?php


namespace App\Services\Telegram\Senders\Addresses;


use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class AddAddressSender
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

    public function handle(Message $message, bool $hasAddress)
    {
        $keyboard = $this->generateKeyboard($hasAddress);
        $telegramId = $message->chat->id;

        if ($hasAddress){
            $text = "You have already connected your address to the system, but you can update it ✅";
        }else{
            $text = "Connect your address to the system for convenient delivery ❓";
        }

        Telegram::editMessageText([
            'chat_id' => $telegramId,
            'message_id' => $message->message_id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateKeyboard(bool $hasAddress): Keyboard
    {
        $inlineKeyboard = [];
        $inlineKeyboard[] = [['text' => 'Back to main menu', 'callback_data' => '/decline']];
        if ($hasAddress){
            $inlineKeyboard[] = [['text' => 'Update address', 'callback_data' => 'add_address_start']];
        }else{
            $inlineKeyboard[] = [['text' => 'Add address', 'callback_data' => 'add_address_start']];
        }

        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
