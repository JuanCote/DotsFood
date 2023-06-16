<?php


namespace App\Services\Telegram\Senders\MainMenu;


use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Users\UsersService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class HistoryOrdersSender
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

    public function handle(Message $message, array $historyOrders)
    {
        $keyboard = $this->generateKeyboard();
        $telegramId = $message->chat->id;
        if (empty($historyOrders["items"])){
            $text = "You don't have any orders in your history🤷‍♂️";
        }else{
            $text = "Your orders history:\n------------------------------\n";
            foreach ($historyOrders["items"] as $order){
                $creationTime = date('Y-m-d H:i:s', $order['createTime']);
                $text .= "The company name - {$order['companyName']}\n";
                $text .= "Creation time - $creationTime\n";
                $text .= "------------------------------\n";
            }
        }
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
        $inlineKeyboard[] = [
            ['text' => 'Back to main menu', 'callback_data' => '/decline'],
        ];
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
