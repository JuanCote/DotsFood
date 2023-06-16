<?php

namespace App\Services\Telegram\Senders\CreateOrder;


use App\Services\Dots\DotsService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CheckOrderSender
{
    private $dotsService;

    public function __construct(
        DotsService $dotsService,
    ) {
        $this->dotsService = $dotsService;
    }
    public function handle(Message $message, array $order_info)
    {
        if (empty($order_info)){
            $text = "Something went wrong 😞";
        }else{
            $text = "The company name - {$order_info['companyName']}\n
Type of delivery - {$order_info['delivery']['deliveryTypeText']}\n
Address of the institution - {$order_info['delivery']['deliveryAddress']}\n
Payment type - {$order_info['payment']['title']}\n
Total price - {$order_info['prices']['fullPrice']}";
        }
        $keyboard = $this->generateKeyboard();
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateKeyboard(): Keyboard
    {
        $inlineKeyboard = [[
            ['text' => 'Back to menu', 'callback_data' => 'main_menu'],
        ]];
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
