<?php

namespace App\Services\Telegram\Callbacks\Addresses;

use App\Services\AddressesStates\AddressStateService;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\Addresses\AddAddressSender;
use App\Services\Telegram\Senders\Addresses\CityAddressSender;
use App\Services\Telegram\Senders\Addresses\TypeAddressSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\CallbackQuery;
use function Symfony\Component\Translation\t;


class CityAddressCallback
{
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
        $cityId = $this->getCityIdFromData($callbackData);
        $telegramId = $callbackQuery->message->chat->id;
        $this->addCityToAddressState($cityId, $telegramId);
        app(TypeAddressSender::class)->handle($callbackQuery->message);
    }
    private function getCityIdFromData(string $callbackData): string
    {
        $array = explode('_', $callbackData);
        return end($array);
    }
    private function addCityToAddressState(string $cityId, int $telegramId)
    {
        $user = $this->userService->findUserByTelegramId($telegramId);
        $this->addressStateService->updateAddressState($user->addressState, [
            'city_id' => $cityId
        ]);
    }
}
