<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 8/14/14
 * Time: 2:00 PM
 * To change this template use File | Settings | File Templates.
 */

class DataLink extends Eloquent
{
    public $timestamps = true;

    protected $table = 'data_link';

    protected $guarded = array('id');


}