<?php

namespace Utils;



class Env
{
    public static function getRootDomain($url = false)
    {
        if (!$url) $url = 'http://' . $_SERVER['HTTP_HOST'];

        $domain = $url;
        preg_match('/^([a-zA-Z]+:\/\/)?([a-zA-Z0-9_\-\.]+)\/?(.*)?$/', $url, $match);
        if ($match[2]) {
            $tmp = explode('.', $match[2]);
            $size = count($tmp);
            if ($size > 1) {
                $lkey = $size - 1;
                $last = $tmp[$lkey];
                if (is_numeric($last)) {
                    $domain = $tmp[$lkey - 3] . '.' . $tmp[$lkey - 2] . '.' . $tmp[$lkey - 1] . '.' . $tmp[$lkey];
                } else {
                    $domain = $tmp[$lkey - 1] . '.' . $tmp[$lkey];
                }
            }
        }
        return $domain;
    }

    public static function getIP()
    {
        if (isSet($_SERVER)) {
            if (isSet($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } elseif (isSet($_SERVER["HTTP_CLIENT_IP"])) {
                $IP = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $IP = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $IP = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $IP = getenv('HTTP_CLIENT_IP');
            } else {
                $IP = getenv('REMOTE_ADDR');
            }
        }
        if (strstr($IP, ',')) {
            $ips = explode(',', $IP);
            $IP = $ips[0];
        }
        return $IP;
    }


    public static  function messageTip($type = "messageTip", $status = 'success', $message)
    {

        switch ($status) {
            case 'success':
                \Illuminate\Support\Facades\Session::flash($type, "toastr.success('操作成功', '$message');");
                break;
            case 'error':
                \Illuminate\Support\Facades\Session::flash($type, "toastr.error('操作失败', '$message');");
                break;
            default :
                \Illuminate\Support\Facades\Session::flash('info', "toastr.info('操作成功', '$message');");
                break;
        }
    }
}