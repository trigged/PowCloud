<?php
class GroupOperation extends XEloquent
{

    const  HAS_RIGHT = 2;

    const  NO_RIGHT = 2;

    protected $table = 'group_operation';

    //protected $softDelete = true;
    public $timestamps = false;

    protected $fillable = array('group_id', 'models_id', 'read', 'edit');

    public function models()
    {

        //    $this->belongsTo('SchemaBuilder');
    }

    public function rules()
    {

        return array(
            'default' => array(),
        );

    }


}