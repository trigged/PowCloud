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
        $app_ids = ATURelationModel::where('user_id', $user->id)->lists('app_id');
        \Utils\CMSLog::debug(sprintf('user :%s, app_ids:%s', $user->name, json_encode($app_ids)));

        Session::put($this->allow_app_id_key, $app_ids);
        $apps = null;
        if (!count($app_ids) == 0) {

            $apps = AppModel::whereIn('id', $app_ids)->get();

        }

        return View::make('dashboard.index', array('models' => $apps, 'enable' => $user->status))
            ->nest('header', 'dashboard.header')
            ->nest('footer', 'dashboard.footer');
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
        $user_ids = Input::get('user_ids');

        if (empty($app_id)) {
            $this->ajaxResponse(array(), 'fail', 'app_id 为空');
        }
        if (empty($user_ids)) {
            //$this->ajaxResponse(array(), 'success', 'app_id 为空');
            $this->ajaxResponse(array(), 'fail', 'user_ids 为空');
        }

        foreach ($user_ids as $user_id) {
            $atu = new ATURelationModel();
            $atu->user_id = $user_id;
            $atu->app_id = $app_id;

            if (!$atu->save()) {
                $this->ajaxResponse(array(), 'fail', '不能保存');
            }
        }

        $this->ajaxResponse(array(), 'success', '保存成功', 'DashBoardController@index');
    }

    public function delete()
    {

        $app_id = Input::get('app_id');
        if (empty($app_id)) {
            $this->ajaxResponse(array(), 'fail', 'app_id is empty', 'DashBoardController@index');

        }
        $user_id = Input::get('user_id');
        if (empty($user_id)) {
            $this->ajaxResponse(array(), 'success', 'user_id is empty', 'DashBoardController@index');

        }

        $model = ATURelationModel::where('app_id', '=', $app_id)->where('user_id', '=', $user_id)->first();
        if (isset($model)) {
            $model->delete();
            $this->ajaxResponse(array(), 'success', 'delete成功', 'DashBoardController@index');
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

        $app = new AppModel();
        $appName = Input::get('name');
        $appInfo = Input::get('info');
        $error = '';
        if ($appName) {
            DB::connection('base')->beginTransaction();
            try {
                //保存应用
                $app->Author = Auth::user()->name;
                $app->user_id = Auth::user()->id;
                $app->name = $appName;
                $app->info = $appInfo;
                $app->save();
                //保存对应关系
                $atuRelation = new ATURelationModel();
                $atuRelation->app_id = $app->id;
                $atuRelation->user_id = $app->user_id;
                $atuRelation->roles = 3;
                $atuRelation->save();
                DB::connection('base')->commit();
                $result = \Utils\DBMaker::createDataBase(\Utils\AppChose::getDbModelsName($app->id));
                if ($result !== true) {
                    $this->ajaxResponse(array(), 'fail', $result, Url::action('DashBoardController@index'));
                }
                $result = \Utils\DBMaker::createDataBase(\Utils\AppChose::getDbDataName($app->id), true);
                if ($result !== true) {
                    $this->ajaxResponse(array(), 'fail', $result, Url::action('DashBoardController@index'));
                }

                $this->ajaxResponse(array(), 'success', '添加应用' . $app->name . '成功', Url::action('DashBoardController@index'));
            } catch (\Exception $e) {
                DB::connection('base')->rollBack();
                $this->ajaxResponse(array(), 'fail', $e->getMessage(), Url::action('DashBoardController@index'), Url::action('DashBoardController@index'));
            }
        } else {
            $error = '请填写应用名称';
        }
        $this->ajaxResponse(array(), 'fail', $error, Url::action('DashBoardController@index'), Url::action('DashBoardController@index'));
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