<?php

namespace Tests\Feature\Callbacks\Addresses;


use App\Models\AddressState;
use App\Models\Order;
use App\Models\User;
use App\Services\AddressesStates\AddressStateService;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Callbacks\Addresses\AddAddressCallback;
use App\Services\Telegram\Callbacks\Addresses\CityAddressCallback;
use App\Services\Telegram\Callbacks\CreateOrder\CityCallback;
use App\Services\Telegram\Callbacks\MainMenu\ActiveOrdersCallback;
use App\Services\Telegram\Senders\Addresses\AddAddressSender;
use App\Services\Telegram\Senders\Addresses\TypeAddressSender;
use App\Services\Telegram\Senders\CreateOrder\CompanySender;
use App\Services\Telegram\Senders\MainMenu\ActiveOrdersSender;
use App\Services\Users\UsersService;
use Mockery;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;
use Tests\TestCase;

class CityAddressCallbackTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testCityAddressCallbackHandle(): void
    {
        $usersServiceMock = Mockery::mock(UsersService::class);
        $addressStateServiceMock = Mockery::mock(AddressStateService::class);

        $cityAddressCallback = new CityAddressCallback($usersServiceMock, $addressStateServiceMock);

        $messageData = [
            'message_id' => 928,
            'from' => [
                'id' => 6024637811,
                'is_bot' => true,
                'first_name' => 'dotphpPractise',
                'username' => 'phppract_bot',
            ],
            'chat' => [
                'id' => 123,
                'first_name' => 'Никита',
                'last_name' => 'Бондаренко',
                'type' => 'private',
            ],
            'date' => 1687018932,
            'edit_date' => 1687022994,
        ];

        $message = new Message($messageData);
        $callbackQuery = new CallbackQuery(['data' => 'address_city_asd1asd', 'message' => $message]);

        $user = new User();
        $user->addressState = new AddressState();

        $usersServiceMock->shouldReceive('findUserByTelegramId')->with($message->chat->id)->andReturn($user);
        $addressStateServiceMock->shouldReceive('updateAddressState')->once()->andReturn();

        $typeAddressSenderMock = Mockery::mock(TypeAddressSender::class);
        $typeAddressSenderMock->shouldReceive('handle')->once()->andReturn();
        app()->instance(TypeAddressSender::class, $typeAddressSenderMock);

        $cityAddressCallback->handle($callbackQuery);
    }
}
