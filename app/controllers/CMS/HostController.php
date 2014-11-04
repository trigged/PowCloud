<?php

use Utils\CreateVirtualHost;

class HostController extends SystemController
{

    public $nav = 'system';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->menu = 'host.list';
        $hosts = Host::paginate(10);
        return $this->render('host.list', array('hosts' => $hosts));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->menu = 'host.create';
        return $this->render('host.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $host = new Host(Input::all());
        $host->scene = 'create';
        $host->user_id = Auth::user()->id; //TODO 后期 放到saving 事件中
        $domain_regex = '/(\w+)\.(com|cn|net)/';
        preg_match($domain_regex, $host->host, $result);
        if ($result) {
            if ($host->validator() && CreateVirtualHost::createHost($result[1])) {
                if ($host->save()) {
                    $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('HostController@index'));
//                    $this->ajaxResponse(array(), 'success', '添加成功', URL::action('HostController@index'));
                }
            }

            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);
//            $this->ajaxResponse($host->errors, 'fail', '主机目录创建失败可能已存在,请联系管理员');
        }
        $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//        $this->ajaxResponse(array('host' => '主机格式不对,请认真填写主机格式'), 'fail', '主机格式不对,请认真填写主机格式');
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
        $this->menu = 'host.list';
        if (!$host = Host::find($id))
            App::abort(404);

        return $this->render('host.update', array(
            'host' => $host
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        if (!$host = Host::find($id))
            App::abort(404);

        $host->scene = 'update'; //设置模型 场景

        if (($name = Input::get('name', '')) && $name !== $host->name) {
            $host->scene = 'updateName';
        }
        if ($host->update(Input::except(array('host')))) {
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('HostController@index'));
            $this->ajaxResponse(array(), 'success', '更新成功', URL::action('HostController@index'));
        } else {
            $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//            $this->ajaxResponse($host->errors, 'fail', '更新主机失败');
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

        if (Host::find($id)->delete()) {
            $this->ajaxResponse(BaseController::$_SUCCESS_TEMPLATE);
//            $this->ajaxResponse(array(), 'success');
        }
        $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//        $this->ajaxResponse(array(), 'fail', '删除失败');

    }

    public function restore($id)
    {

        if (Host::find(Input::get('id'))->restore()) {
            $this->ajaxResponse(BaseController::$_SUCCESS_TEMPLATE);
//            $this->ajaxResponse(array(), 'success');
        }
        $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//        $this->ajaxResponse(array(), 'fail', '恢复失败');
    }

}