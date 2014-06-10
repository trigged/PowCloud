<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 12/12/13
 * Time: 10:06 AM
 * To change this template use File | Settings | File Templates.
 * @property mixed code
 * @property mixed name
 * @property mixed table_name
 * @property mixed action
 */

class Widget extends XEloquent
{

    protected $table = 'widget';

    protected $softDelete = true;

    protected $fillable = array('table_name', 'name', 'code','status', 'action','user_name');

    public function rules()
    {

        return array(
            'default' => array(
                'table_name' => 'required|exists:models,table_name',
                'name' => 'required',
                'action' => 'required',
            )
        );

    }

    public static  function boot(){

        parent::boot();

        self::updated(function($model){
            Record::recordHistory('',$model->getTable(),$model->id,json_encode($model->oldData));
        });
    }
    public function messages()
    {
        return array(
            'table_name.required' => '模型名称必填',
            'table_name.exists'=>'表名不存在',
            'name.required' => '挂件名称必填',
            'action.required'=>'动作名称必填',
        );
    }

    public function fireXEloquentSavingEvent($model)
    {

        $this->user_name = Auth::user()->name;

        return true;
    }
}