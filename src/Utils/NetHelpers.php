<?php

namespace Utils;


/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 1/10/14
 * Time: 6:43 PM
 * To change this template use File | Settings | File Templates.
 */
class NetHelpers
{


    public static function call_api($url, $method = 'GET', $data = false)
    {
        $curl = curl_init();

        switch ($method) {
            case 'GET':
                break;
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        return curl_exec($curl);
    }

    public static function get_url_code($url)
    {
        $header = get_headers($url);
        if ($header && isset($header[0])) {
            return $header[0];
        }
        return false;
    }

    public static function isUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL))
            return true;
        return false;
    }
}


