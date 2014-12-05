<?php


class DashBoardController extends MasterController
{

    public $nav = 'advanced';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $user = Auth::user();
        $app_ids = ATURelationModel::where('user_id', $user->id)->lists('roles', 'app_id');
        \Utils\CMSLog::debug(sprintf('user :%s, app_ids:%s', $user->name, json_encode($app_ids)));

        Session::put($this->allow_app_id_key, $app_ids);
        $apps = null;
        if (!count($app_ids) == 0) {
            $apps = AppModel::whereIn('id', array_keys($app_ids))->get();
        }


        return View::make('dashboard.index', array('apps' => $apps, 'appIds' => $app_ids, 'enable' => $user->status))
            ->nest('header', 'dashboard.header')
            ->nest('footer', 'dashboard.footer');
    }


    public function testDB()
    {
        $host = Input::get('host');
        $name = Input::get('name');
        $pwd = Input::get('pwd');
        try {
            $connection = mysqli_connect($host, $name, $pwd);
            if (mysqli_connect_errno() || !$connection) {
//                return false;
                BaseController::ajaxResponse(BaseController::$_FAILED_TEMPLATE);
            } else {
                BaseController::ajaxResponse(BaseController::$_SUCCESS_TEMPLATE);
            }
        } catch (Exception $e) {
            BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED, $e->getMessage());

        }
    }

    public function addMember()
    {

        $app_id = Input::get('app_id');
        $user_id = Input::get('user_id');
        $user_ids = ATURelationModel::where('app_id', $app_id)->lists('user_id');
        $app = AppModel::find($app_id);


        if (empty($app_id))
            return Response::view('errors.403', array(), 403);


        return Response::json(array(
            'addMembers' => View::make('dashboard.addMember', array('users' => $user_ids, 'app' => $app, 'user_id' => $user_id))->render(),

        ));
    }

    public function editApp()
    {

        $app_id = Input::get('app_id');
        if (empty($app_id)) {
            return Response::view('errors.403', array(), 403);
        }
        $app = AppModel::find($app_id);

        return View::make('dashboard.editApp', array('app' => $app))
            ->nest('header', 'dashboard.header')
            ->nest('footer', 'dashboard.footer');

    }

    public function storeMember()
    {
        $app_id = Input::get('app_id');
        $user_id = Input::get('user_id');

        if (empty($app_id)) {
            BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);

        }
        if (empty($user_ids)) {
            BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
        }
        if (ATURelationModel::where('user_id', $user_id)->where('app_id', $app_id)->count() > 0) {
            BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);
        }
        $atu = new ATURelationModel();
        $atu->user_id = $user_id;
        $atu->app_id = $app_id;
        if ($atu->save()) {
            BaseController::ajaxResponse(BaseController::$_SUCCESS_TEMPLATE);
        }
        BaseController::ajaxResponse(BaseController::$_FAILED_TEMPLATE);
    }

    public function delete()
    {

        $app_id = Input::get('app_id');
        if (empty($app_id)) {
            BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS, '', 'index');
        }
        $user_id = Input::get('user_id');
        if (empty($user_id)) {
            BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS, '', 'index');
        }

        $model = ATURelationModel::where('app_id', '=', $app_id)->where('user_id', '=', $user_id)->first();
        if (isset($model)) {
            $model->delete();
            BaseController::ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', 'index');
        }

    }

    public function addApp()
    {
        return View::make('dashboard.addApp')
            ->nest('header', 'dashboard.header')
            ->nest('footer', 'dashboard.footer');
    }

    public function storeApp()
    {
////        ALTER TABLE `x_cms`.`app`   ADD COLUMN `type` VARCHAR(45) NULL AFTER `user_id`, ADD COLUMN `config` VARCHAR(200) NULL AFTER `type`;
        $app = new AppModel();
        $appName = Input::get('name');
        $appInfo = Input::get('info');
        $type = Input::get('database');
        $host = Input::get('host');
        $name = Input::get('name');
        $password = Input::get('password');


        $error = '';
        if ($appName) {
            DB::connection('base')->beginTransaction();
            try {
                //保存应用
                $app->Author = Auth::user()->name;
                $app->user_id = Auth::user()->id;
                $app->name = $appName;
                $app->info = $appInfo;
                $app->type = $type;
                $app->config = json_encode(array('name' => $name, 'host' => $host, 'password' => $password));

                $app->save();
                //保存对应关系
                $atuRelation = new ATURelationModel();
                $atuRelation->app_id = $app->id;
                $atuRelation->user_id = $app->user_id;
                $atuRelation->roles = 3;
                $atuRelation->save();
                DB::connection('base')->commit();

                if ($type == 'self') {
                    //todo change to self db


                    $result = \Utils\DBMaker::createSelfDataBase(\Utils\AppChose::getDbModelsName($app->id));
                    if ($result !== true) {
                        BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED, $result, 'index');
                    }
                    $result = \Utils\DBMaker::createSelfDataBase(\Utils\AppChose::getDbDataName($app->id), true);
                    if ($result !== true) {
                        BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED, $result, 'index');
                    }
                    /*todo  add new conf in setting


                     call db::name::reconnect
                    then get connection pdo and run init sql
                    set app_id:db_config

                     * */


//                    \DB::connection('base')->getPdo()->exec("");
//                    \Utils\AppChose::updateConf($id);
//                    DB::reconnect()
                } else {
                    $result = \Utils\DBMaker::createDataBase(\Utils\AppChose::getDbModelsName($app->id));
                    if ($result !== true) {
                        BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED, $result, 'index');
                    }
                    $result = \Utils\DBMaker::createDataBase(\Utils\AppChose::getDbDataName($app->id), true);
                    if ($result !== true) {
                        BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED, $result, 'index');
                    }
                }

                BaseController::ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', 'index');
            } catch (\Exception $e) {
                DB::connection('base')->rollBack();
                BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED, $e->getMessage(), 'index');
            }
        } else {
            $error = '请填写应用名称';
        }
        BaseController::ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED, $error, 'index');
    }

    public function updateApp()
    {
        $app_id = Input::get('app_id');
        if (empty($app_id)) {
            return Response::view('errors.403', array(), 403);
        }

        $app = AppModel::find($app_id);
        $post_data = array_except($_POST, 'app_id');
        if (!empty($post_data)) {
            $app->name = Input::get('name');
            $app->info = Input::get('info');
        }

        if (!$app->save()) {
            return Response::view('errors.403', array(), 403);
        }
        \Utils\Env::messageTip('nessageTip', 'success', '修改应用成功');
        return Redirect::action('DashBoardController@index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
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


}