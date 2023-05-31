<?php

namespace App\Services\Telegram\Handlers\Commands;


use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Telegram\Callbacks\CityCallback;
use App\Services\Telegram\Senders\CitySender;
use App\Services\Users\UsersService;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;


class StartCommandHandler
{

    private $dotsService;
    private $userService;

    public function __construct(
        DotsService   $dotsService,
        UsersService $userService,
    ) {
        $this->dotsService = $dotsService;
        $this->userService = $userService;
    }

    public function handle(Command $command)
    {
        $message = $command->getUpdate()->message;
        // Checking if the user has a phone number in db
        $check_phone = $this->check_phone($message);
        if(!$check_phone){
            $inline_keyboard = [
                [['text' => 'Поділитись контактом', 'request_contact' => true, 'callback_data' => 'receive_contact']],
            ];
            $reply_markup = new Keyboard([
                'keyboard' => $inline_keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ]);
            Telegram::sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'Будь ласка, поділіться своїм номером телефону',
                'reply_markup' => $reply_markup
            ]);
        }else{
            app(CitySender::class)->handle($message, false);
        }
    }

    private function check_phone(Message $message) : bool
    {
        $telegram_id = $message->chat->id;
        $user = $this->userService->findUserByTelegramId($telegram_id);
        // Checking for the existence of a user
        if (!$user){
            $data = [
                'name' => $message->chat->first_name,
                'telegram_id' => $telegram_id
            ];
            $this->userService->createUser($data);
            return false;
        }else{
            if ($user->phone !== null){
                return true;
            }else{
                return false;
            }
        }
    }
}
