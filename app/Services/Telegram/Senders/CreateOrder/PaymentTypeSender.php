<?php

namespace App\Services\Telegram\Senders\CreateOrder;

use App\Models\User;
use App\Services\Dots\DotsService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class PaymentTypeSender
{
    private $dotsService;

    public function __construct(
        DotsService $dotsService,
    ) {
        $this->dotsService = $dotsService;
    }
    public function handle(Message $message, User $user)
    {
        $keyboard = $this->generatePaymentTypesKeyboard();
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => "Select the payment type",
            'reply_markup' => $keyboard,
        ]);
    }

    private function generatePaymentTypesKeyboard(): Keyboard
    {
        $inlineKeyboard = [
            [
                ['text' => 'In cash', 'callback_data' => 'payment_' . 1],
                ['text' => 'Online', 'callback_data' => 'payment_' . 2],
                ['text' => 'Terminal', 'callback_data' => 'payment_' . 3],
            ],
            [
                ['text' => 'Decline', 'callback_data' => '/decline'],
            ]
        ];
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
