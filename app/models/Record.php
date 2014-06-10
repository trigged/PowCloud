<?php

/**
 * Class Record
 */

class Record extends XEloquent{

    protected  $table = 'record';

    protected  $guarded = array('id');

    protected $softDelete = true;


    /**
     * @param string $connection  空为默认数据库连接  ApiModel指的是models链接
     * @param string $tableName
     * @param        $dataId
     * @param string $data   json
     *
     * @return bool
     */
    public static function recordHistory($connection='models',$tableName,$dataId,$data){

        $record = new Record();
        $record->connect = $connection;
        $record->table_name = $tableName;
        $record->content_id = $dataId;
        $record->content = $data;
        if(!$data)//没有数据的时候不进行任何操作
            return true;
        if($record->save()){
            Log::info('插入:'.$tableName.':数据历史纪录');
            return true;
        }

        return false;
    }

    public function fireXEloquentSavingEvent($model)
    {

        if(Auth::user())
            $model->user_name = Auth::user()->name;

        return true;
    }
}