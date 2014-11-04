<?php

class DataLinkController extends CmsBaeController
{

    public $menu = 'data.link';

    public $template_start = ' <div class="dropdown" >  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu"
    style="display: block; position: static; margin-bottom: 5px; *width: 180px;">
    <a class="close" style="color: red ; opacity: 0.6;" href="javascript:void(0)" data-value="-1"  onclick="deleteItem(this)" >&times;</a>
    <li  data-value="table_name"><a tabindex="-1" href="%s" target="_blank">%s(点击查看详情)</a></li>
    <li class="divider"></li>';

    public $template_li = '<li><a tabindex="-1"  href="javascript:void(0)">%s(点击删除同步此字段)</a>
    <input type="hidden" name="link_items[%s][%s][]" value="%s">
    </li>';

    public $template_end = ' </ul></div>';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $data_change = DataLink::paginate();
        return $this->render('DataLink.index', array('data_link' => $data_change));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return $this->render('DataLink.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $table_name = Input::get('table_name');
        $data_id = Input::get('data_id');
        if ($table_name == null || $data_id == null) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
//            $this->ajaxResponse('', 'fail', '请输入关键字');
        }
        if (!SchemaBuilder::where('table_name', $table_name)->exists()) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
//            $this->ajaxResponse('', 'fail', '表名输入错误');
        }
        if (DataLink::where("table_name", $table_name)->where('data_id', $data_id)->count() >= 1) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);
//            $this->ajaxResponse('', 'fail', '关键字已经存在了');
        }
        $link = new DataLink();
        $link->table_name = $table_name;
        $link->data_id = $data_id;
        if ($link->save()) {
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$SUCCESS, '', URL::action('DataLinkController@index'));
//            $this->ajaxResponse(array(), 'success', '创建字关键变更成功', URL::action('DataLinkController@index'));
        }
        $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
//        $this->ajaxResponse(array(), 'fail', '数据保存失败');
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

        $data_link = DataLink::find($id);
        if (!$data_link->exists) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
//            $this->ajaxResponse('', 'fail', '数据不存在');
        }
        $items = DataLinkItem::where("data_link_id", $id)->get(array('table_id', 'table_alias', 'data_id', 'table_name', 'id', 'options'));
        return $this->render('DataLink.edit', array(
            'data_link' => $data_link,
            'items'     => $items
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * link_items[table_name][data_id] = array(filed1, filed2,...)
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $link_items = Input::get('link_items');
        if (!DataLink::find($id)->exists) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
//            $this->ajaxResponse('', 'fail', '数据不存在');
        }
        $info = '';
        foreach ($link_items as $table_name => $data) {
            foreach ($data as $data_id => $fileds) {
                if (isset($fileds['id']) && $item_id = $fileds['id']) {
                    $data_link_item = DataLinkItem::find($item_id);
                    if ($data_link_item->exists) {
                        unset($fileds['id']);
                        $data_link_item->options = json_encode($fileds);
                        $data_link_item->save();
                        continue;
                    }
                }
                if (ApiModel::find($table_name, $data_id)->exists) {
                    $table_info = \Operator\ReadApi::getTableInfo($table_name);
//                    DataLinkItem::find();
                    $data_link_item = new DataLinkItem();
                    $data_link_item->data_link_id = $id;
                    $data_link_item->table_id = $table_info['id'];
                    $data_link_item->table_name = $table_info['table_name'];
                    $data_link_item->table_alias = $table_info['table_alias'];
                    $data_link_item->data_id = $data_id;
                    $data_link_item->options = json_encode($fileds);
                    $data_link_item->save();
                } else {
                    $info .= ' ' . $table_name . '找不到: ' . $data_id . '的记录';
                }
            }
        }

        $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('DataLinkController@index'));
//        $this->ajaxResponse(array(), 'success', '创建字关键变更成功' . $info, URL::action('DataLinkController@index'));
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

    public function addLinkItem()
    {

    }

    public function checkMappingItem()
    {
        $master_table_name = Input::get('master_table');
        $mapping_table_name = Input::get('mapping_table');
        $mapping_data_id = Input::get('mapping_id');

        if (!$mapping_table_name || !$mapping_data_id) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
//            $this->ajaxResponse(array(), 'error', '参数错误');
        }

        $mapping_data = ApiModel::find($mapping_table_name, $mapping_data_id);
        if (!$mapping_data || !$mapping_data->exists) {
            //todo return data missing
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
//            $this->ajaxResponse(array(), 'error', '子数据不存在');
        }
        $master_table = SchemaBuilder::getByTableName($master_table_name);
        $mapping_table = SchemaBuilder::getByTableName($mapping_table_name);
        if (!$master_table || !$master_table->exists || !$mapping_table) {
            //todo return data missing
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
//            $this->ajaxResponse(array(), 'error', '子表不存在');
        }
        $master_property = json_decode($master_table->property, true);
        $model_property = json_decode($mapping_table->property, true);
        $mapping_property = '';
        $same_key = array_keys(array_intersect_key($master_property, $model_property));
        foreach ($same_key as $key) {
            if (isset($master_property[$key][0]) && isset($model_property[$key][0]) &&
                $master_property[$key][0] == $model_property[$key][0]
            ) {
                $mapping_property .= sprintf($this->template_li, $key, $mapping_table_name, $mapping_data_id, $key);
            }
        }
        $html = sprintf($this->template_start, URL::action('CmsController@edit', array('table' => $mapping_table->id, 'cms' => $mapping_data_id)), $mapping_table->table_alias . '.' . $mapping_data_id)
            . $mapping_property . $this->template_end;
        $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_SUCCESS, $html);
//        $this->ajaxResponse($html, 'success', '子数据不存在');

    }

    public function deleteItem()
    {
        $item_id = Input::get('id');

        try {
            if ($item_id != -1) {
                DataLinkItem::destroy($item_id);
            }

            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_SUCCESS);
//            $this->ajaxResponse('', 'success', '删除成功');
        } catch (\Exception $e) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
//            $this->ajaxResponse('', 'success', '删除失败');
        }


    }

}