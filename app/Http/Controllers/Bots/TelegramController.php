<?php


namespace App\Http\Controllers\Bots;

use App\Http\Controllers\Controller;
use App\Services\Telegram\Callbacks\CityCallback;
use App\Services\Telegram\Callbacks\CompanyCallback;
use App\Services\Telegram\Handlers\ReceiveContact;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function updates()
    {
        $updates = $this->telegram->getUpdates();
        if (!empty($updates)) {
            foreach ($updates as $update) {
                // Checking if update is a callback or not
                if ($update->ObjectType() === "callback_query") {
                    $callback_query = $update->callbackQuery;
                    $data = $callback_query->data;
                    if (str_starts_with($data, 'city_')) {
                        app(CityCallback::class)->handle($callback_query);
                    }elseif (str_starts_with($data, 'company_')){
                        app(CompanyCallback::class)->handle($callback_query);
                    }
                    // Checking if update is an incoming contact after receiving
                }elseif (isset($update->getMessage()->contact)){
                    app(ReceiveContact::class)->handle($update);
                }
            }
        }
        $this->telegram->commandsHandler(false);



        return 'Ok';
    }
}
