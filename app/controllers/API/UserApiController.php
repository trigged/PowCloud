<?php

class UserApiController extends ModelController
{

    static $ACTION_USER = 'user';

    static $ACTION_FRIENDS = 'friends';

    static $ACTION_XXX = 'xxx';

    static $USER_TABLE = 'user';

    public $user_id ;


    ## region user
    function user_create(){
        $name = Input::get('name');
        $nick_name = Input::get('nick_name',$name);
        $pwd = Input::get('password');
        $sex = Input::get('sex');
        $age = Input::get('age');
        $email = Input::get('email');
        $phone = Input::get('phone');
        $address = Input::get('address');
        $active = Input::get('active');


        if(empty($name) || empty($pwd) ){

        }


        $pwd = sha1($pwd);

    }

    function user_update(){

    }

    function user_info(){

    }


    function user_delete(){

    }
    ## endregion
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if ($this->action == self::$ACTION_USER) {
            return parent::index();
        }
        elseif($this->action == self::$ACTION_FRIENDS) {

        }
        elseif($this->action == self::$ACTION_XXX) {

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if ($this->action == self::$ACTION_USER) {
            $$this->user_create();
        }
        elseif($this->action == self::$ACTION_FRIENDS) {
            $this->friends_create();
        }
        elseif($this->action == self::$ACTION_XXX) {
            $$this->user_xx_create();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}