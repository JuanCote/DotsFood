<?php

namespace App\Services\Telegram\Handlers\Messages;

use App\Models\User;
use App\Services\AddressesStates\AddressStateService;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\Addresses\FlatAddressSender;
use App\Services\Telegram\Senders\Addresses\HouseAddressSender;
use App\Services\Telegram\Senders\MainMenu\MainMenuSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

// Сlass for processing a message with a house.
class HouseHandler
{
    public function __construct(
        DotsService   $dotsService,
        UsersService $userService,
        OrdersService $orderService,
        AddressStateService $addressStateService
    ) {
        $this->dotsService = $dotsService;
        $this->userService = $userService;
        $this->orderService = $orderService;
        $this->addressStateService = $addressStateService;
    }

    public function handle(Update $update, User $user)
    {
        $house = $update->getMessage()->text;
        $type = $user->addressState->type;
        // A check to determine whether further inquiry for the house number is needed.
        if (in_array($type, [0,2])){
            $state = 'flat';
        }else{
            $state = 'note';
        }
        $this->addHouseToAddressState($house, $user, $state);
        if ($state === 'flat'){
            app(FlatAddressSender::class)->handle($update->getMessage());
        }
    }
    private function addHouseToAddressState(string $house, User $user, string $state)
    {
        $this->addressStateService->updateAddressState($user->addressState, [
            'house' => $house,
            'state' => $state
        ]);
    }
}
