<?php

namespace App\Services\Telegram\Handlers;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\CitySender;
use App\Services\Users\UsersService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

// Class for getting a contact and adding a phone to the database
class ReceiveContact
{

    private $dotsService;
    private $userService;
    private $orderService;
    public function __construct(
        DotsService   $dotsService,
        UsersService $userService,
        OrdersService $orderService
    ) {
        $this->dotsService = $dotsService;
        $this->userService = $userService;
        $this->orderService = $orderService;
    }

    public function handle(Update $update)
    {
        $message = $update->getMessage();
        $telegram_id = $message->from->id;
        $user = $this->userService->findUserByTelegramId($telegram_id);
        $this->addPhoneToUser($user, $message);

        if (!$user->order){
            $this->addOrderToUser($user);
        }

        app(CitySender::class)->handle($message, false);
    }

    private function addPhoneToUser(User $user, Message $message)
    {
        $telegram_id = $message->from->id;
        $data = [
            'name' => $message->contact->first_name,
            'phone' => $message->contact->phone_number,
            'telegram_id' => $telegram_id
        ];
        $user = $this->userService->updateUser($user, $data);
    }

    private function addOrderToUser(User $user)
    {
        $this->orderService->createOrder([
            'user_id' => $user->id,
            'userName' => $user->name,
            'userPhone' => $user->phone
        ]);
    }

}
