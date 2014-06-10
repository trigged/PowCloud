<?php
class GroupOperation extends XEloquent {
    protected $table = 'group_operation';

    //protected $softDelete = true;
    public $timestamps = false;

    protected  $fillable = array('group_id', 'models_id', 'read', 'edit');

    public function models(){

        //    $this->belongsTo('SchemaBuilder');
    }

    public function rules(){

        return  array(
            'default'=>array(
            ),
        );

    }


}