<?php

namespace App\Services\Telegram\Handlers\Messages;

use App\Models\User;
use App\Services\AddressesStates\AddressStateService;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\Addresses\HouseAddressSender;
use App\Services\Telegram\Senders\Addresses\StageAddressSender;
use App\Services\Telegram\Senders\Addresses\SuccessAddressSender;
use App\Services\Telegram\Senders\MainMenu\MainMenuSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

// Сlass for processing a message with a note.
class NoteHandler
{
    public function __construct(
        DotsService   $dotsService,
        UsersService $userService,
        AddressStateService $addressStateService
    ) {
        $this->dotsService = $dotsService;
        $this->userService = $userService;
        $this->addressStateService = $addressStateService;
    }

    public function handle(Update $update, User $user)
    {
        $note = $update->getMessage()->text;

        $result = $this->addAddressToApi($user, $note);
        if ($result){
            app(SuccessAddressSender::class)->handle($update->getMessage(), true);
            return;
        }
        app(SuccessAddressSender::class)->handle($update->getMessage(), false);
    }

    private function addAddressToApi(User $user, string $note): bool
    {
        $addressState = $user->addressState;
        $addressObject = [
            'cityId' => $addressState->city_id,
            'type' => $addressState->type,
            'street' => $addressState->street,
            'house' => $addressState->house,
            'flat' => $addressState->flat,
            'stage' => $addressState->stage,
            'note' => $note
        ];
        $checkResult = $this->dotsService->validateUserAddress($addressObject);
        $this->userService->updateUser($user, [
            'address' => 'yes'
        ]);
        if (!array_key_exists('errors', $checkResult)){
            $this->dotsService->storeUserAddress($addressObject);
            return true;
        }
        return false;
    }
}
