<?php
use Operator\ReadApi;
use Operator\WriteApi;

/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/18/13
 * Time: 2:25 PM
 * To change this template use File | Settings | File Templates.
 */

class RouteManager
{

    protected static $routes = array();

    protected static $controller = 'ModelController';

    public static function addRouteWithRestFul($path, $model, $expire, $rights = array('index' => 1, 'store' => 1, 'update' => 1, 'delete' => 1))
    {
        if (empty($model)) {
            return false;
        }

        //default allow show
        $rights['show'] = 1;

        $data = array(
            'base'   => $path,
            'right'  => $rights,
            'model'  => $model,
            'expire' => $expire,
        );
        self::add_route($path, $data);

        return true;
    }

    protected static function addRoute($path, $data)
    {
        self::$routes[$path] = $data;

        $data['right'] = json_encode($data['right'], true);

        WriteApi::setRoutes($path, $data);
    }

    public static function updateRoute($path, $expires)
    {
        $value = self::findController($path);
        if (!$value) {
            return 'can\'t find the path ,check your params !';
        }
        $value['expire'] = $expires;
        self::add_route($path, $value);
        return true;
    }

    public static function findController($path)
    {
        //remove path numbers prepare for match
        $path = rtrim($path, '0..9');
        if (isset(self::$routes[$path])) {
            return self::$routes[$path];
        } elseif ($value = ReadApi::getRoutes($path)) {
            $value['right'] = json_decode($value['right'], true);
            Route::resource($value['base'], self::$controller);
            return $value;
        }
        return false;
    }

    /**
     * @param $path
     * @param $data
     */
    public static function add_route($path, $data)
    {
        self::addRoute($path, $data);
        self::addRoute($path . '/store', $data);
        self::addRoute($path . '/edit', $data);
        self::addRoute($path . '/', $data);
        self::addRoute($path . '/search', $data);
        self::addRoute($path . '/incrby', $data);
        Route::resource($path, self::$controller);
    }


}