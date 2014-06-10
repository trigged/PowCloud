<?php
/**
 * Events list file
 * Created by PhpStorm.
 * User: troyfan
 * Date: 13-12-13
 * Time: 下午2:21
 */

Event::listen('widget',function($table_name,$action){
    $widget = Widget::whereRaw('status=1 AND table_name =? AND action = ?',array($table_name,$action))->first();
    if($widget){
        \Plugin\Widget::cast($widget)->run();
        App::shutdown();
        exit();
    }
});

Event::listen('cms.hook',function($tableName,$hookName,$params = array()){
    $hooks = Hook::whereRaw('status=1 AND table_name = ? AND name = ?',array($tableName,$hookName))->get();
    $hookStr = '';
    ob_start();
    foreach($hooks as $hook){
        \Plugin\Hook::createHook($hook)->run($params);
    }
    $hookStr = ob_get_contents();
    ob_clean();

    return $hookStr;
});


