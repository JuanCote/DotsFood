<?php

namespace App\Services\Telegram\Handlers\Messages;

use App\Models\User;
use App\Services\AddressesStates\AddressStateService;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Senders\Addresses\HouseAddressSender;
use App\Services\Telegram\Senders\Addresses\NoteAddressSender;
use App\Services\Telegram\Senders\Addresses\StageAddressSender;
use App\Services\Telegram\Senders\MainMenu\MainMenuSender;
use App\Services\Users\UsersService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;

// Сlass for processing a message with a stage.
class StageHandler
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
        $stage = $update->getMessage()->text;
        $state = 'note';
        $this->addStageToAddressState($stage, $user, $state);
        app(NoteAddressSender::class)->handle($update->getMessage());
    }
    private function addStageToAddressState(string $stage, User $user, string $state)
    {
        $this->addressStateService->updateAddressState($user->addressState, [
            'stage' => $stage,
            'state' => $state
        ]);
    }
}
