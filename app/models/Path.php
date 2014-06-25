<?php

class Path extends XEloquent
{

    protected $table = 'path';

    protected $fillable = array('name', 'host_id', 'parent', 'expire', 'last_name');

    protected $guarded = array('id');

    protected $softDelete = true;

//    static $pathType = array(
//        '1' => 'restful',
//        '2' => 'url'
//    );

    static $pathRoot = array(
        'id'         => 0,
        'host_id'    => 0,
        'name'       => '/',
        'parent'     => '',
        'expire'     => '3600',
        'updated_at' => null,
        'type'       => 0
    );

    public function rules()
    {
        return array();
    }


    static $pathTree = array();

    static $pathTreeListOptions = array();

    /**
     * array(
     *      array( 'id'=>xxx,'pid'=>xxxx,'name'=>xxxx')
     * )
     *
     * @param string $path
     * @param integer $pid
     * @param integer $level
     * @param int $pid
     */
    public static function  getPathTree($path = '/', $pid = 0, $level = 1)
    {
        if ((int)$pid === 0) {
            self::$pathTreeListOptions[0] = '/';
            self::$pathTreeListOptions[-1] = '无';
            self::$pathTree [] = array(
                'id'      => 0,
                'name'    => '/',
                'open'    => true,
                'pId'     => 0,
                'infoUrl' => URL::action('PathController@edit', array('path' => 0))
            );
        }
        $paths = Path::where('parent', '=', $path)->get();
        if ($paths) {
            $level++;
            foreach ($paths as $path) {
                self::$pathTree [] = array(
                    'id'      => $path->id,
                    'name'    => $path->name,
                    'open'    => true,
                    'pId'     => $pid,
                    'infoUrl' => URL::action('PathController@edit', array('path' => $path->id))
                );
                self::$pathTreeListOptions[$path->id] = str_repeat('--', $level - 1) . $path->name;

                self::getPathTree($path->name, $path->id, $level);
            }
        }
    }


}