<?php
/**
 * Description of DotsService.php
 * @copyright Copyright (c) MISTER.AM, LLC
 * @author    Egor Gerasimchuk <egor@mister.am>
 */

namespace App\Services\Dots;


use App\Models\User;
use App\Services\Dots\Providers\DotsProvider;


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
                // The number in the database is stored with '+' and it is not needed in the request
                'userPhone' => substr($order->userPhone, 1),
                'deliveryType' => $order->delivery_type,
                'paymentType' => $order->payment_type,
                'deliveryTime' => 0,
                'cartItems' => $order->items
            ]
        ];
        return $this->dotsProvider->createOrder($orderObject);
    }
}
