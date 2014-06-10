<?php namespace Library\DataSource;

use Utils\CMSLog;
use Utils\NetHelpers;

class DataProvider{

    public static function factory($dataSource,$target,$url=''){
        if(NetHelpers::isUrl($dataSource))
            $object = "Library\\DataSource\\Data\\UrlData";
        else
            $object = "Library\\DataSource\\Data\\".$dataSource;
        if(class_exists($object))//外部数据源查找
            return new $object($target,$url,$dataSource);
        else{//内部数据源查找
            $vm = new \ApiModel($dataSource);
            if($vmObj = $vm->newQuery()->find($target))
                return $vmObj;
            else
                CMSLog::debug($dataSource.':'.$target.'不存在');
            return $vm;
        }
    }
}