<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/12/13
 * Time: 2:59 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Operator;

//use Illuminate\Support\Facades\Log;

use DB;
use Illuminate\Support\Facades\Redis;
use SchemaBuilder;
use Utils\CMSLog;

class ReadApi
{
    const LOAD_COUNT = 100;

    public static function tableHasMore($table_name)
    {
        return self::redis()->get(RedisKey::sprintf(RedisKey::MORE_DATA, $table_name));
    }

    public static function redis()
    {
        //WriteApi::redis();
        return Redis::connection();

    }

    public static function existsKey($key)
    {
        return self::redis()->exists(RedisKey::sprintf($key));
    }

    public static function getTableInfo($table_name, $flag = false)
    {
        $value = self::redis()->hgetall(RedisKey::sprintf(RedisKey::INFO, $table_name));
        if ($value == null || $flag) {
            $value = SchemaBuilder::where('table_name', $table_name)->first();
            if (is_object($value)) {
                $value = $value->toArray();
            }
            WriteApi::setTableObject(RedisKey::sprintf(RedisKey::INFO, $table_name), $value);
        }
        return $value;
    }

    public static function countZset($key)
    {
        return self::redis()->zcount(RedisKey::sprintf($key), '-inf', '+inf');
    }

    public static function getTimingData($pub_time, $withScores = null)
    {
        if ($withScores) {
            return self::redis()->ZREVRANGEBYSCORE(RedisKey::sprintf(RedisKey::TIMING_PUB), $pub_time, '-inf', 'withscores');
        }
        return self::redis()->ZREVRANGEBYSCORE(RedisKey::sprintf(RedisKey::TIMING_PUB), $pub_time, '-inf');
    }

    public static function getTimingInfo($key)
    {
        return self::redis()->hget(RedisKey::sprintf(RedisKey::TIMING_PUB_INFO), $key);
    }

    public static function getAllTableObject($table_name, $flash = false)
    {
        $value = CacheController::getRange($table_name, null);
        if ($value != null && count($value) !== 0) {
            return self::getDataByIDs($table_name, $value);
        }
        if (empty($value)) {
            if ($flash) {
                WriteApi::flushTableCache($table_name);
            }
            self::getLimitTableObject($table_name);
            CMSLog::debug(sprintf('key  not exists load from db key: %s', $table_name));
            $value = DB::connection('models')->table($table_name)->where('deleted_at', '0000-00-00 00:00:00')->get();
            CacheController::setRange($table_name, $value);
        }
        return $value;
    }

    public static function getDataByIDs($table_name, $data)
    {
        $result = array();
        foreach ($data as $id) {
            $tmp = self::getTableObject($table_name, $id, true);
            if ($tmp != null && !is_string($tmp)) {
                $result[] = $tmp;
            }
        }
        return $result;
    }

    public static function getTableObject($table_name, $id, $failBack = false)
    {
        $key = RedisKey::buildKey($table_name, $id);
        $value = self::redis()->hgetall($key);
        if (empty($value) && $failBack) {
            CMSLog::debug(sprintf('key  not exists load from db key: %s', $key));
            $value = DB::connection('models')->table($table_name)->where('deleted_at', '0000-00-00 00:00:00')->where('id', $id)->first();
            if ($value == null) {
                return '数据不存在';
            }
            if (is_object($value)) {
                $value = (array)$value;
            }
            WriteApi::setTableObject($key, $value);
        }
        return $value;
    }

    public static function getLimitTableObject($table_name, $offset = 1000, $count = 100)
    {
        $cache_count = ReadApi::getCacheCount($table_name);
        //redis has cached some some data not need load this again~
        for ($i = $cache_count; $i <= $offset * $count; $i += self::LOAD_COUNT) {
            $value = self::loadDatas($table_name, $i);
            if ($value !== true) {
                CMSLog::debug(sprintf('already get all data table name %s, offset : %s,', $table_name, $i));
                break;
            }
        }
    }

    public static function getCacheCount($table_name)
    {
//        ZCOUNT special::index  -inf +inf
        return self::redis()->ZCOUNT(RedisKey::sprintf(RedisKey::Index, $table_name), '-inf', '+inf');
    }

    public static function loadDatas($table_name, $offset = 0, $count = self::LOAD_COUNT)
    {
        $query = DB::connection('models')->table($table_name)->orderBy('rank', 'desc');
        $query = $query->skip($offset)->take($count)->where('deleted_at', '0000-00-00 00:00:00');
        $sql = $query->toSql();
        $data = $query->get();
        CacheController::setRange($table_name, $data);
        CMSLog::debug(sprintf('load data from db, sql : %s, count :%s, limit :%s', $sql, count($data), $count));
        if (count($data) < $count) {
            //no more data
            WriteApi::setTableCountState($table_name);
            return false;

        }
        return true;
    }

    public static function getKey($key, $value)
    {
        return RedisKey::sprintf($key, $value);
    }

    public static function getRoutes($path)
    {
        return self::redis()->hgetall(RedisKey::sprintf(RedisKey::ROUTE, $path));
    }

    public static function getApiInfo($table, $method)
    {
        return self::redis()->hgetall(RedisKey::sprintf(RedisKey::API_INFO, $table, $method));
    }

    public static function get($key)
    {
        return self::redis()->get(RedisKey::sprintf($key));
    }

}