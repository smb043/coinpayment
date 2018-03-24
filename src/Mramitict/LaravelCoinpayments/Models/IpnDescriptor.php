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
        'ipns_id', 'ref_id','address','txn_id',
        'currency', 'currency1', 'currency2',
        'amount', 'amount1', 'amount2', 'fee',
        'buyer_name', 'item_name', 'item_number',
        'invoice', 'custom', 'send_tx', 'received_amount',
        'received_confirms', 'description'
    ];

    public function ipn() {
        return $this->belongsTo(Ipn::class, 'ipns_id');
    }
}