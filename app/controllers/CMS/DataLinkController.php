<?php

class DataLinkController extends CmsBaeController
{


    public $menu = 'data.link';

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
            $this->ajaxResponse('', 'fail', '请输入关键字');
        }
        if (!SchemaBuilder::where('table_name', $table_name)->exists()) {
            $this->ajaxResponse('', 'fail', '表名输入错误');
        }
        if (DataLink::where("table_name", $table_name)->where('data_id', $data_id)->count() >= 1) {
            $this->ajaxResponse('', 'fail', '关键字已经存在了');
        }
        $link = new DataLink();
        $link->table_name = $table_name;
        $link->data_id = $data_id;
        if ($link->save()) {
            $this->ajaxResponse(array(), 'success', '创建字关键变更成功', URL::action('DataLinkController@index'));
        }
        $this->ajaxResponse(array(), 'fail', '数据保存失败');
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
            $this->ajaxResponse('', 'fail', '数据不存在');
        }
        $items = DataLinkItem::where("data_link_id", $id)->get(array('table_id', 'table_alias', 'data_id', 'table_name', 'id'));
        return $this->render('DataLink.edit', array(
            'data_link' => $data_link,
            'items'     => $items
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
        $link_items = Input::get('link_items');
        if (!DataLink::find($id)->exists) {
            $this->ajaxResponse('', 'fail', '数据不存在');
        }
        $info = '';
        foreach ($link_items as $item) {
            list($table_name, $data_id) = explode(':', $item);
            if (!empty($table_name) && !empty($data_id)) {
                $table_info = \Operator\ReadApi::getTableInfo($table_name);
                if ($table_info) {
                    if (ApiModel::find($table_name, $data_id)->exists) {
                        $data_link_item = new DataLinkItem();
                        $data_link_item->data_link_id = $id;
                        $data_link_item->table_id = $table_info['id'];
                        $data_link_item->table_name = $table_info['table_name'];
                        $data_link_item->table_alias = $table_info['alias'];
                        $data_link_item->data_id = $data_id;
                        $data_link_item->save();
                    } else {
                        $info .= ' ' . $table_name . '找不到: ' . $data_id . '的记录';
                    }
                } else {
                    $info .= ' 找不到 :' . $table_name . '的表';
                }
            }
        }

        $this->ajaxResponse(array(), 'success', '创建字关键变更成功 ,部分失效数据不会加入记录' . $info, URL::action('DataLinkController@index'));
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