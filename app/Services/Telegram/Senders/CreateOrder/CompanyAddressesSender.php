<?php


namespace App\Services\Telegram\Senders\CreateOrder;


use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Users\UsersService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CompanyAddressesSender
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

    public function handle(Message $message, User $user)
    {
        $telegramId = $message->chat->id;
        $companyId = $user->order->company_id;
        $keyboard = $this->generateCompanyAddresses($companyId);
        Telegram::editMessageText([
            'chat_id' => $telegramId,
            'message_id' => $message->message_id,
            'text' => "Select the address of the company where you want to receive the order",
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateCompanyAddresses($companyId): Keyboard
    {
        $inlineKeyboard = [];
        $addresses = $this->dotsService->getCompanyAddresses($companyId);
        foreach ($addresses as $address){
            $inlineKeyboard[] = [
                'text' => $address['title'],
                'callback_data' => 'companyAddress_' . $address['id']
            ];
        }
        $inlineKeyboard = array_chunk($inlineKeyboard, 2);
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
