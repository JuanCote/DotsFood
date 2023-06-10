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
            'text' => 'Оберіть категорію',
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateCategoriesKeyboard(array $categories): Keyboard
    {
        $inline_keyboard = [];
        foreach ($categories['items'] as $category){
            $inline_keyboard[] = [
                'text' => $category['name'],
                'callback_data' => 'category_' . $category['id']
            ];
        }
        $inline_keyboard = array_chunk($inline_keyboard, 2);
        $inline_keyboard[] = [
            ['text' => 'Скасувати', 'callback_data' => '/decline'],
            ['text' => 'Замовити', 'callback_data' => 'delivery']
        ];
        return $reply_markup = new Keyboard([
            'inline_keyboard' => $inline_keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
