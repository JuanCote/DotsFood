<?php

namespace App\Services\Telegram\Handlers;

use App\Models\User;
use App\Services\Dots\DotsService;
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

    public function __construct(
        DotsService $dotsService,
        UsersService $userService,
    ) {
        $this->dotsService = $dotsService;
        $this->userService = $userService;
    }

    public function handle(Update $update)
    {
        $message = $update->getMessage();
        $telegram_id = $message->from->id;
        $user = $this->userService->findUserByTelegramId($telegram_id);
        $this->addPhoneToUser($user, $message);
        app(CitySender::class)->handle($message);
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

}
