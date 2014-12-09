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

class WriteApi
{


    /**
     * 记录 api 性能
     * @param $model
     * @param $method
     * @param $time
     */
    public static function addApiMonitor($model, $method, $time)
    {
        $date = date("Y-m-d");
        self::redis()->HINCRBY(RedisKey::sprintf('api::' . $model . '::' . $method), $date . ':time', $time);
        self::redis()->HINCRBY(RedisKey::sprintf('api::' . $model . '::' . $method), $date . ':count', 1);
    }

    public static function redis()
    {
        return Redis::connection();
    }

    /**
     * 记录路由
     * @param $path
     * @param $date
     */
    public static function setRoutes($path, $date)
    {
        self::redis()->hmset(RedisKey::sprintf(RedisKey::ROUTE, $path), $date);
    }

    /**
     * 清空 table 在 redis 数据
     * @param $table_name
     */
    public static function flushTableCache($table_name)
    {
//        $value = self::redis()->ZRANGE(sprintf(RedisKey::Index, $table_name), 0, -1);
//        if ($value) {
//            foreach ($value as $key) {
//                self::delCacheItemByID($table_name,$key);
//            }
//        }
        $value = self::redis()->keys(RedisKey::buildKey($table_name, '*'));
        if ($value) {
            foreach ($value as $key) {
                self::redis()->del($key);
            }
        }
        self::redis()->del(RedisKey::sprintf(RedisKey::Index, $table_name));
    }

    /**
     * 根据id 删除 cache 中的数据
     * @param $table_name
     * @param $id
     */
    public static function delCacheItemByID($table_name, $id)
    {
        WriteApi::redis()->del(RedisKey::buildKey($table_name, $id));
        //delete sort
        self::delCacheInRange($table_name, $id);
    }

    /**
     * 删除 rang 中的数据
     * @param $table_name
     * @param $id
     */
    private static function delCacheInRange($table_name, $id)
    {
        WriteApi::redis()->zrem(RedisKey::sprintf(RedisKey::Index, $table_name), $id);
    }

    /**
     * 标记table 的数据已经被全部 cache 住
     * @param $table_name
     * @return mixed
     */
    public static function setTableCountState($table_name, $state = 'true')
    {
        return self::redis()->set(RedisKey::sprintf(RedisKey::MORE_DATA, $table_name), $state);
    }

    #region zset operator

    public static function addUserBehavior($table_name, $uid, $data_id, $score = null)
    {
        if ($score == null) {
            $score = time();
        }
        self::zsetAdd(RedisKey::buildKey($table_name, $uid), $score, $data_id);
    }

    public static function delUserBehavior($table_name, $uid, $data_id)
    {
        self::zsetRem(RedisKey::buildKey($table_name, $uid), $data_id);
    }

    public static function zsetAdd($key, $score, $member)
    {
        self::redis()->zadd($key, $score, $member);
    }


    public static function zsetAdds($key, $score, $member)
    {
        self::redis()->zadd($key, $score, $member);
    }

    public static function zsetRem($key, $member)
    {
        self::redis()->zrem($key, $member);
    }

    #endregion

    /**
     * 加入定时发布数据
     * @param $table_name
     * @param $content_id
     * @param $pub_time
     * @return mixed
     */
    public static function addTimingData($type, $table_name, $content_id, $title, $pub_time)
    {
        $key = RedisKey::buildKey($table_name, $content_id);
        self::zsetAdd(RedisKey::sprintf(RedisKey::TIMING_PUB), $pub_time, $key);
        return self::redis()->hmset(RedisKey::sprintf(RedisKey::TIMING_PUB_INFO), $key, RedisKey::buildKeys(array($type, $table_name, $content_id, $title)));
    }

    /**
     * 删除定时发布的数据
     * @param $table_name
     * @param $content_id
     * @return mixed
     */
    public static function delTimingData($table_name, $content_id)
    {
        $key = RedisKey::buildKey($table_name, $content_id);
        self::redis()->zrem(RedisKey::sprintf(RedisKey::TIMING_PUB), $key);
        return self::redis()->hdel(RedisKey::sprintf(RedisKey::TIMING_PUB_INFO), $key);
    }

    public static function set($key, $value)
    {
        return self::redis()->set(RedisKey::sprintf($key), $value);
    }

    public static function incrby($model, $data_id, $filed, $value)
    {
        self::redis()->HINCRBY(RedisKey::buildKey($model, $data_id), $filed, $value);
    }

    public static function addDataInRange($table_name, $value, $rank, $id)
    {
        self::zsetAdd(RedisKey::sprintf(RedisKey::Index, $table_name), $rank, $id);
        self::setTableObject(RedisKey::buildKey($table_name, $id), $value);
    }

    /**
     * table obj to redis hash
     * @param $key
     * @param $data
     */
    public static function setTableObject($key, $data)
    {
        if ($data !== null) {
            self::redis()->hmset($key, $data);
            self::redis()->expire($key, 3600 * 24 * 7);
        }
    }

    public static function setAppConf($app_id, $data)
    {
        if ($data !== null) {
            self::redis()->hset(RedisKey::sprintf(RedisKey::APP_INFO), $app_id, $data);
        }
    }
}