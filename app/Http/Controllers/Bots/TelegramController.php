<?php


namespace App\Http\Controllers\Bots;

use App\Http\Controllers\Controller;
use App\Services\Telegram\Callbacks\ActiveOrdersCallback;
use App\Services\Telegram\Callbacks\CategoryCallback;
use App\Services\Telegram\Callbacks\CheckOrderCallback;
use App\Services\Telegram\Callbacks\CityCallback;
use App\Services\Telegram\Callbacks\CompanyAddressCallback;
use App\Services\Telegram\Callbacks\CompanyCallback;
use App\Services\Telegram\Callbacks\CreateNewOrderCallback;
use App\Services\Telegram\Callbacks\HistoryOrdersCallback;
use App\Services\Telegram\Callbacks\MainMenuCallback;
use App\Services\Telegram\Callbacks\DeclineCallback;
use App\Services\Telegram\Callbacks\DeliveryCallback;
use App\Services\Telegram\Callbacks\DeliveryTypeCallback;
use App\Services\Telegram\Callbacks\DishCallback;
use App\Services\Telegram\Callbacks\OrderAgreeCallback;
use App\Services\Telegram\Callbacks\PaymentTypeCallback;
use App\Services\Telegram\Commands\StartCommand;
use App\Services\Telegram\Handlers\Commands\StartCommandHandler;
use App\Services\Telegram\Handlers\ReceiveContact;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use function Symfony\Component\Translation\t;

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
                    app(DeclineCallback::class)->handle($callback_query);
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
                }elseif (str_starts_with($data, 'check_order_')) {
                    app(CheckOrderCallback::class)->handle($callback_query);
                } elseif (str_starts_with($data, 'delivery_type_')) {
                    app(DeliveryTypeCallback::class)->handle($callback_query);
                }elseif (str_starts_with($data, 'payment_')) {
                    app(PaymentTypeCallback::class)->handle($callback_query);
                }elseif (str_starts_with($data, 'companyAddress_')) {
                    app(CompanyAddressCallback::class)->handle($callback_query);
                }
                // Checking if update is an incoming contact after receiving
            }elseif (isset($update->getMessage()->contact)){
                app(ReceiveContact::class)->handle($update);
            }
        }
        $this->telegram->commandsHandler(true);

        return 'Ok';
    }
}
