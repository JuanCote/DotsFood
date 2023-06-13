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

class MainMenuSender
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
    public function handle(Message $message, bool $edit)
    {
        $telegramId = $message->chat->id;
        $this->clearOrder($telegramId);
        $keyboard = $this->generateMainKeyboard();
        $text = "Hi, with the help of this bot you can order food all over Ukraine 🚚";
        if ($edit){
            Telegram::editMessageText([
                'chat_id' => $telegramId,
                'message_id' => $message->message_id,
                'text' => $text,
                'reply_markup' => $keyboard,
            ]);
        }else{
            Telegram::sendMessage([
                'chat_id' => $telegramId,
                'text' => $text,
                'reply_markup' => $keyboard,
            ]);
        }
    }
    private function generateMainKeyboard(): Keyboard
    {
        $inlineKeyboard = [
            [['text' => '➕ Create new order', 'callback_data' => 'create_order']],
            [['text' => '👀 View active orders', 'callback_data' => 'active_orders']],
            [['text' => '📜 Order history', 'callback_data' => 'history_orders']],
        ];
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
    // Clear user order object
    private function clearOrder(int $telegramId)
    {
        $user = $this->usersService->findUserByTelegramId($telegramId);
        $this->ordersService->updateOrder($user->order, [
            'city_id' => null,
            'company_id' => null,
            'items' => null,
            'delivery_type' => null,
            'payment_type' => null,
            'company_address' => null
        ]);
    }
}
