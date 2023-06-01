<?php

namespace App\Services\Telegram\Senders;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
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
// Message $message, User $user, string $cityId
    public function handle(Message $message, User $user)
    {
        $keyboard = $this->generatePaymentTypesKeyboard();
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => "Виберіть тип оплати",
            'reply_markup' => $keyboard,
        ]);
    }

    private function generatePaymentTypesKeyboard(): Keyboard
    {
        $inline_keyboard = [
            [
                ['text' => 'Готівкою', 'callback_data' => 'payment_' . 1],
                ['text' => 'Онлайн', 'callback_data' => 'payment_' . 2],
                ['text' => 'Термінал', 'callback_data' => 'payment_' . 3],
            ],
            [
                ['text' => 'Скасувати', 'callback_data' => '/decline'],
            ]
        ];
        return $reply_markup = new Keyboard([
            'inline_keyboard' => $inline_keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
