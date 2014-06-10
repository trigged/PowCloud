<?php
class Group extends XEloquent
{
    protected $table = 'group';

    //protected $softDelete = true;

    protected $fillable = array('groupName');

    public function models()
    {

        $this->belongsTo('GroupOperation');
    }

    public function rules()
    {
        return array(
            'default' => array(),
        );

    }

    private static $_groups = array();
    public static function getGroups()
    {
        $groupObjectArray = Group::all(array('id', 'groupName'));

        if ($groupObjectArray) {
            foreach ($groupObjectArray as $group) {
                self::$_groups[$group->id] = $group->groupName;
            }
        }
        return self::$_groups;
    }

    public static function getGroupName($gid){
        $groupArray = self::$_groups?self::$_groups:self::getGroups();
        return isset($groupArray[$gid])?$groupArray[$gid]:'';
    }

}