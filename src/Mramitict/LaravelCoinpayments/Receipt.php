<?php
/**
 * Created by PhpStorm.
 * User: Amit
 * Date: 20/03/2017
 * Time: 4:22 PM
 */

namespace Mramitict\LaravelCoinpayments;


class Receipt
{

    private $command;
    private $request;
    private $response;

    public function __construct($command, $request, $response)
    {
        $this->command = $command;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return bool
     */
    public function hasError () {
        return $this->response['error'] != 'ok';
    }

    /**
     * @return string
     */
    public function getError () {
        return $this->hasError() ? $this->response['error'] : null;
    }

    /**
     * @return array
     */
    public function toResultArray () {
        return array_merge($this->request, $this->response['result']);
    }
}