<?php

namespace Tests\Feature\Callbacks\CreateOrder;


use App\Services\Dots\DotsService;
use App\Services\Telegram\Callbacks\CreateOrder\CheckOrderCallback;
use App\Services\Telegram\Senders\CreateOrder\CheckOrderSender;
use Mockery;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;
use Tests\TestCase;

class CheckOrderCallbackTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testCheckOrderCallbackHandle(): void
    {
        $dotsServiceMock = Mockery::mock(DotsService::class);

        $checkOrderCallback = new CheckOrderCallback($dotsServiceMock);

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
        $callbackQuery = new CallbackQuery(['data' => 'check_order_23as1', 'message' => $message]);

        $dotsServiceMock->shouldReceive('checkOrder')->once()->andReturn();

        $checkOrderSenderMock = Mockery::mock(CheckOrderSender::class);
        $checkOrderSenderMock->shouldReceive('handle')->once()->andReturn();
        app()->instance(CheckOrderSender::class, $checkOrderSenderMock);

        $checkOrderCallback->handle($callbackQuery);
    }
}
