<?php
return array(
    'nav'  => array(
        'cms'      => array('url' => URL::action('CmsController@index'), 'label' => '内容管理'),
        'system'   => array('url' => URL::action('SystemController@system'), 'label' => '系统管理'),
//        'advanced' => array('url' => URL::action('AdvancedController@index'), 'label' => '高级模式'),
        'limit'    => array('url' => URL::action('LimitController@user'), 'label' => '权限管理'),
//        'monitor'  => array('url' => URL::action('MonitorController@index'), 'label' => '数据监控'),
        'help'     => array('url' => 'http://doc.powapi.com', 'target' => '_blank', 'label' => '帮助文档'),
    ),

    'side' => array(
        'system'   => array(
//            '主机管理' => array(
//                array('label' => '域名添加', 'url' => URL::action('HostController@create'), 'menu' => 'host.create'),
//                array('label' => '域名列表', 'url' => URL::action('HostController@index'), 'menu' => 'host.list'),
//            ),

            'API 管理' => array(
                array('label' => 'API列表', 'url' => URL::action('PathController@index'), 'menu' => 'path.list'),
            ),

            '表管理'    => array(
                array('label' => '创建表', 'url' => URL::action('SchemaBuilderController@create'), 'menu' => 'schema.create'),
                array('label' => '表列表', 'url' => URL::action('SchemaBuilderController@index'), 'menu' => 'schema.list'),
            ),
            '内置模板'   => array(
//                array('label' => '分类', 'url' => URL::action('FormsController@forms'), 'menu' => 'forms.list'),
//                array('label' => '用户', 'url' => URL::action('FormsController@forms'), 'menu' => 'forms.list'),
            ),
            '表单管理'   => array(
                array('label' => '界面管理', 'url' => URL::action('FormsController@forms'), 'menu' => 'forms.list'),
            ),
        ),


        'cms'      => array(),
        'advanced' => array(
            '代码片断' => array(
                array('label' => 'widget', 'url' => URL::action('CodeFragmentController@widget'), 'menu' => 'codeFragment.widget'),
                array('label' => 'hooks', 'url' => URL::action('CodeFragmentController@hook'), 'menu' => 'codeFragment.hook'),
            ),
            '数据查询' => array(
                array('label' => 'mysql', 'url' => URL::action('CodeFragmentController@mysql'), 'menu' => 'codeFragment.mysql'),
                array('label' => 'redis', 'url' => URL::action('CodeFragmentController@redis'), 'menu' => 'codeFragment.redis'),
            ),
        ),
        'limit'    => array(
            '权限管理' => array(
                array('label' => '用户列表', 'url' => URL::action('LimitController@user'), 'menu' => 'limit.user'),
                array('label' => '用户组列表', 'url' => URL::action('LimitController@group'), 'menu' => 'limit.group'),
            ),
        ),
        'monitor'  => array(
            '服务管理'  => array(
                array('label' => 'mysql', 'url' => URL::action('MonitorController@mysql'), 'menu' => 'monitor.mysql'),
                array('label' => 'redis', 'url' => URL::action('MonitorController@redis'), 'menu' => 'monitor.redis'),
                array('label' => 'cache', 'url' => URL::action('MonitorController@cache'), 'menu' => 'monitor.cache'),

            ),
            'API监控' => array( //                array('label' => 'API执行', 'url' => URL::action('MonitorController@api'), 'menu' => 'monitor.api'),

            ),
            '服务器监控' => array(
                array('label' => 'mysqlMonitor', 'url' => '', 'menu' => 'monitor.mysql'),
                array('label' => 'redisMonitor', 'url' => '', 'menu' => 'monitor.redis'),
                array('label' => 'cacheMonitor', 'url' => '', 'menu' => 'monitor.redis'),
            ),
        )
    )
);