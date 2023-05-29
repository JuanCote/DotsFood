<?php

namespace App\Services\Telegram\Handlers\Commands;


use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Telegram\Callbacks\CityCallback;
use App\Services\Telegram\Senders\CitySender;
use App\Services\Users\UsersService;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
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
        $message = $command->getUpdate()->getMessage();

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
            $command->replyWithMessage(['text' => 'Будь ласка, поділіться своїм номером телефону', 'reply_markup' => $reply_markup]);
        }else{
            app(CitySender::class)->handle($message);
        }
    }

    private function check_phone(Message $message) : bool
    {
        $telegram_id = $message->from->id;
        $user = $this->userService->findUserByTelegramId($telegram_id);
        // Checking for the existence of a user
        if (!$user){
            $data = [
                'name' => $message->from->first_name,
                'phone' => $message->from->null,
                'telegram_id' => $telegram_id
            ];
            $user = $this->userService->createUser($data);
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
