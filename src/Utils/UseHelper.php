<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/12/13
 * Time: 2:29 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Utils;


error_reporting(E_ERROR | E_PARSE);

class UseHelper
{
    public static $default_key = 'R3iD=rYT7cAn6UYYgQ06qGrdHMll2i65AK4@CX3PsrVh';

    private static $keygen = 'x-cms-lmc';

    public static function checkToken($token, $secret_key, $fields = array(0))
    {
        if (empty($token)) {
            return false;
        }
        $value = self::parse($token, $secret_key, $fields);
        if ($value && $value[0]) {
            return rtrim($value[0], self::$keygen);
        }
        return false;
    }

    /**
     * 通用解析Parse
     *
     * @static
     * @param       $token
     * @param array $fields
     * @return array
     */
    private static function parse($token, $secret_key, $fields = array())
    {
        $ret = array();
        if (!$token) return $ret;

        // 解析
        $str = self::decrypt(base64_decode($token), $secret_key, $secret_key . '_iv');
        if (!$str) return $ret;

        // 将字段和值拼接，根据$的数量进行拆解
        $ret = substr_count($str, '$') + 1 >= count($fields) ? array_combine($fields, array_slice(explode('$', $str), 0, count($fields))) : false;
        return $ret;
    }

    private static function decrypt($input, $key, $iv)
    {
        $key = pack('H48', $key);
        $iv = pack('H16', $iv);
        $result = mcrypt_decrypt(MCRYPT_3DES, $key, $input, MCRYPT_MODE_ECB, $iv);
        $end = ord(substr($result, -1));
        $out = substr($result, 0, -$end);
        return $out;
    }

    public static function makeToken($key, $secret_key = null)
    {
        return self::build(array($key . self::$keygen), $secret_key);
    }

    /**
     * 通用Build
     *
     * @static
     * @param $values
     * @param $secret_key
     * @return string
     */
    private static function build($values, $secret_key = null)
    {
        if (empty($secret_key)) {
            $secret_key = self::generateRandomString();
        }
        return base64_encode(self::encrypt(join('$', $values), $secret_key, $secret_key . '_iv'));
    }

    public static function generateRandomString($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_#@+-';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    private static function encrypt($input, $key, $iv)
    {
        $key = pack('H48', $key);
        $iv = pack('H16', $iv);

        $src_data = $input;
        $block_size = mcrypt_get_block_size('tripledes', 'ecb');
        $padding_char = $block_size - (strlen($input) % $block_size);
        $src_data .= str_repeat(chr($padding_char), $padding_char);
        return mcrypt_encrypt(MCRYPT_3DES, $key, $src_data, MCRYPT_MODE_ECB, $iv);
    }
}


//$secret_key = 'R3iD=rYT7cAn6UYYgQ06qGrdHMll2i65AK4@CX3PsrVh';
//var_dump($secret_key);
//$token = UseHelper::makeToken('63', UseHelper::$default_key);
//var_dump($token);
//$start = microtime();
//$value = UseHelper::checkToken($token, $secret_key);
//var_dump(microtime() - $start);
//var_dump($value);
//
//$token = '8ch86oMZN6p1N1/TcSr9Fw==';
//var_dump(UseHelper::checkToken($token,UseHelper::$default_key));