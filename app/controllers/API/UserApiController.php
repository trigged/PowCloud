<?php

class UserApiController extends ModelController
{

    static $ACTION_USER = 'user';

    static $ACTION_FRIENDS = 'friends';

    static $ACTION_XXX = 'xxx';

    static $USER_TABLE = 'user';

    public $user_id ;


    ## region user
    public function login(){
        $name = Input::get('name');
        $pwd = Input::get('password');
        if(empty($name) || empty($pwd) ){
            return  $this->getResult(-1,'用户名或密码不能为空');
        }
        $user  = new ApiModel('user');
        if($user = $user->where('name',$name)->where('password',sha1($pwd))->first()){
            return  $this->getResult(1,'登陆成功',$this->process($user->toArray(),false));
        }
        return  $this->getResult(-1,'登陆失败,用户名或者密码错误');
    }

    public function userNameCheck(){
        $name = Input::get('name');
        if(empty($name)  ){
            return  $this->getResult(-1,'用户名不能为空');
        }
        $user  = new ApiModel('user');
        if($user->where('name',$name)->count()){
            return  $this->getResult(-1,'用户名已经被注册');
        }

        return  $this->getResult(1,'用户名尚未被注册');
    }

    function user_create(){
        $name = Input::get('name');
        $nick_name = Input::get('nick_name',$name);
        $pwd = Input::get('password');
        $sex = Input::get('sex');
        $age = Input::get('age');
        $email = Input::get('email');
        $phone = Input::get('phone');
        $address = Input::get('address');

        if(empty($name) || empty($pwd) ){
            return  $this->getResult(-1,'用户名或密码不能为空');
        }
        $user  = new ApiModel('user');
        if($user->where('name',$name)->count()){
            return  $this->getResult(-1,'昵称已经被占用');
        }
        $user ->name = $name;
        $user->nick_name = $nick_name;
        $user->password = sha1($pwd);
        $user->sex = $sex;
        $user->age = $age;
        $user->email = $email;
        $user->phone = $phone;
        $user->address = $address;
        $user->save();
        return $this->getResult(1,'',$this->process($user->toArray(),false));
          //todo mail active check

//        $active = Input::get('active',false);
//        $user->status = !$active;
//        if($active){
//            UserMessage::sendMail()
//        }

    }

    function user_update($id){
        $data = $data = json_decode(Input::get('data'), true);
        if(empty($id) || empty($data) ){
            return  $this->getResult(-1,'请输入用户ID 和要修改的数据');
        }
        $user  = ApiModel::Find('user',$id);

        if(isset($data['password']) && empty($data['password']) ){
            return  $this->getResult(-1,'密码不能为空');
        }
        if(isset($data['name'])){
            return  $this->getResult(-1,'不能修改 name');
        }
        $data['password'] = sha1($data['password']);
        try{
            $user->update($data);
        }
        catch (Exception $e) {
            \Utils\CMSLog::debug(sprintf('update user error :%s',$e->getMessage()));
            return $this->getResult(-1,'出现错误,请检查');
        }
        return $this->getResult(1,'',$this->process($user->toArray(),false));
    }

    function user_info($id){
        $user  = ApiModel::Find('user',$id);
        if($user->exists){
            return $this->getResult(1,'',$this->process($user->toArray(),false));
        }
        return $this->getResult(-1,'用户不存在');
    }
    ## endregion


    #region friends
    function friends_create(){

    }

    #endregion

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    { if($this->action == self::$ACTION_FRIENDS) {

        }
        elseif($this->action == self::$ACTION_XXX) {

        }
        else{
            $this->table_name = 'user';
            return parent::index();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        if($this->action == self::$ACTION_FRIENDS) {
            $this->friends_create();
        }
        elseif($this->action == self::$ACTION_XXX) {
            $this->user_xx_create();
        }
        else{
           return  $this->user_create();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        if($this->action == self::$ACTION_FRIENDS) {
            return $this->friends_create();
        }
        elseif($this->action == self::$ACTION_XXX) {
            return $this->user_xx_create();
        }
        else{
            return  $this->user_info($id);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        if($this->action == self::$ACTION_FRIENDS) {
            return $this->friends_create();
        }
        elseif($this->action == self::$ACTION_XXX) {
            return $this->user_xx_create();
        }
        else{
            return  $this->user_update($id);
        }
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