<?php

class SystemController extends BaseController
{

    public $nav = 'system';

    public function __construct()
    {
        parent::__construct();

        if (Auth::user() && (int)Auth::user()->roles !== 3) {
            header('Location:' . URL::action('CmsController@index'));
        }
    }

    public function system()
    {
        return $this->render('cms.system', array());
    }
}