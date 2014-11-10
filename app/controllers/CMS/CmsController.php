<?php

use Operator\RedisKey;

class CmsController extends CmsBaeController
{

    public function index()
    {
        //suppose this action was been reflactor
        //make sure the user has access the app permission
        $user = Auth::user();
        $this->menu = 'cms.dataTable';
        $table_id = Input::get('id', '');
        $status = Input::get('status', '');
        $pageSize = Input::get('pageSize', 20);
        $pageSize = $pageSize > 1000 ? 1000 : $pageSize;

        switch (!!$table_id) {
            case true:
                $forms = Forms::where('models_id', '=', $table_id)->get();
                $table = SchemaBuilder::find($table_id);
                $children = $table->getForeignField();
                array_push($children, 'children', 'parent');
                if (!$table || !$forms)
                    App::abort(404);
                $this->menu = 'cms.table.' . $table_id;
                //调用事件
                Event::fire('widget', array($table->table_name, 'index'));
                $vm = new ApiModel($table->table_name);
                //如果是超级管理员 可以看到已经删除的数据
                $vm->setTable($table->table_name);
                $dataList = $vm->getDataList($table, $status, $pageSize);
                return $this->render('cms.table', array(
                        'table'         => $table,
                        'table_options' => $table->models_options ? json_decode($table->models_options, true) : array(),
                        'forms'         => $forms,
                        'dataList'      => $dataList,
                        'children'      => $children,
                        'options'       => $this->getOption(),
                        'status'        => $status,
                        'roles'         => $this->getRoles(),
                        'pageSize'      => $pageSize
                    )
                );
                break;
            case false:
                return $this->render('cms.index', array(
                    'timing_data'  => \Operator\ReadApi::getTimingData('+inf', true),
                    'timing_count' => \Operator\ReadApi::countZset(RedisKey::TIMING_PUB),
                    'options'      => $this->getOption(),
                    'roles'        => $this->getRoles(),
                    'user'         => $user->toArray()
                ));
                break;
        }
    }

    /**
     * 转发器
     */
    public function dispatch()
    {
        return Response::json(array(
            'code'    => -1,
            'message' => 'request not found!',
            'data'    => array()), 404);
    }

    public function destroy($id)
    {
        $table_id = Input::get('table');
        $table = SchemaBuilder::find($table_id);

        $vm = new ApiModel($table->table_name);
        $vm = $vm->newQueryWithDeleted()->find($id);
        if (!$vm->exists) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
        }

        $vm->setTable($table->table_name);
        $vm->timing_state = RedisKey::DELETED;
        $vm->processGeo = false;
        $vm->save();
        $return = $vm->delete();

        if ($return || $return === null) {
            $this->ajaxResponse(BaseController::$SUCCESS, '删除成功');
        }
        $this->ajaxResponse(BaseController::$FAILED, '删除失败');
    }

    /**
     * 恢复软删除
     * @param $tableId
     * @param $id
     */
    public function restore($tableId, $id)
    {
        $table = SchemaBuilder::find($tableId);
        if (!$table) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
        }
//            $this->ajaxResponse(array(), 'fail', '表不存在');
        $vm = ApiModel::find($table->table_name, $id);
        if (!$vm) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
        }
        $vm->oldData = $vm->toArray();
        $vm->processGeo = false;
        if ($vm->children) {
            list($field, $children_table) = explode(':', $vm->children);
            if ($ids = $vm->$field) {
                foreach ($ids as $id) {
                    $vmForeign = new ApiModel($children_table);
                    $vmForeign = $vmForeign->newQueryWithDeleted()->find($id);
                    $vmForeign->setTable($children_table);
                    $vmForeign->oldData = $vmForeign->toArray();
                    $vmForeign->processGeo = false;
                    if (isset($vmForeign->timing_state)) {
                        $vmForeign->timing_state = RedisKey::PUB_ONLINE;
                    }
                    $vmForeign->restore();
                }
            }
            if (isset($vm->timing_state)) {
                $vm->timing_state = RedisKey::PUB_ONLINE;
            }
            $vm->restore();
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS);
        } else {

            if (isset($vm->timing_state)) {
                $vm->timing_state = RedisKey::PUB_ONLINE;
            }
            $vm->restore();
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS);
        }
        $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
    }

    public function edit($id)
    {
        if (!($tableId = Input::get('table')) ||
            !($table = SchemaBuilder::find($tableId))
        )
            App::abort(404);
        //调用 事件  定向新的页面
        Event::fire('widget', array($table->table_name, 'edit'));
        $this->menu = 'cms.table.' . $tableId;

        //TODO 兼容老数据  以后删除
        $hide = $table->getForeignField();
        array_push($hide, 'user', 'children', 'parent');

        //主表单
        $forms = Forms::where('models_id', '=', $tableId)->orderBy('rank', 'desc')->get();
        $vm = new ApiModel($table->table_name);
        $table_data = $vm->newQueryWithDeleted()->find($id);

        //外链接表Form

        $children_relations = Forms::loadRelationForm($table, 'edit', $table_data);


        $data_link = DataLink::where('table_name', $table->table_name)->where('data_id', $id)->first();
        $data_info = array();
        if ($data_link && $data_link->exists) {
            $data_info = DataLinkItem::where('data_link_id', $data_link->id)->get(array('table_alias', 'table_name', 'data_id', 'table_id', 'options'));
        }
        return $this->render('cms.update', array(
            'table'              => $table,
            'tableData'          => $table_data,
            'forms'              => $forms,
            'hide'               => $hide,
            'children_relations' => $children_relations,
            'options'            => $this->getOption(),
            'data_link_info'     => $data_info,
        ));
    }

    public function online()
    {
        $table_id = Input::get('table');
        $id = Input::get('id');
        $table = SchemaBuilder::find($table_id);
        $vm = new ApiModel($table->table_name);
        $vm = $vm->newQueryWithDeleted()->find($id);
        if (!$vm->exists) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
        }
        if (!isset($vm->timing_state)) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DATA_ERROR);
        }
        $vm->setTable($table->table_name);
        $vm->rank = time();
        $vm->timing_state = RedisKey::PUB_ONLINE;
        $vm->processGeo = false;
        $return = $vm->restore();
        if ($return || $return === null) {
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS);
        }
        $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
    }

    public function  offline()
    {
        $table_id = Input::get('table');
        $id = Input::get('id');
        $table = SchemaBuilder::find($table_id);
        $vm = new ApiModel($table->table_name);
        $vm = $vm->newQueryWithDeleted()->find($id);
        if (!$vm->exists) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
        }

        if (!isset($vm->timing_state)) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DATA_ERROR);
        }
        $vm->setTable($table->table_name);
        $vm->cascadeDelete = false;
        $vm->delete();
        $vm = $vm->newQueryWithDeleted()->find($id);
        $vm->setTable($table->table_name);
        $vm->timing_state = RedisKey::READY_LINE;
        $vm->processGeo = false;
        $return = $vm->save();

        if ($return || $return === null) {
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS);
        }
        $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
    }

    public function create()
    {

        if (!($table_id = Input::get('id', ''))
            || !($table = SchemaBuilder::find($table_id))
        )
            App::abort(404);
        //调用事件
        Event::fire('widget', array($table->table_name, 'create'));
        $this->menu = 'cms.table.' . $table_id;
        //主表单Form
        $forms = Forms::where('models_id', '=', $table_id)->orderBy('rank', 'desc')->get();
        //外链接表Form
        $children_relations = Forms::loadRelationForm($table, 'update');

        return $this->render('cms.create', array(
                'forms'              => $forms,
                'table'              => $table,
                'table_id'           => $table_id,
                'children_relations' => $children_relations,
            )
        );
    }

    public function store()
    {
        if (!($tableId = Input::get('id', ''))
            || !($table = SchemaBuilder::find($tableId))
        )
            App::abort(404);

        //store 调用事件
        Event::fire('widget', array($table->table_name, 'store'));

        $tableStore = Input::get($table->table_name);
        $flag = Input::get('create_flag', 'save');
        if ($flag === 'create') {
            $tableStore['deleted_at'] = date('Y-m-d H:i:s', time());
            $tableStore['timing_state'] = RedisKey::READY_LINE;
        }
        //主表单为空时 直接跳转
        if (!array_filter(array_except($tableStore, array('geo'))))
            return Redirect::action('CmsController@index', array('id' => $tableId));

        $vm = new ApiModel($table->table_name, $tableStore);
        $children_field = $table->getForeignField();
        //设置默认值
        if ($children_field)
            $vm->setDefaultValue($children_field);

        $childStore = Input::except(array('id', $table->table_name));
        unset($childStore['create_flag']);
        if (count($childStore) >= 1)
            $vm->setChildSets($childStore);

        $vm->XSave($table, $children_field);
        return Redirect::action('CmsController@index', array('id' => $tableId));
    }

    public function update($id)
    {
        if (!($tableId = Input::get('table', ''))
            || !($table = SchemaBuilder::find($tableId))
        ) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS, '', URL::action('CmsController@index', array('id' => $tableId)));
        }

        //调用事件
        Event::fire('widget', array($table->table_name, 'update'));
        $tableStore = Input::get($table->table_name);

        if (!array_filter(array_except($tableStore, array('geo')))) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS, '', URL::action('CmsController@index', array('id' => $tableId)));
        }

        $vm = new ApiModel($table->table_name);
        $tableData = $vm->newQueryWithDeleted()->find($id);
        $tableData->oldData = $tableData->toArray();
        $tableData->setTable($table->table_name);

        $children_field = $table->getForeignField();
        //设置默认值
        if ($children_field)
            $tableData->setDefaultValue($children_field);

        $childStore = Input::except(array('id', 'table', 'link_items', $table->table_name));
        $link_items = Input::get('link_items');
        $this->mapping_data($link_items, $tableStore);

        if (count($childStore) >= 1)
            $tableData->setChildSets($childStore);

        if ($tableData->XUpdate($table, $children_field, $tableStore)) {
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('CmsController@index', array('id' => $tableId)));
        }
        $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED, '', URL::action('CmsController@index', array('id' => $tableId)));

    }

    /**
     * @param $table
     * @param $table_info
     * @param $tableStore
     */
    public function mapping_data($link_options, $tableStore)
    {
        //table_name data_id optopns

        foreach ($link_options as $table_name => $data_ids) {
            foreach ($data_ids as $data_id => $options) {
                //change data
                $mapping_data = ApiModel::find($table_name, $data_id);
                $option_filed = json_decode(htmlspecialchars_decode($options), true);
                foreach ($option_filed as $filed) {
                    $mapping_data[$filed] = $tableStore[$filed];
                }
                $mapping_data->save();
            }
        }
    }

    /**
     * CMS 内部动态页面生成
     *
     * @param $id
     */
    public function page($id)
    {

        $page = Widget::whereRaw('status=1 AND name =? AND action = ?', array($id, 'page'))->first();
        if ($page) {
            \Plugin\Widget::cast($page)->run();
            App::shutdown();
            exit();
        } else
            App::abort(404);
    }

    /**
     * 历史记录页面
     * @param $tableId
     * @param $id
     *
     * @return string
     */
    public function detail($tableId, $id)
    {
        $table = SchemaBuilder::find($tableId);
        if (!$table) App::abort(404);

        $currentVersion = new ApiModel($table->table_name);
        $currentVersion = $currentVersion->newQuery()->find($id);
        if (!$currentVersion) App::abort(404, '未找到数据');


        $this->menu = 'cms.table.' . $tableId;
        $records = Record::whereRaw('connect = ? AND table_name=? AND content_id=?', array('ApiModel', $table->table_name, $id))->paginate(10);

        return $this->render('cms.detail', array(
            'table'          => $table,
            'currentVersion' => $currentVersion
        ))->nest('record', 'record._record', array('records' => $records));
    }

    public function cacheRefresh($table, $target)
    {

        $vm = new ApiModel($table);
        if ($vm = $vm->newQuery()->find($target)) {
            \Operator\CacheController::update($table, $vm->toArray());
        }
        $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS);
//        $this->ajaxResponse(array(), 'success', '更新缓存成功', true);
    }

    public function search($table_name, $filed, $condition)
    {
        if (empty($filed) || empty($condition)) {
            return null;
        }
        $vm = new ApiModel($table_name);
        $vm->where($filed, $condition)->get();
        if ($vm->exists) {
            return $vm;
        }
        return null;
    }

    public function upload()
    {
        return $this->render('cms.upload');
    }

    public function test()
    {

        print_r(Input::all());

        return $this->render('cms.test');
    }
}