<?php


namespace App\Services\Telegram\Senders\Addresses;


use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\MainMenu\MainMenuSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class SuccessAddressSender
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

    public function handle(Message $message, bool $success)
    {
        $telegramId = $message->chat->id;

        if ($success) {
            $text = "The address has been successfully added";
        }else{
            $text = "Something is wrong with the address";
        }
        Telegram::sendMessage([
            'chat_id' => $telegramId,
            'text' => $text,
        ]);
        app(MainMenuSender::class)->handle($message, false);
    }
}
