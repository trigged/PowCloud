<?php

class LimitController extends SystemController
{


    const ADMIN = 3;

    public $nav = 'limit';

    public function index()
    {
        $this->render('limit.list');
    }

    protected function  checkAdmin() {
        $group_id = Auth::user()->group_id;
        $role = Auth::user()->roles;
        $groupName = Group::find($group_id);
        $groupName = $groupName?$groupName->groupName:'';

        if ($groupName == 'admin' || $role == self::ADMIN) {
            $isAdmin = true;
        } else {
            $isAdmin = false;
        }

        return $isAdmin;
    }

    public function group()
    {
        $this->menu = 'limit.group';
        $groupModels = Group::all();

        $isAdmin = $this->checkAdmin();
        return $this->render('limit.group', array(
            'groupModels' => $groupModels,
            'isAdmin' => $isAdmin,
        ));
    }

    public function create()
    {
        $models = SchemaBuilder::all();
        return $this->render('limit.create', array('models' => $models));
    }

    public function edit($id)
    {
        $this->menu = 'limit.list';
        if (!$limit = Group::find($id))
            App::abort(404);

        $models = SchemaBuilder::all();
        $goModels = GroupOperation::where('group_id', '=', $id)->get();
        if (!$goModels && !isset($goModels)) {
            App::abort(404);
        }

        $limitData = array();
        foreach ($goModels as $goModel) {
            if(!$table = SchemaBuilder::find($goModel->models_id))
                continue;
            $table_name = $table->table_name;
            $limitData[$table_name]['read'] = $goModel->read;
            $limitData[$table_name]['edit'] = $goModel->edit;
        }

        return $this->render('limit.update', array(
            'limit'     => $limit,
            'dataModel' => $limitData,
            'models'    => $models,
        ));
    }

    public function update($id)
    {
        $this->menu = 'limit.list';
        if (!$group = Group::find($id))
            App::abort(404);

        $group_data = Input::except('limit');
        $limit_data = Input::except('group');

        if (isset($group_data['group']['groupName'])) {
            $group->groupName = $group_data['group']['groupName'];
        } else {
            $this->ajaxResponse(array(), 'fail', '不存在数组');
        }

        if (!$group->save())
            $this->ajaxResponse(array(), 'fail', '更新Group失败');

        if (!isset($limit_data['limit'])) {
            $this->ajaxResponse(array(), 'fail', '不存在limit_data[limit]数组');
        }

        foreach ($limit_data['limit'] as $key => $val) {
            $model = SchemaBuilder::where('table_name', '=', $key)->first();

            $go = GroupOperation::where('models_id', '=', $model->id)->where('group_id', '=', $group->id)->first();
            if (!$go) {
                $go = new GroupOperation();
                $go->models_id = $model->id;
            }
            $go->group_id = $group->id;
            switch ($val) {
                case 3:
                    $go->read = 2;
                    $go->edit = 2;
                    break;
                case 2:
                    $go->read = 2;
                    $go->edit = 1;
                    break;
                case 1:
                    $go->read = 1;
                    $go->edit = 1;
                    break;
            }
            if (!$go->save()) {
                $this->ajaxResponse(array(), 'fail', '保存group_operation失败！');
            }
        }

        $this->ajaxResponse(array(), 'success', '更新成功！', Url::action('LimitController@group'));
    }

    public function checkExistGroup($groupName)
    {
        $group = Group::where('groupName', '=', $groupName);
        if (!isset($group->groupName)) {
            return false;
        } else {
            return true;
        }

    }


    public function store()
    {
        $group_data = Input::except('limit');
        $limit_data = Input::except('group');

        if (!$this->checkExistGroup($group_data['group']['groupName'])) {
            $group = new Group();
            $group->groupName = $group_data['group']['groupName'];
        } else {
            return Redirect::action('LimitController@create', array('info' => '已经存在该分组'));
        }

        //保存group表
        $group = Group::where('groupName', '=', $group_data['group']['groupName']);
        if (!isset($group->groupName)) {
            $group = new Group();
            $group->groupName = $group_data['group']['groupName'];
        } else {
            return Redirect::action('LimitController@create', array('info' => '已经存在该分组'));
        }

        //保存group_operation
        if ($group->save()) {
            foreach ($limit_data['limit'] as $key => $val) {
                $go = new GroupOperation();
                $result = SchemaBuilder::where('table_name', '=', $key)->first();
                if ($result) {
                    $go->models_id = $result->id;
                }
                $go->group_id = $group->id;
                switch ($val) {
                    case 3:
                        $go->read = 2;
                        $go->edit = 2;
                        break;
                    case 2:
                        $go->read = 2;
                        $go->edit = 1;
                        break;
                    case 1:
                        $go->read = 1;
                        $go->edit = 1;
                        break;
                }
                if (!$go->save()) {
                    //todo
                    //保存失败时记录信息
                    return Redirect::action('LimitController@create', array('info' => '保存group_operation失败！'));
                }
            }
        }

        return Redirect::action('LimitController@group');
    }

    public function getGroupsArray()
    {
        $groups = Group::all();
        $groupsArray = array();
        $groupsArray['-1'] = '全部'; //need to change index, maybe cause risk


        foreach ($groups as $group) {
            $groupsArray[$group->id] = $group->groupName;
        }

        return $groupsArray;

    }

    public function user()
    {
        $this->menu = 'limit.user';
        $groupId = Input::get('group');
        $username = Input::get('username');
        if (empty($groupId) || $groupId == -1) {
            $limits = User::paginate(5);
            $groupId = -1;
        } else {
            $limits = User::where('group_id', '=', Input::get('group'))->paginate(5);
        }
        if (isset($username)) {
            $limits = User::where('name', '=', $username)->paginate(5);
        }
        return $this->render('limit.user', array(
            'limits'      => $limits,
            'isAdmin'     => $this->checkAdmin(),
            'groupsArray' => $this->getGroupsArray(),
            'group' =>$groupId,
        ));
    }

    public function handleUser($id)
    {
        if (!isset($id)) {
            app::abort(404);
        }
        $user = User::find($id);
        return $this->render('limit.editUser', array('user' => $user));
    }

    public function updateUser($id)
    {

        if (!isset($id) && $id) {
            $this->ajaxResponse(array(), 'fail', '获取id失败');
        }
        $user = User::find($id);
        $userInfo = Input::all();
        $user->name = $userInfo['name'];
        $user->mail = $userInfo['email'];
        $user->department = $userInfo['department'];
        $user->group_id = $userInfo['group_id'];
        if (!$user->save()) {
            $this->ajaxResponse(array(), 'fail', '保存失败');
        }

        $this->ajaxResponse(array(), 'success', '更新成功！', Url::action('LimitController@user'));
    }

    public function test($id)
    {
        $this->ajaxResponse(array(), 'fail', 'failure' . $id);
    }

    public function setAdmin($id)
    {
        if (isset($id)) {
            $model = User::find($id);
            $group = Group::where('groupName', '=', 'admin')->first();
            $model->group_id = $group->id;

            if ($model->save()) {
                return Redirect::action('LimitController@user');
            }
        }

        return $this->render('limit.user');
    }

    public function cancelAdmin($id)
    {

        if (isset($id)) {
            $model = User::find($id);
            $model->group_id = null;

            if ($model->save()) {
                return Redirect::action('LimitController@user');
            }
        }

        return $this->render('limit.user');


    }

    public function destroy($id)
    {
        //删除group表中数据和groupOperation表中数据
        //todo
        $group = Group::find($id);
        if ($group->groupName == 'admin') {
            $this->ajaxResponse(array(), 'fail', '不可删除管理员组');
        }
        if (Group::find($id)->delete()) {
            $models = GroupOperation::where('group_id', '=', $id)->get();
            foreach ($models as $model) {
                $model->delete();
            }

            $user = User::where('group_id', '=', $id)->first();
            $user->group_id = null;
            if ($user->save()) {
                $this->ajaxResponse(array(), 'success', '删除成功！');
            } else {
                $this->ajaxResponse(array(), 'fail', '将用户中group_id保存为null失败');
            }


        }

        $this->ajaxResponse(array(), 'fail', '删除失败！');
    }


}