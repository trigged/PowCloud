<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 3/27/14
 * Time: 5:46 PM
 * To change this template use File | Settings | File Templates.
 */

class ATURelationModel extends Eloquent
{
    public $timestamps = true;

    protected $table = 'atu_relation';

    protected $softDelete = true;

    protected $connection = 'base';

    public static function hasAccessRight($app_id, $user_id)
    {
        return ATURelationModel::where('app_id', $app_id)->where('user_id', $user_id)->exists();
    }

    public function user()
    {
        return $this->belongsTo('User');
    }


}