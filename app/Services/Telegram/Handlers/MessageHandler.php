<?php

namespace App\Services\Telegram\Handlers;

use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Handlers\Messages\FlatHandler;
use App\Services\Telegram\Handlers\Messages\HouseHandler;
use App\Services\Telegram\Handlers\Messages\StreetHandler;
use App\Services\Users\UsersService;
use Telegram\Bot\Objects\Update;

// Сlass intercepting all messages written by the user in the chat
class MessageHandler
{
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
        $state = $user->addressState->state;
        if ($user) {
            if ($state === 'street') {
                app(StreetHandler::class)->handle($update, $user);
            }elseif ($state === 'house') {
                app(HouseHandler::class)->handle($update, $user);
            }elseif ($state === 'flat') {
                app(FlatHandler::class)->handle($update, $user);
            }
        }
    }
}
