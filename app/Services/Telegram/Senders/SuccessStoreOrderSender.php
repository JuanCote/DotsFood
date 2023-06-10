<?php

namespace App\Services\Telegram\Senders;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;
use function Symfony\Component\Translation\t;

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
        Log::info($orderResult);
        if (array_key_exists('title', $orderResult) and $orderResult['title'] === 'Oops...'){
            $check_order = false;
            $text = 'Вибрана компанія не працює  😞';
        }else{
            $check_order = true;
            $text = 'Замовлення успішно створене 🥳';
        }
        $keyboard = $this->generateSuccessTypesKeyboard($orderResult, $check_order);
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateSuccessTypesKeyboard(array $orderResult, bool $check_order): Keyboard
    {
        $inline_keyboard = [
            [
                ['text' => 'Створити нове замовлення', 'callback_data' => 'create_order'],
            ],
        ];
        if ($check_order){
            $inline_keyboard[][0] = ['text' => 'Переглянути замовлення', 'callback_data' => 'check_order_' . $orderResult['id']];
        }
        return $reply_markup = new Keyboard([
            'inline_keyboard' => $inline_keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
