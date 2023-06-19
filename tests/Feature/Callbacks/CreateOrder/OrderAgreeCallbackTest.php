<?php

namespace Tests\Feature\Callbacks\CreateOrder;


use App\Models\Order;
use App\Models\User;
use App\Services\Dots\DotsService;
use App\Services\Telegram\Callbacks\CreateOrder\OrderAgreeCallback;
use App\Services\Telegram\Senders\CreateOrder\SuccessStoreOrderSender;
use App\Services\Users\UsersService;
use Mockery;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;
use Tests\TestCase;

class OrderAgreeCallbackTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testOrderAgreeCallbackHandle(): void
    {
        $usersServiceMock = Mockery::mock(UsersService::class);
        $dotsServiceMock = Mockery::mock(DotsService::class);

        $orderAgreeCallback = new OrderAgreeCallback($usersServiceMock, $dotsServiceMock);

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
        $callbackQuery = new CallbackQuery(['data' => 'payment_1', 'message' => $message]);

        $user = new User();
        $user->order = new Order();

        $usersServiceMock->shouldReceive('findUserByTelegramId')->with($message->chat->id)->andReturn($user);

        $dotsServiceMock->shouldReceive('createOrder')->once()->andReturn();

        $successStoreOrderSenderMock = Mockery::mock(SuccessStoreOrderSender::class);
        $successStoreOrderSenderMock->shouldReceive('handle')->once()->andReturn();
        app()->instance(SuccessStoreOrderSender::class, $successStoreOrderSenderMock);

        $orderAgreeCallback->handle($callbackQuery);
    }
}
