<?php

class Host extends XEloquent{

    protected  $table = 'host';

    protected  $fillable = array('name','host','comment','expire','cdn');

    protected  $guarded = array('id');

    protected $softDelete = true;

    public function rules(){

        return  array(
            'default'=>array(
                'name'=>'required|max:45|unique:host',
                'expire'=>'integer',
                'cdn'=>'in:0,1',
            ),
            'create'=>array(
                'host'=>'required|unique:host',
            ),
            'update'=>array(
                 'host'=>'',
                 'name'=>''
            ),
            'updateName'=>array(
                'host'=>'',
            )
        );

    }
    public function messages(){
        return array(
            'host.unique'=>'主机地址已存在',
            'name.unique'=>'主机名称已存在',
        );
    }
    public function user(){
        return $this->belongsTo('User');
    }



    public static function getHostList(){
        $optionsArray = array();
        $hosts = Host::all(array('id','name'));
        if($hosts){
            foreach($hosts as $host){
                $optionsArray[$host->id] = $host->name;
            }
        }
        return $optionsArray;
    }
}