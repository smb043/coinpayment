<?php

namespace Mramitict\LaravelCoinpayments;

use Illuminate\Http\Request;
use Mramitict\LaravelCoinpayments\Exceptions\CoinPaymentsException;
use Mramitict\LaravelCoinpayments\Exceptions\CoinPaymentsResponseError;
use Mramitict\LaravelCoinpayments\Exceptions\IpnIncompleteException;
use Mramitict\LaravelCoinpayments\Models\Ipn;
use Mramitict\LaravelCoinpayments\Models\Log;
use Mramitict\LaravelCoinpayments\Models\Model;
use Mramitict\LaravelCoinpayments\Models\Transaction;
use Mramitict\LaravelCoinpayments\Models\Transfer;
use Mramitict\LaravelCoinpayments\Models\Withdrawal;

/**
 * Class LaravelCoinpayments
 * @package Mramitict\LaravelCoinpayments
 * @method Transaction createTransactionSimple($amount, $currencyIn, $currencyOut, $additional = [])
 * @method Transaction createTransaction($req)
 * @method Transfer createTransfer($amount, $currency, $merchant, $autoConfirm = false)
 * @method Withdrawal createWithdrawal($amount, $currency, $address, $autoConfirm = false, $ipnUrl = '')
 *
 */
class LaravelCoinpayments extends Coinpayments {

    private $app;

    public function __construct($app)
    {
        $this->app = $app;

        parent::__construct(
            cp_conf('private_key'),
            cp_conf('public_key'),
            cp_conf('merchant_id'),
            cp_conf('ipn_secret'),
            cp_conf('ipn_url'),
            cp_conf('format')
        );
    }

    /**
     * Overrides the apiCall function returning the element
     *
     * @param string $cmd
     * @param array $req
     * @return Model
     * @throws CoinPaymentsResponseError
     */
    protected function apiCall($cmd, $req = array())
    {
        $receipt = parent::apiCall($cmd, $req);

        $has_error = $receipt->hasError();

        cp_log([
            'request' => $receipt->getRequest(),
            'response' => $receipt->getResponse()
        ], $has_error ? 'API_CALL_ERROR' : 'API_CALL',
            $has_error ? Log::LEVEL_ERROR : Log::LEVEL_ALL
        );

        if ($has_error)
            throw new CoinPaymentsResponseError($receipt->getError());

        $data = $receipt->toResultArray();

        if (isset($data['id'])) {
            $data['ref_id'] = $data['id'];
            unset($data['id']);
        }

        switch ($receipt->getCommand()) {
            case CoinpaymentsCommands::CREATE_TRANSACTION:
                return Transaction::create($data);
            case CoinpaymentsCommands::CREATE_WITHDRAWAL:
                return Withdrawal::create($data);
            case CoinpaymentsCommands::CREATE_TRANSFER:
                return Transfer::create($data);
        }

        return $receipt->getResponse()['result'];

    }

    /**
     * @param array $request
     * @param array|null $server
     * @param array $headers
     * @return Ipn
     * @throws IpnIncompleteException|CoinPaymentsException
     */
    public function validateIPN(array $request, array $server, $headers = [])
    {
        $log_data = [
            'request' => $request,
            'headers' => $headers,
            'server' => array_intersect_key($server, [
                'PHP_AUTH_USER', 'PHP_AUTH_PW'
            ])
        ];

        try {
            cp_log($log_data, 'IPN_RECEIVED', Log::LEVEL_ALL);

            $is_complete    = parent::validateIPN($request, $server);

            // create or update the existing IPN record
            try {
                $ipn = Ipn::where('ipn_id', $request['ipn_id'])->firstOrFail();
            }
            catch (\Exception $e) {
                $ipn = new Ipn();
                
            }
            
            $ipn->fill([
                'ipn_version' => $request['ipn_version'],
                'ipn_id' => $request['ipn_id'],
                'ipn_mode' => $request['ipn_mode'],
                'ipn_type' => $request['ipn_type'],
                'merchant' => $request['merchant'],
                'status' => $request['status'],
                'status_text' => $request['status_text'],
            ]);

            $ipn->save();

            $descriptor = $ipn->descriptor()->firstOrNew(['ipns_id' => $ipn->id]);

            $descriptor->fill([
                'ipns_id' => $ipn->id,
                'ref_id' => isset($request['id'])?$request['id']:null,
                'address' => isset($request['address'])?$request['address']:null,
                'txn_id' => isset($request['txn_id'])?$request['txn_id']:null,
                'currency' => isset($request['currency'])?$request['currency']:null,
                'currency1' => isset($request['currency1'])?$request['currency1']:null,
                'currency2' => isset($request['currency2'])?$request['currency2']:null,
                'amount' => isset($request['amount'])?$request['amount']:null,
                'amount1' => isset($request['amount1'])?$request['amount1']:null,
                'amount2' => isset($request['amount2'])?$request['amount2']:null,
                'fee' => isset($request['fee'])?$request['fee']:null,
                'buyer_name' => isset($request['buyer_name'])?$request['buyer_name']:null,
                'item_name' => isset($request['item_name'])?$request['item_name']:null,
                'item_number' => isset($request['item_number'])?$request['item_number']:null,
                'invoice' => isset($request['invoice'])?$request['invoice']:null,
                'custom' => isset($request['custom'])?$request['custom']:null,
                'send_tx' => isset($request['send_tx'])?$request['send_tx']:null,
                'received_amount' => isset($request['received_amount'])?$request['received_amount']:null,
                'received_confirms' => isset($request['received_confirms'])?$request['received_confirms']:null,
                'description' => serialize($request)
            ]);

            $descriptor->save();

            
            // only return the ipn if it was successful, otherwise throw an exception
            // we do it like this so we can record the ipn either way.
            if ($is_complete) {
                return $ipn;
            } else {
                throw new IpnIncompleteException($request['status_text'], $ipn);
            }
        }
        catch (CoinPaymentsException $e) {
            $log_data['error_message'] = $e->getMessage();

            cp_log($log_data, 'IPN_ERROR', Log::LEVEL_ERROR);

            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return Ipn
     */
    public function validateIPNRequest (Request $request) {
        return $this->validateIPN($request->all(), $request->server(), $request->headers);
    }
}