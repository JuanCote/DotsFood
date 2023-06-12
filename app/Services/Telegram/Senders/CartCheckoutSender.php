<?php


namespace App\Services\Telegram\Senders;


use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CartCheckoutSender
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
        $user = $this->userService->findUserByTelegramId($message->chat->id);
        $resolveCartResult = $this->dotsService->resolveCart($user);
        $text = "Residual price:\n
Order price - {$resolveCartResult['price']}\n
Delivery price - {$resolveCartResult['deliveryPrice']}\n
Total - {$resolveCartResult['totalPrice']}\n";
        $keyboard = $this->generateCheckoutKeyboard();
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateCheckoutKeyboard(): Keyboard
    {
        $inlineKeyboard = [];
        $inlineKeyboard[] = [
            ['text' => 'Decline', 'callback_data' => '/decline'],
            ['text' => 'Order', 'callback_data' => 'order_agree']
        ];
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
