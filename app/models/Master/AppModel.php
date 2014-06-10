<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 3/27/14
 * Time: 2:16 PM
 * To change this template use File | Settings | File Templates.
 */

class AppModel extends Eloquent
{
    public $timestamps = true;

    protected $table = 'app';

    protected $softDelete = true;

    protected $guarded = array('id');

    protected $connection = 'base';

    public $errors = array();

    public function team()
    {
        return $this->belongsTo('team', 'team_id');
    }

    public function service()
    {
        return $this->belongsTo('service', 'service_id');
    }

    public function appUser()
    {
        return $this->hasMany('ATURelationModel', 'app_id');
    }
}
