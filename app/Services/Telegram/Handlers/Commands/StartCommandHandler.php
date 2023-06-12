<?php

namespace App\Services\Telegram\Handlers\Commands;


use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Telegram\Callbacks\CityCallback;
use App\Services\Telegram\Senders\CitySender;
use App\Services\Telegram\Senders\MainMenuSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
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
        $text = "Hello, {$message->from->first_name}";
        // Checking if the user has a phone number in db
        $checkPhone = $this->checkPhone($message);
        if(!$checkPhone){
            $inlineKeyboard = [
                [['text' => 'Share a contact', 'request_contact' => true]],
            ];
            $text .= "\nYour contact information is required to work with the bot";
            $replyMarkup = new Keyboard([
                'keyboard' => $inlineKeyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ]);
            Telegram::sendMessage([
                'chat_id' => $message->chat->id,
                'text' => $text,
                'reply_markup' => $replyMarkup
            ]);
        }else{
            app(MainMenuSender::class)->handle($message, false);
        }
    }

    private function checkPhone(Message $message) : bool
    {
        $telegramId = $message->chat->id;
        $user = $this->userService->findUserByTelegramId($telegramId);
        // Checking for the existence of a user
        if (!$user){
            $data = [
                'name' => $message->chat->first_name,
                'telegram_id' => $telegramId
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
