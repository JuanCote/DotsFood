<?php
/**
 * Description of DotsService.php
 * @copyright Copyright (c) MISTER.AM, LLC
 * @author    Egor Gerasimchuk <egor@mister.am>
 */

namespace App\Services\Dots;


use App\Models\User;
use App\Services\Dots\Providers\DotsProvider;
use Illuminate\Support\Facades\Log;


class DotsService
{

    private $dotsProvider;

    public function __construct(
        DotsProvider $dotsProvider,
    ) {
        $this->dotsProvider = $dotsProvider;
    }

    public function getCities(): array
    {
        return $this->dotsProvider->getCities();
    }

    public function getCompanies(string $cityId): array
    {
        return $this->dotsProvider->getCompanies($cityId);
    }
    public function getDishes(string $companyId): array
    {
        return $this->dotsProvider->getDishes($companyId);
    }
    public function getDeliveryTypes(string $companyId): array
    {
        return $this->dotsProvider->getDeliveryTypes($companyId);
    }
    public function createOrder(User $user): array
    {
        $order = $user->order;
        $orderObject = [
            'orderFields' => [
                'cityId' => $order->city_id,
                'companyId' => $order->company_id,
                'userName' => $order->userName,
                'userPhone' => $order->userPhone,
                'deliveryType' => $order->delivery_type,
                'paymentType' => $order->payment_type,
                'deliveryTime' => 0,
                'cartItems' => $order->items
            ]
        ];
        if (!in_array($order->delivery_type, [0, 1])){
            $orderObject['orderFields']['companyAddressId'] = $order->company_address;
        }
        return $this->dotsProvider->createOrder($orderObject);
    }
    public function getCompanyAddresses(string $companyId): array
    {
        $addresses = $this->dotsProvider->getCompanyInfo($companyId)['addresses'];
        return $addresses;
    }
    public function checkOrder(string $orderId): array
    {
        return $this->dotsProvider->checkOrder($orderId);
    }
    public function resolveCart(User $user): array
    {
        $order = $user->order;
        $orderObject = [
            'orderFields' => [
                'cityId' => $order->city_id,
                'companyId' => $order->company_id,
                'userName' => $order->userName,
                'userPhone' => $order->userPhone,
                'deliveryType' => $order->delivery_type,
                'paymentType' => $order->payment_type,
                'deliveryTime' => 0,
                'cartItems' => $order->items
            ]
        ];
        if (!in_array($order->delivery_type, [0, 1])){
            $orderObject['orderFields']['companyAddressId'] = $order->company_address;
        }
        return $this->dotsProvider->resolveCart($orderObject);
    }
    public function userStatByPhone(int $phoneNumber): array
    {
        return $this->dotsProvider->userStatByPhone($phoneNumber);
    }
    public function userActiveOrders(string $dotsUserId): array
    {
        return $this->dotsProvider->userActiveOrders($dotsUserId);
    }
    public function userHistoryOrders(string $dotsUserId): array
    {
        return $this->dotsProvider->userHistoryOrders($dotsUserId);
    }
}
