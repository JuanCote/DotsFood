<?php

namespace App\Services\Telegram\Senders;


use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class DeliveryTypesSender
{
    private $dotsService;

    public function __construct(
        DotsService $dotsService,
    ) {
        $this->dotsService = $dotsService;
    }

    public function handle(Message $message, User $user)
    {
        $delivery_types = $this->dotsService->getDeliveryTypes($user->order->company_id);
        $keyboard = $this->generateDeliveryTypesKeyboard($delivery_types);
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => "Оберіть тип доставки",
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateDeliveryTypesKeyboard($types): Keyboard
    {
        $inline_keyboard = [];
        foreach ($types['items'] as $type){
            $inline_keyboard[] = [
                'text' => $type['title'],
                'callback_data' => 'delivery_type_' . $type['type']
            ];
        }
        $inline_keyboard = array_chunk($inline_keyboard, 2);
        $inline_keyboard[] = [['text' => 'Скасувати', 'callback_data' => '/decline']];
        return $reply_markup = new Keyboard([
            'inline_keyboard' => $inline_keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
