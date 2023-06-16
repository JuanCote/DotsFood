<?php

namespace App\Services\Telegram\Handlers\Messages;

use App\Models\User;
use App\Services\AddressesStates\AddressStateService;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\Addresses\HouseAddressSender;
use App\Services\Telegram\Senders\MainMenu\MainMenuSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

// Сlass for processing a message with a flat.
class FlatHandler
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
        $flat = $update->getMessage()->text;
        $type = $user->addressState->type;
        // A check to determine whether further inquiry for the house number is needed.
        if ($type == 0){
            $state = 'stage';
        }else{
            $state = 'note';
        }
        $this->addFlatToAddressState($flat, $user, $state);
        if ($state === 'stage'){
            app(::class)->handle($update->getMessage());
        }
    }
    private function addFlatToAddressState(string $flat, User $user, string $state)
    {
        $this->addressStateService->updateAddressState($user->addressState, [
            'flat' => $flat,
            'state' => $state
        ]);
    }
}
