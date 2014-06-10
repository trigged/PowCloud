<?php

/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 12/12/13
 * Time: 10:06 AM
 *
 *
 * @property mixed code
 * @property mixed name
 * @property mixed table_name
 */

class Hook extends XEloquent
{
    const CMS_TABLE_HEAD_BUTTON = 'cms.tableList.headButton';

    const CMS_TABLE_HEADER = 'cms.tableList.header';

    const CMS_TABLE_FOOTER_BUTTON = 'cms.tableList.footerButton';


    protected $table = 'hook';

    protected $softDelete = true;

    protected $fillable = array('table_name', 'name', 'code','status','user_name');

    public function rules()
    {

        return array(
            'default' => array(
                'table_name' => 'required|exists:models,table_name',
                'name' => 'required',
            )
        );

    }

    public function messages()
    {
        return array(
            'table_name.required' => '模型名称必填',
            'table_name.exists'=>'表名不存在',
            'name.required' => '挂件名称必填',
        );
    }
    public static  function boot(){

        parent::boot();

        self::updated(function($model){
            Record::recordHistory('',$model->getTable(),$model->id,json_encode($model->oldData));
        });
    }

    public function fireXEloquentSavingEvent($model)
    {

        $this->user_name = Auth::user()->name;

        return true;
    }

    public static function getHooks(){

        return array(
            self::CMS_TABLE_HEAD_BUTTON =>'数据列表顶部Button',
            self::CMS_TABLE_FOOTER_BUTTON =>'数据列表底部Button',
            self::CMS_TABLE_HEADER => '数据列表头部'
        );
    }

    public static function getHooksName($hook){

        $hooks = self::getHooks();

        if(isset($hooks[$hook]))
            return $hooks[$hook];
        return '';
    }

}