<?php

namespace App\Services\Telegram\Handlers\Messages;

use App\Models\User;
use App\Services\AddressesStates\AddressStateService;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\Addresses\HouseAddressSender;
use App\Services\Telegram\Senders\Addresses\NoteAddressSender;
use App\Services\Telegram\Senders\MainMenu\MainMenuSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

// Сlass for processing a message with a street.
class StreetHandler
{
    public function __construct(
        AddressStateService $addressStateService
    ) {
        $this->addressStateService = $addressStateService;
    }

    public function handle(Update $update, User $user)
    {
        $street = $update->getMessage()->text;
        $type = $user->addressState->type;
        // A check to determine whether further inquiry for the house number is needed.
        if (in_array($type, [0,1,2])){
            $state = 'house';
        }else{
            $state = 'note';
        }
        $this->addStreetToAddressState($street, $user, $state);
        if ($state === 'house'){
            app(HouseAddressSender::class)->handle($update->getMessage());
        }else{
            app(NoteAddressSender::class)->handle($update->getMessage());
        }
    }
    private function addStreetToAddressState(string $street, User $user, string $state)
    {
        $this->addressStateService->updateAddressState($user->addressState, [
            'street' => $street,
            'state' => $state
        ]);
    }
}
