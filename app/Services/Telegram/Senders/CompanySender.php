<?php

namespace App\Services\Telegram\Senders;


use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
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
            'text' => "Оберіть компанію в якій бажаєте створити замовлення",
            'reply_markup' => $keyboard,
        ]);
    }

    private function generateCompaniesKeyboard($companies): Keyboard
    {
        $inline_keyboard = [];
        foreach ($companies['items'] as $company){
            $inline_keyboard[] = [
                'text' => $company['name'],
                'callback_data' => 'company_' . $company['id']
            ];
        }
        $inline_keyboard = array_chunk($inline_keyboard, 2);
        $inline_keyboard[] = [['text' => 'Скасувати', 'callback_data' => '/decline']];
        return $reply_markup = new Keyboard([
            'inline_keyboard' => $inline_keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }
}
