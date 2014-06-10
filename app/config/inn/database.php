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
            'host'      => 'd0.inn',
            'database'  => 'x_cms',
            'username'  => 'inno',
            'password'  => 'inno!@#',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),
        'mysql'  => array(
            'driver'    => 'mysql',
            'host'      => 'd0.inn',
            'database'  => 'cms_1_data',
            'username'  => 'inno',
            'password'  => 'inno!@#',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),
        'models' => array(
            'driver'    => 'mysql',
            'host'      => 'd0.inn',
            'database'  => 'cms_1_models',
            'username'  => 'inno',
            'password'  => 'inno!@#',
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
            'database' => 4,
        ),

    ),
);
