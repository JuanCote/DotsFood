<?php

namespace App\Services\Telegram\Callbacks\Addresses;

use App\Services\AddressesStates\AddressStateService;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\Addresses\AddAddressSender;
use App\Services\Telegram\Senders\Addresses\CityAddressSender;
use App\Services\Telegram\Senders\Addresses\StreetAddressSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\CallbackQuery;
use function Symfony\Component\Translation\t;


class TypeAddressCallback
{

    private $userService;

    public function __construct(
        UsersService $userService,
        AddressStateService $addressStateService
    ) {
        $this->userService = $userService;
        $this->addressStateService = $addressStateService;
    }
    public function handle(CallbackQuery $callbackQuery)
    {
        $callbackData = $callbackQuery->getData();
        (int)$type = $this->getTypeIdFromData($callbackData);
        $telegramId = $callbackQuery->message->chat->id;
        $user = $this->userService->findUserByTelegramId($telegramId);
        $this->addTypeAddressToAddressState($type, $telegramId);
        app(StreetAddressSender::class)->handle($callbackQuery->message);
    }
    private function getTypeIdFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }
    // Add the selected type and update the state to intercept the street after the next message
    private function addTypeAddressToAddressState(int $type, int $telegramId)
    {
        $user = $this->userService->findUserByTelegramId($telegramId);
        $this->addressStateService->updateAddressState($user->addressState, [
            'type' => $type,
            'state' => 'street'
        ]);
    }
}
