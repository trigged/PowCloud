<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/14/13
 * Time: 11:13 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Utils;


class IOHelper
{

    public static function Store($path, $file_name, $content, $replace = false)
    {
        try{
            //if path not exists then make it
            if (!file_exists($path)) {
                mkdir($path);
            }
            //check allow replace
            if (file_exists($path . '/' . $file_name) && $replace === false) {
                return false;
            }
            if (($result =  $result = file_put_contents($path . '/' . $file_name, $content)) !== false) {
                return true;
            }
            return false;
        }catch (\Exception $e){
            CMSLog::debug(__FILE__.$path);

            CMSLog::debug(__FILE__.':'.$e->getMessage());
            return false;
        }

    }

}