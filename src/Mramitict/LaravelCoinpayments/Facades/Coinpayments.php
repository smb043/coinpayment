<?php
/**
 * Created by PhpStorm.
 * User: Amit
 * Date: 20/03/2017
 * Time: 4:22 PM
 */

namespace Mramitict\LaravelCoinpayments\Facades;

use Illuminate\Support\Facades\Facade;
use Mramitict\LaravelCoinpayments\Providers\LaravelCoinpaymentsServiceProvider;

class Coinpayments extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return LaravelCoinpaymentsServiceProvider::SINGLETON; }
}