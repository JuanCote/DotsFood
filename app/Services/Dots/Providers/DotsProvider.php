<?php
/**
 * Description of DotsProvider.php
 * @copyright Copyright (c) MISTER.AM, LLC
 * @author    Egor Gerasimchuk <egor@mister.am>
 */

namespace App\Services\Dots\Providers;


use App\Services\Http\HttpClient;

class DotsProvider extends HttpClient
{

    protected function getServiceHost()
    {
        return config('services.dots.host');
    }

    public function getCities()
    {
        $response = $this->get('api/v2/cities?v=2.0.0');
        return $response;
    }
}
