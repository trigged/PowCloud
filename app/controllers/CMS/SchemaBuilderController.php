<?php


use Operator\ReadApi;

class SchemaBuilderController extends SystemController
{

    public $nav = 'system';

    public function index()
    {
        $this->menu = 'schema.list';
        $tables = SchemaBuilder::paginate(10);

        return $this->render('schema.list', array('tables' => $tables));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //执行创建
        Log::info(Auth::user()->name . '创建表');
        $this->menu = 'schema.create';
        Path::getPathTree();

        return $this->render('schema.create', array('pathTreeListOptions' => Path::$pathTreeListOptions));
    }

    public function store()
    {
        //设置默认值
        $input = array_merge(array('index' => null, 'update' => null, 'delete' => null, 'create' => null),
            Input::all());

        $table = new SchemaBuilder($input);

        $table->scene = 'create';
        //TODO add event for after validator
        if ((int)$table->path_id !== -1) {
            if ($value = SchemaBuilder::where('path_id', '=', $table->path_id)->orWhere('table_name', $table->table_name)->count()) {
                $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS, '路径已被绑定,请重新选择路径');
            }
        } elseif (SchemaBuilder::where('table_name', $table->table_name)->count()) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);
        }

        if ($table->save()) {
            Log::info(Auth::user()->name . '更新表' . $table->table_name);
            //set group option
            $group_id = $this->getGroupID();
            $g_option = new GroupOperation();
            $g_option->group_id = $group_id;
            $g_option->read = (int)GroupOperation::HAS_RIGHT;
            $g_option->edit = (int)GroupOperation::HAS_RIGHT;
            $g_option->models_id = (int)$table->id;
            $g_option->save();
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('SchemaBuilderController@index'));

        }

        $this->ajaxResponse(BaseController::$FAILED, json_encode($table->errors));;
    }

    /**
     * @param $id
     * input ->[oldkey]=newkey
     */
    public function update($id)
    {

        $schema = SchemaBuilder::find($id);
        $schema->scene = 'update';

        //设置默认值
        if ((int)Input::get('restful') === 0 || (int)Input::get('path_id') === -1)
            $input = array_merge(Input::all(), array('index' => null, 'update' => null, 'delete' => null, 'create' => null));
        elseif ((int)Input::get('restful') === 1)
            $input = array_merge(array('index' => null, 'update' => null, 'delete' => null, 'create' => null),
                Input::all());

        if (Input::get('path_id') != $schema->path_id && Input::get('path_id') != -1) {
            if (SchemaBuilder::where('path_id', '=', Input::get('path_id'))->get()->count() > 0) {
                $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);
            }
        }

        //rename filed
        $property = json_decode($schema->property, true);
        $rename = array();
        foreach ($property as $key => $value) {
            if (isset($input[$key]) && !empty($input[$key]) && $input[$key] !== $key) {
                $rename[$key] = $input[$key];
            }
        }

        //update property
        foreach ($rename as $oldKey => $newKey) {
            $property[$newKey] = $property[$oldKey];
            unset($property[$oldKey]);
        }

        $that = $this;

        $value = DB::transaction(function () use ($schema, $rename, $property, $that, $input, $id) {
            if (count($rename)) {
                //update db
                $table_name = $schema->table_name;
                $result = \Utils\DBMaker::reNameField($table_name, $rename);
                if ($result !== true) {
                    $that->ajaxResponse(array(), 'fail', '更新字段名称失败');
                }
                //update property
                $schema->property = json_encode($property);
                $schema->save();
                //update forms
                $forms = Forms::where('models_id', $id)->get();
                if ($forms) {
                    foreach ($forms as $form) {
                        if (isset($form->field) && isset($rename[$form->field])) {
                            $form->field = $rename[$form->field];
                            $form->save();
                        }
                    }
                }
                \Operator\CacheController::flashTable($table_name);
            }
        });

        if ($schema->update(array_except($input, array('property')))) {
            ReadApi::getTableInfo($schema->table_name, true);
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('SchemaBuilderController@index'));
        }
        $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {

        if (SchemaBuilder::find($id)->delete()) {
            $this->ajaxResponse(BaseController::$_SUCCESS_TEMPLATE);

        }
        Log::info(Auth::user()->name . '删除表' . $id);
        $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//        $this->ajaxResponse(array(), 'fail', '删除失败');

    }

    public function restore()
    {

        if (SchemaBuilder::find(Input::get('id'))->restore()) {
            $this->ajaxResponse(BaseController::$_SUCCESS_TEMPLATE);

        }
        $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//        $this->ajaxResponse(array(), 'fail', '恢复失败');
    }

    public function edit($id)
    {
        if (!$table = SchemaBuilder::find($id))
            App::abort(404);
        $this->menu = 'schema.list';
        $schema = SchemaBuilder::find($id);
        Path::getPathTree();
        Log::info(Auth::user()->name . '编辑表' . $table->table_name);
        $schema->property = json_decode($schema->property, true);

        return $this->render('schema.update', array(
                'table'               => $table,
                'schema'              => $schema,
                'pathTreeListOptions' => Path::$pathTreeListOptions)
        );
    }

    public function tableOptions($id)
    {
        $table = SchemaBuilder::find($id);
        if (!$table) App::abort(404);
        Log::info(Auth::user()->name . '设置表选项' . $table->table_name);
        $message = '';
        if (isset($_POST['options']) && $_POST['options']) {
            if (DB::table($table->getTable())->where('id', $id)->update(array('models_options' => json_encode($_POST['options'])))) {
                $table->models_options = json_encode($_POST['options']);
            }
            $message = '修改成功';
        }

        return $this->render('schema/options', array(
            'table'   => $table,
            'options' => $table->models_options ? json_decode($table->models_options, true) : array(),
            'message' => $message
        ));
    }

    /*
     * 表结构列表
     */

    public function tableSchema($id)
    {
        $table = SchemaBuilder::find($id);
        if (!$table)
            App::abort(404);

        $schema = json_decode($table->property, true);

        return $this->render('schema.schema', array(
            'table'  => $table,
            'schema' => $schema,
        ));
    }

    /**
     * 添加字段
     */
    public function addField($id)
    {
        $table = SchemaBuilder::find($id);
        if (!$table)
            App::abort(404);
        $field = Input::get('field', '');
        $property = Input::get('property', '');
        if ($field && $property) {
            $schema = json_decode($table->property, true);
            if (isset($schema[$field])) {
                $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);
//                $this->ajaxResponse(array('field' => '字段已经存在'), 'fail', '字段已经存在', '');
            }
            $schema [$field] = $table->analyzeFieldValue($property);
            $r = DB::transaction(function () use ($id, $schema, $table, $field) {
                $table_name = $table->table_name;
                if (\Utils\DBMaker::addField($table_name, array($field => $schema[$field])) === true) {
                    DB::table('models')->where('id', $id)->update(array('property' => json_encode($schema)));
                    \Operator\CacheController::flashTable($table_name);
                    return true;
                } else
                    return '添加出错,请联系管理员！';
            });
            if ($r === true) {
                $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('SchemaBuilderController@tableSchema', array('table' => $id)));
            }
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED, '', URL::action('SchemaBuilderController@tableSchema', array('table' => $id)));

        }

        return $this->render('schema.addField', array(
            'table' => $table
        ));
    }

    /**
     * 删除一个字段
     */
    public function destroyField($id, $field)
    {
        $table = SchemaBuilder::find($id);
        if (!$table) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);

        }
        $schema = json_decode($table->property, true);
        if (!isset($schema[$field])) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);

        }
        if (in_array($field, SchemaBuilder::$keyField)) {
            $this->ajaxResponse(BaseController::$FAILED, '此字段不可删除,如要删除请联系管理员');

        }
        unset($schema[$field]);
        $r = DB::transaction(function () use ($id, $schema, $table, $field) {
            $table_name = $table->table_name;
            \Utils\DBMaker::deleteField($table_name, $field);
            DB::table('models')->where('id', $id)->update(array('property' => json_encode($schema)));
            \Operator\CacheController::flashTable($table_name);
            DB::table('forms')->whereRaw('field=? AND models_id=?', array($field, $id))->delete();
            return true;
        });
        if ($r === true) {
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('SchemaBuilderController@tableSchema', array('table' => $id)));

        }
        $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED, '', URL::action('SchemaBuilderController@tableSchema', array('table' => $id)));
//        $this->ajaxResponse(array('field' => $r), 'fail', '删除失败', URL::action('SchemaBuilderController@tableSchema', array('table' => $id)));
    }

    public function docs($table)
    {
        echo \Utils\DocGenerator::getDoc($table, $this->app_id);
    }

    public function apiInfo($table)
    {
//        $this->render('schema.api_info',array());
        return $this->render('schema.api_info', array());
    }

    public function docsHtml($table)
    {

        echo '<iframe frameborder="0" width="100%" height="100%"  src="' . URL::action('SchemaBuilderController@docs', array('table' => $table)) . '"></iframe>';
    }
}