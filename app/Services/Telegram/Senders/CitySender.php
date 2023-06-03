<?php


namespace App\Services\Telegram\Senders;


use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Users\UsersService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

// Class for sending a list of cities to the user
class CitySender
{
    private $dotsService;
    private $userService;
    private $ordersService;

    public function __construct(
        OrdersService $ordersService,
        DotsService $dotsService,
        UsersService $usersService,
    ) {
        $this->dotsService = $dotsService;
        $this->usersService = $usersService;
        $this->ordersService = $ordersService;
    }

    public function handle(Message $message, bool $edit)
    {
        $telegram_id = $message->chat->id;

        $this->clear_order($telegram_id);

        $keyboard = $this->generateCitiesKeyboard();
        if ($edit){
            Telegram::editMessageText([
                'chat_id' => $telegram_id,
                'message_id' => $message->message_id,
                'text' => "Оберіть місто у якому бажаєте створити замовлення",
                'reply_markup' => $keyboard,
            ]);
        }else{
            Telegram::sendMessage([
                'chat_id' => $telegram_id,
                'text' => "Оберіть місто у якому бажаєте створити замовлення",
                'reply_markup' => $keyboard,
            ]);
        }
    }

    private function generateCitiesKeyboard(): Keyboard
    {
        $inline_keyboard = [];
        $cities = $this->dotsService->getCities();
        foreach ($cities['items'] as $city){
            $inline_keyboard[] = [
                'text' => $city['name'],
                'callback_data' => 'city_' . $city['id']
            ];
        }
        $inline_keyboard = array_chunk($inline_keyboard, 2);
        return $reply_markup = new Keyboard([
            'inline_keyboard' => $inline_keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

    }

    private function clear_order(int $telegram_id)
    {
        $user = $this->usersService->findUserByTelegramId($telegram_id);
        $this->ordersService->updateOrder($user->order, [
            'city_id' => null,
            'company_id' => null,
            'items' => null,
            'delivery_type' => null,
            'payment_type' => null,
        ]);
    }

}
