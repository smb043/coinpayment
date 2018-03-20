<?php
/**
 * Created by PhpStorm.
 * User: Amit
 * Date: 20/03/2017
 * Time: 4:22 PM
 */

namespace Mramitict\LaravelCoinpayments\Models;

/**
 * Class Ipn
 * @package Kevupton\LaravelCoinpayments\Models
 * @property mixed id
 * @property mixed ipn_version
 * @property mixed ipn_id
 * @property mixed ipn_mode
 * @property mixed merchant
 * @property mixed ipn_type
 * @property mixed status
 * @property mixed status_text
 */
class Ipn extends Model
{

    public $fillable = [
        'ipn_version', 'ipn_id', 'ipn_mode', 
        'merchant', 'ipn_type', 'status',
        'status_text'
    ];

    public function descriptor()
    {
        return $this->hasOne(IpnDescriptor::class, 'ipn_id');
    }
    
    public function isComplete () {
        // If $order_status is >100 or is 2, return true
        return $this->status >= 100 || $this->status == 2;
    }

    public function isDeposit () {
        return $this->ipn_type === 'deposit';
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