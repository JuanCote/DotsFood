<?php

namespace App\Services\Telegram\Handlers;

use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\MainMenu\MainMenuSender;
use App\Services\Users\UsersService;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

// Class for getting a contact and adding a phone to the database
class ReceiveContactHandler
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
        $telegramId = $message->from->id;
        $user = $this->userService->findUserByTelegramId($telegramId);
        $this->addPhoneToUser($user, $message);
        if (!$user->order){
            $this->addOrderToUser($user);
        }
        if (!$user->dotsUserId){
            $this->addDotsIdToUser($user, $message);
        }
        app(MainMenuSender::class)->handle($message, false);
    }

    private function addPhoneToUser(User $user, Message $message)
    {
        $telegramId = $message->from->id;
        $data = [
            'name' => $message->contact->first_name,
            // Phone number is required without a plus sign
            'phone' => substr($message->contact->phone_number, 1),
            'telegram_id' => $telegramId
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
    private function addDotsIdToUser(User $user, Message $message)
    {
        // Phone number is required without a plus sign
        $userStat = $this->dotsService->userStatByPhone(substr($message->contact->phoneNumber, 1));
        $this->userService->updateUser($user, [
           'dotsUserId' => $userStat["user"]["id"]
        ]);
    }
}
