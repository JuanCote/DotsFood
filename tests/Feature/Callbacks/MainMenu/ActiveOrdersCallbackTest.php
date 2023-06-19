<?php

namespace Tests\Feature\Callbacks\CreateOrder;


use App\Models\Order;
use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Callbacks\CreateOrder\CityCallback;
use App\Services\Telegram\Callbacks\MainMenu\ActiveOrdersCallback;
use App\Services\Telegram\Senders\CreateOrder\CompanySender;
use App\Services\Telegram\Senders\MainMenu\ActiveOrdersSender;
use App\Services\Users\UsersService;
use Mockery;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;
use Tests\TestCase;

class ActiveOrdersCallbackTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testActiveOrdersCallbackHandle(): void
    {
        $usersServiceMock = Mockery::mock(UsersService::class);
        $dotsServiceMock = Mockery::mock(DotsService::class);

        $activeOrdersCallback = new ActiveOrdersCallback($usersServiceMock, $dotsServiceMock);

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
        $callbackQuery = new CallbackQuery(['data' => 'city_12123123132', 'message' => $message]);

        $user = new User();
        $user->dotsUserId = 'qa1asd';

        $usersServiceMock->shouldReceive('findUserByTelegramId')->with($message->chat->id)->andReturn($user);

        $dotsServiceMock->shouldReceive('userActiveOrders')->once()->andReturn([]);

        $activeOrdersSenderMock = Mockery::mock(ActiveOrdersSender::class);
        $activeOrdersSenderMock->shouldReceive('handle')->once()->andReturn();
        app()->instance(ActiveOrdersSender::class, $activeOrdersSenderMock);

        $activeOrdersCallback->handle($callbackQuery);
    }
}
