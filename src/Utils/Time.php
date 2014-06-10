<?php

namespace Utils;

class Time
{
    public static function qTime($time)
    {
        $current_time = time();

        if (!is_numeric($time) && ($time = strtotime($time)) <= 0) {
            return '未曾';
        }

        $limit = $current_time - $time;

        $suffix = '前';
        if ($limit < 0) {
            $limit = -$limit;
            $suffix = '后';
        }

        if ($limit < 60) {
            $time = $suffix == '前' ? "刚刚" : '马上';
        } elseif ($limit >= 60 && $limit < 3600) {
            $i = floor($limit / 60);
            $time = "{$i}分" . $suffix;
        } elseif ($limit >= 3600 && $limit < 3600 * 24) {
            $h = floor($limit / 3600);
            $time = "{$h}小时" . $suffix;
        } elseif ($limit >= (3600 * 24) && $limit < (3600 * 24 * 30)) {
            $d = floor($limit / (3600 * 24));
            $time = "{$d}天" . $suffix;
        } elseif ($limit >= (3600 * 24 * 30) && $limit< (3600 * 24 * 30 * 12)) {
            $time = '一月前';
        }elseif ($limit >= (3600 * 24 * 30 * 12)) {
            $time = '一年前';
        }
        return $time;
    }

    public static function durationToSeconds($duration)
    {
        $arr = explode(':', $duration);

        switch (count($arr)) {
            case 2:
                return $arr[0] * 60 + $arr[1];
                break;
            case 3:
                return $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
        }
        return $duration;
    }
}