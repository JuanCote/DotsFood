<?php

namespace App\Services\Telegram\Senders\CreateOrder;

use App\Services\Dots\DotsService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class SuccessStoreOrderSender
{
    private $dotsService;

    public function __construct(
        DotsService $dotsService,
    ) {
        $this->dotsService = $dotsService;
    }
    public function handle(Message $message, array $orderResult)
    {
        if (!array_key_exists('id', $orderResult)){
            $checkOrder = false;
            $text = 'Something went wrong  😞';
            $text .= "\n{$orderResult['message']}";
        }else{
            $checkOrder = true;
            $text = 'Order successfully created 🥳';
        }
        $keyboard = $this->generateSuccessTypesKeyboard($orderResult, $checkOrder);
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateSuccessTypesKeyboard(array $orderResult, bool $checkOrder): Keyboard
    {
        $inlineKeyboard = [
            [
                ['text' => 'Back to menu', 'callback_data' => 'main_menu'],
            ],
        ];
        if ($checkOrder){
            $inlineKeyboard[][0] = ['text' => 'View order', 'callback_data' => 'check_order_' . $orderResult['id']];
        }
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
