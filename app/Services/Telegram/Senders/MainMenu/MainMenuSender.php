<?php


namespace App\Services\Telegram\Senders\MainMenu;


use App\Services\AddressesStates\AddressStateService;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Users\UsersService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class MainMenuSender
{
    private $dotsService;
    private $userService;
    private $ordersService;

    public function __construct(
        OrdersService $orderService,
        DotsService $dotsService,
        UsersService $userService,
        AddressStateService $addressStateService
    ) {
        $this->dotsService = $dotsService;
        $this->userService = $userService;
        $this->orderService = $orderService;
        $this->addressStateService = $addressStateService;
    }
    public function handle(Message $message, bool $edit)
    {
        $telegramId = $message->chat->id;
        $this->clearOrderAndAddressState($telegramId);
        $keyboard = $this->generateMainKeyboard();
        $text = "Hi, with the help of this bot you can order food all over Ukraine 🚚";
        if ($edit){
            Telegram::editMessageText([
                'chat_id' => $telegramId,
                'message_id' => $message->message_id,
                'text' => $text,
                'reply_markup' => $keyboard,
            ]);
        }else{
            Telegram::sendMessage([
                'chat_id' => $telegramId,
                'text' => $text,
                'reply_markup' => $keyboard,
            ]);
        }
    }
    private function generateMainKeyboard(): Keyboard
    {
        $inlineKeyboard = [
            [['text' => '➕ Create new order', 'callback_data' => 'create_order']],
            [['text' => '👀 View active orders', 'callback_data' => 'active_orders']],
            [['text' => '📜 Order history', 'callback_data' => 'history_orders']],
            [['text' => '🏠 Add address', 'callback_data' => 'add_address']],
        ];
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
    // Clear user order and AddressState object
    private function clearOrderAndAddressState(int $telegramId)
    {
        $user = $this->userService->findUserByTelegramId($telegramId);
        $this->orderService->updateOrder($user->order, [
            'city_id' => null,
            'company_id' => null,
            'items' => null,
            'delivery_type' => null,
            'payment_type' => null,
            'company_address' => null
        ]);
        $this->addressStateService->updateAddressState($user->addressState, [
            'state' => null,
            'city_id' => null,
            'type' => null,
            'street' => null,
            'house' => null,
            'flat' => null,
            'stage' => null,
            'note' => null,
            'title' => null
        ]);
    }
}
