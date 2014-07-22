<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/20/13
 * Time: 3:15 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Utils;


use Config;
use Illuminate\Support\Facades\Log;

class CMSLog extends Log
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */

    public static $requestHandler = null;


    public static function debug($message, $context = array())
    {
        if (Config::get('app.debug')) {
            parent::debug('ReqHandler :' . self::$requestHandler . ', ' . $message, $context);
        }
    }


}