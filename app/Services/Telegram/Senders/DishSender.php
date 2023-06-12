<?php

namespace App\Services\Telegram\Senders;

use App\Models\User;
use App\Services\Dots\DotsService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class DishSender
{
    private $dotsService;

    public function __construct(
        DotsService $dotsService,
    ) {
        $this->dotsService = $dotsService;
    }
    public function handle(Message $message, string $categoryId, User $user)
    {
        $companyId = $user->order->company_id;
        $categories = $this->dotsService->getDishes($companyId);
        $keyboard = $this->generateDishesKeyboard($categories, $categoryId, $companyId);
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => "Add dishes to the cart by clicking on them",
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateDishesKeyboard(array $categories, string $categoryId, string $companyId): Keyboard
    {
        $inlineKeyboard = [];
        foreach ($categories['items'] as $category){
            if ($category['id'] === $categoryId){
                foreach ($category['items'] as $dish) {
                    $inlineKeyboard[] = [
                        'text' => $dish['name'],
                        'callback_data' => 'dish_' . $dish['id']
                    ];
                }
            }
        }
        $inlineKeyboard = array_chunk($inlineKeyboard, 2);
        $inlineKeyboard[] = [
            ['text' => 'Decline', 'callback_data' => '/decline'],
            ['text' => 'Categories', 'callback_data' => 'company_' . $companyId],
            ['text' => 'Order', 'callback_data' => 'delivery']
        ];
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
