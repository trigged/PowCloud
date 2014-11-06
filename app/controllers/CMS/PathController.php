<?php

class PathController extends SystemController
{

    public $nav = 'system';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        Path::getPathTree();
        return $this->render('path.list', array('pathTree' => json_encode(Path::$pathTree)));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return View::make('path.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        if (!Input::get('name', '')) {
            echo json_encode(array('error' => 'path不为空'));
            die();
        }

        $path = new Path(Input::all());
        $path->type = 1;
        if (strstr($path->name, $path->parent) === false) {
            $parent = $path->parent === '/' ? '' : $path->parent;
            $path->name = $parent . '/' . trim($path->name, '/');
        }

        if (Path::where('name', '=', $path->name)->count() > 0) {
            echo json_encode(array('error' => 'path已经存在'));
            die();
        }
        $path->last_name = trim(str_replace($path->parent, '', $path->name), '/');
        if ($path->save()) {
            $infoUrl = URL::action('PathController@index', array('path' => $path->id));
            $path = $path->toArray();
            $path['infoUrl'] = $infoUrl;
            echo json_encode($path);
        } else
            echo json_encode(array());
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $path = (int)$id === 0 ? (object)Path::$pathRoot : Path::find($id);
        if (!$path)
            return '';
        return View::make('path.show', array('path' => $path));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $path = (int)$id === 0 ? (object)Path::$pathRoot : Path::find($id);
        if (!$path)
            return '';

        return View::make('path.update', array('path' => $path));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $path = (int)$id === 0 ? '/' : Path::find($id);
        if (!$path) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
//            $this->ajaxResponse(array(), 'fail', '更新的path不存');
        }
        if ($path === '/' || $path->update(array_except(Input::all(), array('id')))) {
            $pathName = $path === '/' ? '/' : $path->name;
            $expire = $path === '/' ? Input::get('expire', '0') : $path->expire;
            RouteManager::updateRoute($pathName, $expire);
            $this->ajaxResponse(BaseController::$_SUCCESS_TEMPLATE);
//            $this->ajaxResponse(array(), 'success', '更新path成功');
        }

    }

    /**
     * 暂时不用
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
//	public function destroy($id)
//	{
//		$path = Path::find($id);
//        if($path){
//            if(DB::table('path')->whereRaw("parent REGEXP '^".$path->name."/|^".$path->name."$' or id=".$id)
//                ->update(array('deleted_at'=>date('Y-m-d H:i:s',time())))){
//
//                $this->ajaxResponse($path->toArray(),'success');
//            }
//        }
//
//        return '';
//	}

}