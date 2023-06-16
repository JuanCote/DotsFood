<?php

namespace App\Services\Telegram\Senders\CreateOrder;


use App\Models\User;
use App\Services\Dots\DotsService;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Message;

class CompanySender
{
    private $dotsService;

    public function __construct(
        DotsService $dotsService,
    ) {
        $this->dotsService = $dotsService;
    }

    public function handle(Message $message, User $user, string $cityId)
    {
        $companies = $this->dotsService->getCompanies($cityId);
        $keyboard = $this->generateCompaniesKeyboard($companies);
        Telegram::editMessageText([
            'chat_id' => $message->chat->id,
            'message_id' => $message->message_id,
            'text' => "Choose the company in which you want to create an order",
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateCompaniesKeyboard($companies): Keyboard
    {
        $inlineKeyboard = [];
        foreach ($companies['items'] as $company){
            $inlineKeyboard[] = [
                'text' => $company['name'],
                'callback_data' => 'company_' . $company['id']
            ];
        }
        $inlineKeyboard = array_chunk($inlineKeyboard, 2);
        $inlineKeyboard[] = [['text' => 'Decline', 'callback_data' => '/decline']];
        return new Keyboard([
            'inline_keyboard' => $inlineKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
