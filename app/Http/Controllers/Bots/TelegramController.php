<?php


namespace App\Http\Controllers\Bots;

use App\Http\Controllers\Controller;
use App\Services\Telegram\Callbacks\Addresses\AddAddressCallback;
use App\Services\Telegram\Callbacks\Addresses\CityAddressCallback;
use App\Services\Telegram\Callbacks\Addresses\StartAddAddressCallback;
use App\Services\Telegram\Callbacks\Addresses\TypeAddressCallback;
use App\Services\Telegram\Callbacks\CreateOrder\CategoryCallback;
use App\Services\Telegram\Callbacks\CreateOrder\CheckOrderCallback;
use App\Services\Telegram\Callbacks\CreateOrder\CityCallback;
use App\Services\Telegram\Callbacks\CreateOrder\CompanyAddressCallback;
use App\Services\Telegram\Callbacks\CreateOrder\CompanyCallback;
use App\Services\Telegram\Callbacks\CreateOrder\CreateNewOrderCallback;
use App\Services\Telegram\Callbacks\CreateOrder\DeliveryCallback;
use App\Services\Telegram\Callbacks\CreateOrder\DeliveryTypeCallback;
use App\Services\Telegram\Callbacks\CreateOrder\DishCallback;
use App\Services\Telegram\Callbacks\CreateOrder\OrderAgreeCallback;
use App\Services\Telegram\Callbacks\CreateOrder\PaymentTypeCallback;
use App\Services\Telegram\Callbacks\MainMenu\ActiveOrdersCallback;
use App\Services\Telegram\Callbacks\MainMenu\HistoryOrdersCallback;
use App\Services\Telegram\Callbacks\MainMenu\MainMenuCallback;
use App\Services\Telegram\Handlers\MessageHandler;
use App\Services\Telegram\Handlers\ReceiveContactHandler;
use Illuminate\Support\Facades\Log;
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
        $update = $this->telegram->getWebhookUpdate();
        if (!empty($update)) {
            // Checking if update is a callback or not
            if ($update->callbackQuery) {
                $callback_query = $update->callbackQuery;
                $data = $callback_query->data;
                if (str_starts_with($data, 'city_')) {
                    app(CityCallback::class)->handle($callback_query);
                }elseif (str_starts_with($data, 'company_')){
                    app(CompanyCallback::class)->handle($callback_query);
                }elseif (str_starts_with($data, '/decline')){
                    app(MainMenuCallback::class)->handle($callback_query);
                }elseif (str_starts_with($data, 'category_')) {
                    app(CategoryCallback::class)->handle($callback_query);
                }elseif (str_starts_with($data, 'dish_')){
                    app(DishCallback::class)->handle($callback_query);
                }elseif ($data === 'delivery') {
                    app(DeliveryCallback::class)->handle($callback_query);
                }elseif ($data === 'main_menu') {
                    app(MainMenuCallback::class)->handle($callback_query);
                }elseif ($data === 'order_agree') {
                    app(OrderAgreeCallback::class)->handle($callback_query);
                }elseif ($data === 'create_order') {
                    app(CreateNewOrderCallback::class)->handle($callback_query);
                }elseif ($data === 'active_orders') {
                    app(ActiveOrdersCallback::class)->handle($callback_query);
                }elseif ($data === 'history_orders') {
                    app(HistoryOrdersCallback::class)->handle($callback_query);
                }elseif ($data === 'add_address') {
                    app(AddAddressCallback::class)->handle($callback_query);
                }elseif ($data === 'add_address_start') {
                    app(StartAddAddressCallback::class)->handle($callback_query);
                }elseif (str_starts_with($data, 'check_order_')) {
                    app(CheckOrderCallback::class)->handle($callback_query);
                } elseif (str_starts_with($data, 'delivery_type_')) {
                    app(DeliveryTypeCallback::class)->handle($callback_query);
                }elseif (str_starts_with($data, 'payment_')) {
                    app(PaymentTypeCallback::class)->handle($callback_query);
                }elseif (str_starts_with($data, 'companyAddress_')) {
                    app(CompanyAddressCallback::class)->handle($callback_query);
                }elseif (str_starts_with($data, 'address_city_')) {
                    app(CityAddressCallback::class)->handle($callback_query);
                }elseif (str_starts_with($data, 'type_address_')) {
                    app(TypeAddressCallback::class)->handle($callback_query);
                }
                // Checking if update is an incoming contact after receiving
            }elseif (isset($update->getMessage()->contact)){
                app(ReceiveContactHandler::class)->handle($update);
            }else{
                app(MessageHandler::class)->handle($update);
            }
        }
        $this->telegram->commandsHandler(true);

        return 'Ok';
    }
}
