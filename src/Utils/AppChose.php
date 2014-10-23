<?php
namespace Utils;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Operator\RedisKey;

/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 3/31/14
 * Time: 10:50 AM
 * To change this template use File | Settings | File Templates.
 */

class AppChose
{

    public static function updateConf($app_id = null)
    {
        if (empty($app_id)) {
            $app_id = Session::get('app_id');
        }
        self::updateDB($app_id);
        self::updateCache($app_id);
    }

    public static function updateDB($app_id)
    {
        $template_connection = Config::get(Config::get('app.template_connection'));
        $app_data = $app_models = $template_connection;

        $app_data_name = self::getDbDataName($app_id);
        $app_models_name = self::getDbModelsName($app_id);
        //update app conf
        $app_data['database'] = $app_data_name;
        $app_models['database'] = $app_models_name;
        Config::set('database.connections.mysql', $app_data);
        Config::set('database.connections.models', $app_models);
        Session::put('db', 'true');

    }

    public static function  getDbDataName($app_id)
    {
        return sprintf('cms_%s_data', $app_id);
    }

    public static function  getDbModelsName($app_id)
    {
        return sprintf('cms_%s_models', $app_id);
    }

    public static function updateCache($app_id)
    {

        RedisKey::$DB_KEY = $app_id;
//        $redis_conf = Config::get('database.redis');
//        $redis_conf['default']['database'] = $app_id;
//        Config::set('database.redis', $redis_conf);
//        Redis::update_connection($redis_conf);
    }

    public static function getCurrentAppID()
    {
        return Session::get('app_id');
    }

}