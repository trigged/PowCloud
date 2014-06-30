<?php

class SystemController extends BaseController
{

    public $nav = 'system';

    public function __construct()
    {
        parent::__construct();

        if (Auth::user() && $this->getRoles() !== 3) {
            header('Location:' . URL::action('CmsController@index'));
        }
    }

    public function system()
    {
        return $this->render('cms.system', array());
    }
}