<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/12/13
 * Time: 3:28 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Operator;


class RedisKey
{

    static $DB_KEY = '';

    /**
     *hash
     *  "base" -> "path"
     *  "right" ->  "{\"index\":1,\"create\":0,\"update\":0,\"delete\":0,\"show\":1}"
     *  "model" -> "dbModel"
     *  "expire"-> "time"
     */
    const ROUTE = 'routes::%s';

    /**
     * design to store table struct
     * not use
     */
    const INFO = 'tables::%s::info';

    /**
     * string
     * a flag to judge redis already cache all of table data
     * when value is true means cache all of data,otherwise not
     */
    const MORE_DATA = '%s::more';

    /**
     * zset
     * to cache table data
     * the value is table id field,score is table rank filed
     */
    const Index = '%s::index';

    /**
     * zset
     * to pub timing data
     * table_name:id
     *
     */
    const TIMING_PUB = 'TIMING_PUB';

    /**
     * HASH
     * to store timing data info
     *  key-> table_name:id: ; value ->type:title
     */
    const TIMING_PUB_INFO = 'TIMING_PUB_INFO';

    /**
     * hash
     * to draw chat of api
     *  "2014-01-24:time" ->"5"
     *  "2014-01-24:count" -> "1"
     */
    const API_INFO = 'api::%s::%s';

    const DEFAULT_DELETE_TIME = '0000-00-00 00:00:00';

    //data state ,not deleted_at means Normal
    //has deleted_at check timing_state

//    CONST SUCCESS = 2;
//
//    //not pub data
//
//    CONST NOT_TIMING = -1;
//
//    //has timing data or pub failed


    //not pub data

    CONST DELETED = -1;

    //not use ,delete in next version

    CONST HAS_PUB = 1;

    //timing success,online

    CONST SUCCESS = 2;

    //just create data,don't display in api

    CONST READY_LINE = 3;

    //has timing data,prepare for pub data

    CONST HAS_PUB_ONLINE = 4;

    //has timing data,prepare for del data

    CONST HAS_PUB_OFFLINE = 5;

    CONST HAS_PUB_FIRST = 6;

    //need pub,bud pub failed

    CONST PUB_FAIL = 7;

    //offline

    CONST OFF_LINE = 8;

    CONST PUB_ONLINE = 9;

    /**
     * it's support return redis key for table,used tableId and tableName
     * @param $id
     * @param $name
     * @return string
     */
    public static function buildKey($name, $id)
    {
        return self::$DB_KEY . $name . '::' . $id;
    }

    /**
     * if not have DB key will generator global key,otherwise the key will like that BD::old_key
     * if value was none ,just add db ley prefix
     * @param $key
     * @param null $value
     * @return string
     */
    public static function sprintf($key, $value = null)
    {
        if ($value !== null) {
            return self::$DB_KEY . sprintf($key, $value);
        } else {
            return self::$DB_KEY . $key;
        }

    }

    public static function buildKeys($value, $key = '::')
    {
        return self::$DB_KEY . join($key, $value);
    }

}