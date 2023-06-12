<?php

namespace App\Services\Telegram\Senders;


use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CategorySender
{
    private $dotsService;

    public function __construct(
        DotsService $dotsService,
    ) {
        $this->dotsService = $dotsService;
    }
    public function handle(Message $message, string $companyId)
    {
        $categories = $this->dotsService->getDishes($companyId);
        $keyboard = $this->generateCategoriesKeyboard($categories);
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => 'Choose a category',
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateCategoriesKeyboard(array $categories): Keyboard
    {
        $inlineKeyboard = [];
        foreach ($categories['items'] as $category){
            $inlineKeyboard[] = [
                'text' => $category['name'],
                'callback_data' => 'category_' . $category['id']
            ];
        }
        $inlineKeyboard = array_chunk($inlineKeyboard, 2);
        $inlineKeyboard[] = [
            ['text' => 'Decline', 'callback_data' => '/decline'],
            ['text' => 'Order', 'callback_data' => 'delivery']
        ];
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
