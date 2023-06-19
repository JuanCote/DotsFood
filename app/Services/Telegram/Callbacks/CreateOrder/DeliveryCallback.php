<?php

namespace App\Services\Telegram\Callbacks\CreateOrder;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CreateOrder\DeliveryTypesSender;
use App\Services\Users\UsersService;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;


class DeliveryCallback
{
    public function __construct(
        UsersService $userService,

    ) {
        $this->userService = $userService;
    }
    public function handle(CallbackQuery $callbackQuery)
    {

        $message = $callbackQuery->message;
        $user = $this->userService->findUserByTelegramId($message->chat->id);
        if ($this->checkForEmptinessItems($user)){
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQuery->id,
                'text' => 'An empty order cannot be created',
                'show_alert' => true,
            ]);
        }else{
            app(DeliveryTypesSender::class)->handle($message, $user);
        }
    }

    private function checkForEmptinessItems(User $user): bool
    {
        if (!$user->order->items){
            return true;
        }else{
            return false;
        }
    }
}
