<?php
/**
 * Description of DotsService.php
 * @copyright Copyright (c) MISTER.AM, LLC
 * @author    Egor Gerasimchuk <egor@mister.am>
 */

namespace App\Services\Dots;


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
}
