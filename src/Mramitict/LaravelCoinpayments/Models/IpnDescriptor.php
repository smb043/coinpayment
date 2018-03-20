<?php
/**
 * Created by PhpStorm.
 * User: Amit
 * Date: 20/03/2017
 * Time: 4:22 PM
 */

namespace Mramitict\LaravelCoinpayments\Models;

/**
 * Class IpnDescriptor
 * 
 */
class IpnDescriptor extends Model
{
    public $fillable = [
        'ipn_id', 'ref_id','address','txn_id',
        'currency', 'currency1', 'currency2',
        'amount', 'amount1', 'amount2', 'fee',
        'buyer_name', 'item_name', 'item_number',
        'invoice', 'custom', 'send_tx', 'received_amount',
        'received_confirms', 'description'
    ];

    public function ipn() {
        return $this->belongsTo(Ipn::class, 'ipn_id');
    }

    public function isComplete () {
        // If $order_status is >100 or is 2, return true
        return $this->status >= 100 || $this->status == 2;
    }

    public function isWithdrawal () {
        return $this->ipn_type === 'withdrawal';
    }

    public function isApi () {
        return $this->ipn_type === 'api';
    }

    public function isSimpleButton () {
        return $this->ipn_type === 'simple';
    }

    public function isAdvancedButton () {
        return $this->ipn_type === 'button';
    }
}