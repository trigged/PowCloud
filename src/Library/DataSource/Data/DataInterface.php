<?php namespace Library\DataSource\Data;
/**
 * Created by PhpStorm.
 * User: troyfan
 * Date: 14-4-18
 * Time: 下午1:59
 */

interface DataInterface{

    public function mapLocal($mapLocal = array());
    public function getData();
}