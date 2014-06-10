<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/6/13
 * Time: 10:39 AM
 * To change this template use File | Settings | File Templates.
 * 10.208.6.15 masterdb.tv.idc.pplive.cn
 * 10.208.6.16 slavedb.tv.idc.pplive.cn
 * 10.208.10.116 redis.tv.idc.pplive.cn
 */
return array(
    'connections' => array(
        'base'   => array(
            'driver'    => 'mysql',
            'host'      => 'masterdb.tv.idc.pplive.cn',
            'database'  => 'x_cms',
            'username'  => 'pp_ott',
            'password'  => 'lC33!k20j9^Dg72sF3',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),
        'mysql'  => array(
            'driver'    => 'mysql',
            'host'      => 'masterdb.tv.idc.pplive.cn',
            'database'  => 'cms_1_data',
            'username'  => 'pp_ott',
            'password'  => 'lC33!k20j9^Dg72sF3',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),
        'models' => array(
            'driver'    => 'mysql',
            'host'      => 'masterdb.tv.idc.pplive.cn',
            'database'  => 'cms_1_models',
            'username'  => 'pp_ott',
            'password'  => 'lC33!k20j9^Dg72sF3',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        )
    ),


    'redis'       => array(

        'cluster' => false,

        'default' => array(
            'host'     => 'redis.tv.idc.pplive.cn',
            'port'     => 6380,
            'database' => 0,
        ),

    ),
);
