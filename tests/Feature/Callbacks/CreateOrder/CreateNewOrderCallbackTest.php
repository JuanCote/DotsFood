<?php

namespace Tests\Feature\Callbacks\CreateOrder;


use App\Services\Telegram\Callbacks\CreateOrder\CreateNewOrderCallback;
use App\Services\Telegram\Senders\CreateOrder\CitySender;
use Mockery;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;
use Tests\TestCase;

class CreateNewOrderCallbackTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testCreateNewOrderCallbackHandle(): void
    {
        $createNewOrderCallback = new CreateNewOrderCallback();
        $messageData = [
            'message_id' => 928,
            'from' => [
                'id' => 6024637811,
                'is_bot' => true,
                'first_name' => 'dotphpPractise',
                'username' => 'phppract_bot',
            ],
            'chat' => [
                'id' => 1908900801,
                'first_name' => 'Никита',
                'last_name' => 'Бондаренко',
                'type' => 'private',
            ],
            'date' => 1687018932,
            'edit_date' => 1687022994,
            'text' => "Hi, with the help of this bot you can order food all over Ukraine 🚚",
            'reply_markup' => [
                'inline_keyboard' => [
                    [
                        [
                            'text' => '✅ Create new order',
                            'callback_data' => 'create_order',
                        ],
                    ],
                    [
                        [
                            'text' => '👀 View active orders',
                            'callback_data' => 'active_orders',
                        ],
                    ],
                    [
                        [
                            'text' => '📜 Order history',
                            'callback_data' => 'history_orders',
                        ],
                    ],
                    [
                        [
                            'text' => '🏠 Add address',
                            'callback_data' => 'add_address',
                        ],
                    ],
                ],
            ],
        ];

        $message = new Message($messageData);
        $callbackQuery = new CallbackQuery(['data' => 'create_order', 'message' => $message]);
        $citySenderMock = Mockery::mock(CitySender::class);
        $citySenderMock->shouldReceive('handle')->once()->andReturn();
        app()->instance(CitySender::class, $citySenderMock);
        $createNewOrderCallback->handle($callbackQuery);
    }
}
