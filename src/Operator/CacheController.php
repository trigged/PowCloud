<?php
namespace Operator;

use Utils\CMSLog;

/**
 * all data must have id and range field
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/20/13
 * Time: 10:31 AM
 * To change this template use File | Settings | File Templates.
 */

class CacheController
{


    public static function flashTable($table_name)
    {
        WriteApi::flushTableCache($table_name);
        ReadApi::getTableInfo($table_name, true);
        self::handlerPaging($table_name, 0, 20);
    }

    public static function update($table_name, $model)
    {
        self::create($table_name, $model);
    }

    public static function create($table_name, $model)
    {
        self::addDataInRange($table_name, $model);
    }

    /**
     * @param $name
     * @param $value
     */
    private static function addDataInRange($name, $value)
    {
        if (is_array($value) && isset($value['id']) && isset($value['rank'])) {
            WriteApi::redis()->zadd(RedisKey::sprintf(RedisKey::Index, $name), $value['rank'], $value['id']);
            WriteApi::setTableObject(RedisKey::buildKey($name, $value['id']), $value);
            WriteApi::redis()->expire(RedisKey::sprintf(RedisKey::Index, $name), 3600 * 24 * 7);
        } elseif (is_object($value) && isset($value->id) && isset($value->rank)) {
            WriteApi::redis()->zadd(RedisKey::sprintf(RedisKey::Index, $name), $value->rank, $value->id);
            WriteApi::setTableObject(RedisKey::buildKey($name, $value->id), (array)$value);
            WriteApi::redis()->expire(RedisKey::sprintf(RedisKey::Index, $name), 3600 * 24 * 7);
        } else {
            return 'miss arguments in model';
        }
        WriteApi::setTableCountState($name, '');
        return true;
    }

    public static function info($table_name, $id)
    {
        return ReadApi::getTableObject($table_name, $id, true);
    }

    public static function delete($table_name, $model)
    {
        if (isset($model['id'])) {
            //delete sort
            WriteApi::delCacheItemByID($table_name, $model['id']);
        } elseif (isset($model->id)) {
            WriteApi::delCacheItemByID($table_name, $model->id);
        }
    }


    public static function handlerPaging($table_name, $offset, $count)
    {
        $value = ReadApi::tableHasMore($table_name);
        //result was null and cache not exists index key
        if (!empty($value) && ReadApi::existsKey($table_name . '::index')) {
            return false;
        }
        if ($offset * $count <= ReadApi::LOAD_COUNT) {
            //first load ,just load the max count
            ReadApi::loadDatas($table_name);
        } else {
            ReadApi::getLimitTableObject($table_name, $offset, $count);
        }
        return self::getRange($table_name, $offset, $count);
    }


    public static function setRange($name, $data)
    {
        foreach ($data as $value) {
            self::addDataInRange($name, $value);
        }
    }

    //批量更数据

    public static function getRange($name, $offset = 0, $limit = 20)
    {
        if ($offset === null) {
            return ReadApi::redis()->zrevrangebyscore(RedisKey::sprintf(RedisKey::Index, $name), '+inf', '-inf');
        }
        return ReadApi::redis()->zrevrangebyscore(RedisKey::sprintf(RedisKey::Index, $name), '+inf', '-inf', 'limit', $offset * $limit, $limit);
    }

}