<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/6/13
 * Time: 10:39 AM
 * To change this template use File | Settings | File Templates.
 */
return array(
    'connections' => array(
        'base'   => array(
            'driver'    => 'mysql',
            'host'      => '192.168.13.16',
            'database'  => 'x_cms',
            'username'  => 'root',
            'password'  => 'ppliveDh',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),
        'mysql'  => array(
            'driver'    => 'mysql',
            'host'      => '192.168.13.16',
            'database'  => 'cms_3_data',
            'username'  => 'root',
            'password'  => 'ppliveDh',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),
        'models' => array(
            'driver'    => 'mysql',
            'host'      => '192.168.13.16',
            'database'  => 'cms_3_models',
            'username'  => 'root',
            'password'  => 'ppliveDh',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        )

    ),


    'redis'       => array(

        'cluster' => false,

        'default' => array(
            'host'     => 'c1.inn',
            'port'     => 6379,
            'database' => 8,
        ),

    ),
);
