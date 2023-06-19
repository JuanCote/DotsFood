<?php

namespace Tests\Feature;


use App\Models\Order;
use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Callbacks\CreateOrder\CityCallback;
use App\Services\Telegram\Handlers\ReceiveContactHandler;
use App\Services\Telegram\Senders\CreateOrder\CompanySender;
use App\Services\Telegram\Senders\MainMenu\MainMenuSender;
use App\Services\Users\UsersService;
use Mockery;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;
use Tests\TestCase;

class ReceiveContactHandlerTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testReceiveContactHandle(): void
    {
        $usersServiceMock = Mockery::mock(UsersService::class);
        $ordersServiceMock = Mockery::mock(OrdersService::class);
        $dotsServiceMock = Mockery::mock(DotsService::class);

        $receiveContactHandler = new ReceiveContactHandler($usersServiceMock, $ordersServiceMock, $dotsServiceMock);

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
            'contact' => [
                'first_name' => 'Nikita',
                'phone_number' => '+380731112924'
            ],
            'date' => 1687018932,
            'edit_date' => 1687022994,
        ];

        $message = new Message($messageData);
        $update = new Update(['message' => $message]);

        $user = new User();
        $user->order = new Order();
        $user->dotsUserId = 'asd123';

        $usersServiceMock->shouldReceive('findUserByTelegramId')->with($message->chat->id)->andReturn($user);
        $usersServiceMock->shouldReceive('updateUser')->once()->andReturn();

        $ordersServiceMock->shouldReceive('createOrder')->andReturn();

        $dotsServiceMock->shouldReceive('userStatByPhone')->andReturn();

        $mainMenySenderMock = Mockery::mock(MainMenuSender::class);
        $mainMenySenderMock->shouldReceive('handle')->once()->andReturn();
        app()->instance(MainMenuSender::class, $mainMenySenderMock);

        $receiveContactHandler->handle($update);
    }
}
