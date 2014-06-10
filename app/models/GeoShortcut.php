<?php


class GeoShortCut extends XEloquent{

    protected  $table = 'geo_shortcut';

    protected  $guarded = array('id');

    protected $softDelete = true;

    public function rules(){

        return array(
            'default'=>array(
                'name'    =>'required|unique:geo_shortcut',
                'shortcut'=>'required',
            ),
            'update'=>array(
                'name'=>'required',
                'shortcut'=>'required',
            )
        );

    }
}