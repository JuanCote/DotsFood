<?php

namespace App\Services\Telegram\Senders;


use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use Illuminate\Support\Facades\Log;
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
        $deliveryTypes = $this->dotsService->getDeliveryTypes($user->order->company_id);
        $keyboard = $this->generateDeliveryTypesKeyboard($deliveryTypes);
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => "Choose the type of delivery",
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateDeliveryTypesKeyboard($types): Keyboard
    {
        $inlineKeyboard = [];
        foreach ($types['items'] as $type){
            $inlineKeyboard[] = [
                'text' => $type['title'],
                'callback_data' => 'delivery_type_' . $type['type']
            ];
        }
        $inlineKeyboard = array_chunk($inlineKeyboard, 2);
        $inlineKeyboard[] = [['text' => 'Decline', 'callback_data' => '/decline']];
        return $reply_markup = new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
