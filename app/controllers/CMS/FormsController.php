<?php

class FormsController extends SystemController
{

    public $nav = 'system';

    public function index()
    {
        $this->menu = 'forms.list';
        $table_id = Input::get('table', '');
        if (!$table_id) App::abort(404);

        $forms = Forms::where('models_id', '=', $table_id)->whereNotIn('field', array('timing_time', 'timing_state'))->orderBy('rank', 'desc')->paginate();
        $table = SchemaBuilder::find($table_id);
        if (!$forms || !$table) App::abort(404);
        $timings = Forms::where('models_id', '=', $table_id)->whereIn('field', array('timing_time', 'timing_state'))->lists('field');
        $timing_state = "开启";
        if (count($timings) == 2) {
            $timing_state = "关闭";
        }
        return $this->render('forms.list', array('forms' => $forms, 'table' => $table, 'timing_state' => $timing_state));

    }

    public function forms()
    {
        $this->menu = 'forms.list';
        $tables = SchemaBuilder::paginate(10);
        return $this->render('forms.forms', array('tables' => $tables));
    }

    public function addField($id)
    {

        $this->menu = 'forms.list';
        $table = SchemaBuilder::find($id);
        if (!$table) App::abort(404);

        return $this->render('forms.addField', array(
            'table' => $table
        ))->nest('ajaxInput', 'forms._ajaxInput', array('filed' => null));
    }

    public function storeField($id)
    {

        $modelsId = Input::get('models_id', '');
        $field = Input::get('field', '');
        if (!$modelsId && $modelsId != $id)
            App::abort(404);
        if ($field) {
            if (Forms::whereRaw('models_id=? AND field= ?', array($id, $field))->count() > 0) {
                $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);
            }
        }
        $forms = new Forms(Input::all());
        if ($forms->save()) {
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('FormsController@index', array('table' => $id)));
        }
    }

    public function create()
    {
        $this->menu = 'forms.list';
        $table_id = Input::get('table', '');
        if ($table_id) {
            $table = SchemaBuilder::find($table_id);

            if (!$table) App::abort(404);

            $vm = new ApiModel($table->table_name);

            $children_filed = $table->getForeignField();
            if ($children_filed) {
                foreach ($children_filed as $fid) {
                    array_push($vm->hiddenField, $fid);
                }
            }

            $formFields = $vm->getEditColumns();

            return $this->render('forms.create', array(
                'formFields' => $formFields,
                'table'      => $table
            ));
        }
    }

    public function store()
    {
        $timing = Input::get('timing_time', 2);
        $filed = Input::get('field', array());
        $tableId = Input::get('tableId', '');
        if ((int)$timing === 1) {
            $filed[] = array(
                'field'         => 'timing_time',
                'models_id'     => $tableId,
                'label'         => '定时发布',
//                'dataType' =>'DateTime',
                'type'          => 'dateTimePicker',
                'rules'         => '',
                'default_value' => '',
                'rank'          => 0,
            );

            $filed[] = array(
                'field'         => 'timing_state',
                'models_id'     => $tableId,
                'label'         => '定时发布',
//                'dataType' =>'DateTime',
                'type'          => 'timingState',
                'rules'         => '',
                'default_value' => '',
                'rank'          => 0,
            );

        }
        if (DB::table('forms')->insert($filed)) {
            return Redirect::action('FormsController@forms');
        }
    }

    public function rank($form)
    {
        $table = Input::get('table', '');
        $ids = Input::get('id', '');
        if (!$table || !$ids) {
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS);
//            $this->ajaxResponse(array(), 'success', '更新成功');
        }

        $idsCount = count($ids);
        try {
            DB::connection()->getPdo()->beginTransaction();
            foreach ($ids as $rankIndex => $id) {
                Forms::where('id', '=', $id)->update(array('rank' => $idsCount - $rankIndex));
            }
            DB::connection()->getPdo()->commit();
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS);
//            $this->ajaxResponse(array(), 'success', '更新成功');
        } catch (Exception $e) {
            DB::connection()->getPdo()->rollBack();
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
//            $this->ajaxResponse(array(), 'fail', '更新失败');
        }
    }

    public function edit($id)
    {
        $this->menu = 'forms.list';
        $field = Forms::find($id);
        if (!$field) App::abort(404);
        if ($field->type == 'ajaxInput' && $field->default_value) {
            $field->default_value = json_decode($field->default_value, true);
        }

        return $this->render('forms.update', array('field' => $field))
            ->nest('ajaxInput', 'forms._ajaxInput', array('filed' => $field));

    }

    public function update($id)
    {
        if (!$field = Forms::find($id))
            App::abort(404);
        if ($field->update(Input::except(array('form'))))
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('FormsController@index', array('table' => $field->models_id)));
        else {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
        }
    }

    /**
     * 删除表单项
     * @param $id
     */
    public function destroy($id)
    {
        if (Forms::find($id)->forceDelete()) {
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS);
//            $this->ajaxResponse(array(), 'success');
        }
        $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
//        $this->ajaxResponse(array(), 'fail', '删除失败');
    }

    /**
     * 恢复 表单项
     * @param $id
     */
    public function restore($id)
    {

        if (Forms::find(Input::get('id'))->restore()) {
            $this->ajaxResponse(BaseController::$_SUCCESS_TEMPLATE);
//            $this->ajaxResponse(array(), 'success');
        }
        $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//        $this->ajaxResponse(array(), 'fail', '恢复失败');
    }

    public function addTiming($models_id)
    {
        $forms = Forms::where('models_id', '=', $models_id)->whereIn('field', array('timing_time', 'timing_state'))->lists('field');
        $filed = array();
        if (!in_array('timing_time', $forms)) {
            $filed[] = array(
                'field'         => 'timing_time',
                'models_id'     => $models_id,
                'label'         => '定时发布',
                'type'          => 'dateTimePicker',
                'rules'         => '',
                'default_value' => '',
                'rank'          => 0,
            );
        }
        if (!in_array('timing_state', $forms)) {
            $filed[] = array(
                'field'         => 'timing_state',
                'models_id'     => $models_id,
                'label'         => '定时发布',
                'type'          => 'timingState',
                'rules'         => '',
                'default_value' => '',
                'rank'          => 0,
            );
        }
        if (!empty($filed)) {
            DB::table('forms')->insert($filed);
        }
        return Redirect::action('FormsController@forms');

    }

    public function delTiming($models_id)
    {
        $forms = Forms::where('models_id', '=', $models_id)->whereIn('field', array('timing_time', 'timing_state'))->lists('id', 'field');
        $destroy = array();
        if (isset($forms['timing_time'])) {
            $destroy[] = $forms['timing_time'];
        }
        if (isset($forms['timing_state'])) {
            $destroy[] = $forms['timing_state'];
        }

        if (!empty($destroy)) {
            Forms::destroy($destroy);
        }
        return Redirect::action('FormsController@forms');
    }

}