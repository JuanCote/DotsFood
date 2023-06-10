<?php


namespace App\Services\Telegram\Senders;


use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

// Class for sending a list of cities to the user
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
        $telegram_id = $message->chat->id;
        $company_id = $user->order->company_id;
        $keyboard = $this->generateCompanyAddresses($company_id);
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => "Виберіть адресу компанії в якому хочете отримати замовлення",
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateCompanyAddresses($company_id): Keyboard
    {
        $inline_keyboard = [];
        $addresses = $this->dotsService->getCompanyAddresses($company_id);
        foreach ($addresses as $address){
            $inline_keyboard[] = [
                'text' => $address['title'],
                'callback_data' => 'companyAddress_' . $address['id']
            ];
        }
        $inline_keyboard = array_chunk($inline_keyboard, 2);
        return $reply_markup = new Keyboard([
            'inline_keyboard' => $inline_keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
