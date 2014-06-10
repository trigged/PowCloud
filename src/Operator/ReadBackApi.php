<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/12/13
 * Time: 2:57 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Operator;
use Illuminate\Support\Facades\Redis;

class ReadBackApi {



    public static function redis()
    {
        //WriteApi::redis();
        return Redis::connection();

    }

}