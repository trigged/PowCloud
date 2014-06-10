<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 3/27/14
 * Time: 2:17 PM
 * To change this template use File | Settings | File Templates.
 */

class TeamModel extends Eloquent
{
    public $timestamps = true;

    protected $table = 'team';

    protected $softDelete = true;

    protected $guarded = array('id');

    protected $connection = 'base';

    public function apps()
    {
        return $this->hasMany('app', 'team_id');
    }
}