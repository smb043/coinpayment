<?php
/**
 * Created by PhpStorm.
 * User: Amit
 * Date: 20/03/2017
 * Time: 4:22 PM
 */

namespace Mramitict\LaravelCoinpayments\Models;

class Log extends Illuminate\Database\Eloquent\Model
{
    protected $table = 'cpts_log';

    const LEVEL_ALL = 2;
    const LEVEL_ERROR = 1;
    const LEVEL_NONE = 0;

    public $fillable = [
        'type', 'log'
    ];
}
