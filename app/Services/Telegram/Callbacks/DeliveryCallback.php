<?php

namespace App\Services\Telegram\Callbacks;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CategorySender;
use App\Services\Telegram\Senders\DeliveryTypesSender;
use App\Services\Telegram\Senders\DishSender;
use App\Services\Users\UsersService;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\CallbackQuery;
use function PHPUnit\Framework\isEmpty;


class DeliveryCallback
{

    private $userService;
    private $orderService;
    private $dotsService;
    public function __construct(
        UsersService $userService,
        OrdersService $orderService,
        DotsService $dotsService,
    ) {
        $this->userService = $userService;
        $this->orderService = $orderService;
        $this->dotsService = $dotsService;
    }
    public function handle(CallbackQuery $callbackQuery)
    {

        $message = $callbackQuery->message;
        $user = $this->userService->findUserByTelegramId($message->chat->id);
        if ($this->checkForEmptinessItems($user)){
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQuery->id,
                'text' => 'Неможливо створювати порожнє замовлення',
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
