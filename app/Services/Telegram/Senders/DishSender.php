<?php

namespace App\Services\Telegram\Senders;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
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
// Message $message, User $user, string $cityId
    public function handle(Message $message, string $category_id, User $user)
    {
        $company_id = $user->order->company_id;
        $categories = $this->dotsService->getDishes($company_id);
        $keyboard = $this->generateDishesKeyboard($categories, $category_id, $company_id);
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => "Додавайте страви до корзини натискаючи на них",
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateDishesKeyboard(array $categories, string $category_id, string $company_id): Keyboard
    {
        $inline_keyboard = [];
        foreach ($categories['items'] as $category){
            if ($category['id'] === $category_id){
                foreach ($category['items'] as $dish) {
                    $inline_keyboard[] = [
                        'text' => $dish['name'],
                        'callback_data' => 'dish_' . $dish['id']
                    ];
                }
            }
        }
        $inline_keyboard = array_chunk($inline_keyboard, 2);
        $inline_keyboard[] = [
            ['text' => 'Скасувати', 'callback_data' => '/decline'],
            ['text' => 'Категорії', 'callback_data' => 'company_' . $company_id],
            ['text' => 'Замовити', 'callback_data' => 'delivery']
        ];
        return $reply_markup = new Keyboard([
            'inline_keyboard' => $inline_keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
