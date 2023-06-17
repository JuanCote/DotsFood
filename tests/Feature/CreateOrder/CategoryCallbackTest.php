<?php

namespace Tests\Feature\CreateOrder;


use App\Models\Order;
use App\Models\User;
use App\Services\Orders\OrdersService;
use App\Services\Telegram\Callbacks\CreateOrder\CategoryCallback;
use App\Services\Telegram\Callbacks\CreateOrder\CityCallback;
use App\Services\Telegram\Callbacks\CreateOrder\CompanyCallback;
use App\Services\Telegram\Callbacks\CreateOrder\CreateNewOrderCallback;

use App\Services\Telegram\Senders\CreateOrder\CategorySender;
use App\Services\Telegram\Senders\CreateOrder\CitySender;
use App\Services\Telegram\Senders\CreateOrder\CompanySender;
use App\Services\Telegram\Senders\CreateOrder\DishSender;
use App\Services\Users\UsersService;
use Mockery;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;
use Tests\TestCase;

class CategoryCallbackTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testCityCallbackHandle(): void
    {
        $usersServiceMock = Mockery::mock(UsersService::class);

        $categoryCallback = new CategoryCallback($usersServiceMock);

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
        $callbackQuery = new CallbackQuery(['data' => 'category_123213', 'message' => $message]);

        $user = new User();

        $usersServiceMock->shouldReceive('findUserByTelegramId')->with($message->chat->id)->andReturn($user);

        $dishSenderMock = Mockery::mock(DishSender::class);
        $dishSenderMock->shouldReceive('handle')->once()->andReturn();
        app()->instance(DishSender::class, $dishSenderMock);

        $categoryCallback->handle($callbackQuery);
    }
}
