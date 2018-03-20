<?php
/**
 * Created by PhpStorm.
 * User: Amit
 * Date: 20/03/2017
 * Time: 4:22 PM
 */

namespace Mramitict\LaravelCoinpayments\Exceptions;


use Mramitict\LaravelCoinpayments\Models\Ipn;
use Throwable;

class IpnIncompleteException extends \Exception
{
    /**
     * @var Ipn
     */
    private $ipn;

    public function __construct ($message = "", Ipn $ipn, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->ipn = $ipn;
    }

    /**
     * @return Ipn
     */
    public function getIpn ()
    {
        return $this->ipn;
    }
}