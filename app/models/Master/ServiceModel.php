<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 3/27/14
 * Time: 2:18 PM
 * To change this template use File | Settings | File Templates.
 */

class ServiceModel extends Eloquent
{
    public $timestamps = true;

    protected $table = 'service';

    protected $softDelete = true;

    protected $connection = 'base';

    protected $guarded = array('id');

}