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


}
