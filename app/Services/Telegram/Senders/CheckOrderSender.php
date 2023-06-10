<?php

namespace App\Services\Telegram\Senders;


use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use Illuminate\Support\Facades\Log;
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
        Log::info($order_info);
        $text = "Назва компанії - {$order_info['companyName']}\n
Тип доставки - {$order_info['delivery']['deliveryTypeText']}\n
Адреса закладу - {$order_info['delivery']['deliveryAddress']}\n
Тип оплати - {$order_info['payment']['title']}\n
Загальна ціна - {$order_info['prices']['fullPrice']}";
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
        $inline_keyboard = [[
            ['text' => 'Створити нове замовлення', 'callback_data' => 'create_order'],
        ]];
        return $reply_markup = new Keyboard([
            'inline_keyboard' => $inline_keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
