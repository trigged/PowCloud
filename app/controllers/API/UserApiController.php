<?php

use Operator\CacheController;
use Operator\ReadApi;
use Operator\RedisKey;
use Operator\WriteApi;

class UserApiController extends ModelController
{

    static $ACTION_USER = 'user';

    static $ACTION_FRIENDS = 'friends';

    static $ACTION_XXX = 'xxx';

    static $USER_TABLE = 'user';

    static $FRIENDS_FLOW = 1;

    static $FRIENDS_Double_FLOW = 2;

    static $FRIENDS_UN_FLOW = -1;

    ## region user

    public function users()
    {
        $this->table_name = 'user';
        return parent::index();
    }

    public function login()
    {
        $name = Input::get('name');
        $pwd = Input::get('password');
        if (empty($name) || empty($pwd)) {
            return $this->getResult(-1, '用户名或密码不能为空');
        }
        $user = new ApiModel('user');
        $user->hidden = array('password');
        if ($user = $user->where('name', $name)->where('password', sha1($pwd))->first()) {

            return $this->getResult(1, '登陆成功', $this->process($user->toArray(), false));
        }
        return $this->getResult(-1, '登陆失败,用户名或者密码错误');
    }

    public function userNameCheck()
    {
        $name = Input::get('name');
        if (empty($name)) {
            return $this->getResult(-1, '用户名不能为空');
        }
        $user = new ApiModel('user');
        if ($user->where('name', $name)->count()) {
            return $this->getResult(-1, '用户名已经被注册');
        }

        return $this->getResult(1, '用户名尚未被注册');
    }

    public function userUpdate($id)
    {
        $data = $data = json_decode(Input::get('data'), true);
        if (empty($id) || empty($data)) {
            return $this->getResult(-1, '请输入用户ID 和要修改的数据');
        }
        //todo need chack cache first then update cache
        $user = ApiModel::Find('user', $id);
        if (!$user) {
            return $this->getResult(-1, '用户不存在');
        }
        $user->hidden = array('password');
        if (isset($data['password']) && empty($data['password'])) {
            return $this->getResult(-1, '密码不能为空');
        }
        if (isset($data['name'])) {
            return $this->getResult(-1, '不能修改 name');
        }
        $data['password'] = sha1($data['password']);
        try {
            $user->update($data);

        } catch (Exception $e) {
            \Utils\CMSLog::debug(sprintf('update user error :%s', $e->getMessage()));
            return $this->getResult(-1, '出现错误,请检查');
        }

        return $this->getResult(1, '', $this->process($user, false));
    }

    public function userInfo($id)
    {
        $user = ApiModel::APIFind('user', $id);
        if ($user) {
            return $this->getResult(1, '', $this->process($user, false));
        }
        return $this->getResult(-1, '用户不存在');
    }

    public function userCreate()
    {
        $name = Input::get('name');
        $nick_name = Input::get('nick_name', $name);
        $pwd = Input::get('password');
        $sex = Input::get('sex');
        $age = Input::get('age');
        $email = Input::get('email');
        $phone = Input::get('phone');
        $address = Input::get('address');

        if (empty($name) || empty($pwd)) {
            return $this->getResult(-1, '用户名或密码不能为空');
        }
        $user = new ApiModel('user');
        if ($user->where('name', $name)->count()) {
            return $this->getResult(-1, '昵称已经被占用');
        }
        $user->name = $name;
        $user->nick_name = $nick_name;
        $user->password = sha1($pwd);
        $user->sex = $sex;
        $user->age = $age;
        $user->email = $email;
        $user->phone = $phone;
        $user->address = $address;
        $user->save();

        return $this->getResult(1, '注册成功', $this->process($user->toArray(), false));
        //todo mail active check

//        $active = Input::get('active',false);
//        $user->status = !$active;
//        if($active){
//            UserMessage::sendMail()
//        }

    }

    public function userDelete($id)
    {

        //todo delete relations

        //todo delete xxxx

    }

    #endregion

    #region friends

    public function friends($uid)
    {
        if (!$uid) {
            return $this->getResult(-1, '请输入用户');
        }

        $data = ReadApi::zsetGet(RedisKey::sprintf(RedisKey::USER_FRIENDS, $uid), '+inf', '-inf', null, null);
        //todo paging friends
//        $data = ReadApi::zsetGet(RedisKey::sprintf(RedisKey::USER_FRIENDS,$uid),'+inf','-inf',null,$this->page,$this->count);
        //load from cache ignore default count
//        if (count($data) < $this->count) {
//            $value = CacheController::handlerPaging($this->table_name, $this->page, $this->count);
//            if ($value !== false) {
//                $data = $value;
//            }
//        }
        $this->table_name = 'user';
        $data = ReadApi::getDataByIDs($this->table_name, $data);
        return $this->getResult(1, 'success', $this->process($data));
    }

    public function friendsCreate()
    {
        $uid = Input::get("uid");
        $target_id = Input::get("target_id");
        $each = Input::get("each");

        if (!$uid || !$target_id) {
            return $this->getResult(-1, '请输入用户');
        }
        $user = ApiModel::APIFind('user', $uid);
        if (!$user) {
            return $this->getResult(-1, '用户不存在');
        }
        $target = ApiModel::APIFind('user', $target_id);
        if (!$target) {
            return $this->getResult(-1, '用户不存在');
        }
        if ($state = ReadApi::zsetCheck(RedisKey::USER_FRIENDS, $uid, $target_id)) {
            //todo check  if target flow current user ,need set state = double_flow
            return $this->getResult(-1, '已经是好友了,请不要重复添加');
        }
        $friends = new ApiModel('user_friends');
        $friends->from_id = $uid;
        $friends->target_id = $target_id;
        $friends->type = self::$FRIENDS_FLOW;
        $friends->save();
        WriteApi::zsetAdd(RedisKey::sprintf(RedisKey::USER_FRIENDS, $uid), self::$FRIENDS_FLOW, $target_id);
        if ($each) {
            $friends = new ApiModel('user_friends');
            $friends->from_id = $target_id;
            $friends->target_id = $uid;
            $friends->type = self::$FRIENDS_Double_FLOW;
            $friends->save();
            WriteApi::zsetAdd(RedisKey::sprintf(RedisKey::USER_FRIENDS, $target_id), self::$FRIENDS_FLOW, $uid);
        }
        return $this->getResult(1, '添加成功');
    }

    public function friendsDelete()
    {
        $uid = Input::get("uid");
        $target_id = Input::get("target_id");
        $each = Input::get("each");

        if (!$uid || !$target_id) {
            return $this->getResult(-1, '请输入用户');
        }
        $user = ApiModel::APIFind('user', $uid);
        if (!$user) {
            return $this->getResult(-1, '用户不存在');
        }
        $target = ApiModel::APIFind('user', $target_id);
        if (!$target) {
            return $this->getResult(-1, '用户不存在');
        }
        if (!ReadApi::zsetCheck(RedisKey::USER_FRIENDS, $uid, $target_id)) {
            //todo check  if target flow current user ,need set state = double_flow
            return $this->getResult(-1, '取消成功,请不要重复提交');
        }
        $friends = new ApiModel('user_friends');
        $friends->from_id = $uid;
        $friends->target_id = $target_id;
        $friends->type = self::$FRIENDS_UN_FLOW;
        $friends->save();
        WriteApi::zsetRem(RedisKey::sprintf(RedisKey::USER_FRIENDS, $uid), $target);
        if ($each) {
            $friends = new ApiModel('user_friends');
            $friends->from_id = $target;
            $friends->target_id = $uid;
            $friends->type = self::$FRIENDS_UN_FLOW;
            $friends->save();
            WriteApi::zsetRem(RedisKey::sprintf(RedisKey::USER_FRIENDS, $target_id), $uid);
        }
        return $this->getResult(1, '取消成功');
    }

    ## endregion

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $uid = Input::get('uid');
        if (!$uid) {
            return $this->getResult(-1, '请传入uid 参数');
        }
        //user_like->user_user_like
        $table_name = UserBehaviorController::getBehaviorName($this->table_name);
        $data = ReadApi::getUserBehavior($table_name, $uid);
        if (count($data) < $this->count) {
            $value = CacheController::handlerUserPaging($table_name, $uid, $this->page, $this->count);
            if ($value !== false) {
                $data = $value;
            }
        }


        $data = ReadApi::getDataByIDs($this->table_name, $data);
        return $this->getResult(1, 'success', $this->process($data));
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
        $format = $this->format;
        $this->format = 'plan';

        if (!$user = $this->getUser()) {
            return $this->getResult(-1, '用户不存在');
        }
        $flag = Input::get('flag');
        $data_id = Input::get('data_id');
        $behavior_name = UserBehaviorController::getBehaviorName($this->table_name);
        $model = new ApiModel($behavior_name);
        if ($flag) {
            $count = $model->where('uid', $user['id'])->where('data_id', $data_id)->count();
            if ($count > 0) {
                return $this->getResult(-1, '操作成功,请不要重复添加');
            } else {
                $model->data_id = $data_id;
                $model->uid = $user['id'];
                $model->user = $user->nick_name;
                $model->cache_flag = false;
                $model->save();
                //WriteApi::addUserBehavior($table_name, $uid, $data_id, $rank);  1 user_user_video::1
                WriteApi::addUserBehavior($behavior_name, $user['id'], $model->id, $model->rank);
                return $this->getResult(1, '操作成功', $this->process($model->toArray(), false));
            }
        } else {
            $result = parent::store(true);

            \Utils\CMSLog::debug("#######");
            $this->format = $format;
            if ($result['code'] == 1) {
                $model->data_id = $result['data']['id'];
                $model->uid = $user['id'];
                $model->user = $user->nick_name;
                $model->cache_flag = false;
                $model->save();
                WriteApi::addUserBehavior($behavior_name, $user['id'], $model->id, $model->rank);
            }
            return $this->getResult($result['code'], $result['message'], $result['data']);
        }
    }

    function getUser()
    {
        $uid = Input::get('uid');
        return ApiModel::APIFind('user', $uid);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {

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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = $this->getUser();
        $data_id = Input::get('data_id');
        if (!$user) {
            return $this->getResult(-1, '用户不存在');
        }
        $data = ApiModel::Find($this->table_name, $data_id);
        if (!$data) {
            return $this->getResult(-1, '数据不存在');
        }
        $data->delete();


    }

}