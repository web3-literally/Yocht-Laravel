<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Shop;

/**
 * Class StoreCallbackController
 * @package App\Http\Controllers
 */
class StoreCallbackController extends \Amsgames\LaravelShop\Http\Controllers\Shop\CallbackController
{
    /**
     * @param Request $request
     * @param $status
     * @param $id
     * @param $shoptoken
     * @return \Amsgames\LaravelShop\Http\Controllers\Shop\redirect
     */
    public function callback(Request $request, $status, $id, $shoptoken)
    {
        return $redirect = $this->process($request, $status, $id, $shoptoken);
    }
}
