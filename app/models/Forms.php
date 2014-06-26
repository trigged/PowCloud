<?php

class Forms extends XEloquent
{

    protected $table = 'forms';

    protected $softDelete = true;

    protected $fillable = array('field', 'models_id', 'isEditable', 'rank', 'label', 'type', 'default_value', 'rules', 'isEditable', 'visibleByGroup', 'isVisible');

    public function models()
    {

        $this->belongsTo('SchemaBuilder');
    }

    public function rules()
    {

        return array(
            'default' => array(),
        );

    }

    /**
     * 根据指定的table加载指定的表单
     * @param        $table
     * @param        $field
     * @param string $type
     * @param array $dataId
     *
     * @return array
     */
    public static function loadForm($table, $field, $type = "edit", $dataId = array())
    {
        $schema = SchemaBuilder::where('table_name', '=', $table)->first();
        if (!$schema)
            return array();
        //虚拟Model
        $vm = new ApiModel($table);
        $data = array(array());
        if ($type === 'edit' && !empty($dataId)) {
            $data = array();
            foreach ($dataId as $id) {
                if ($formData = $vm->newQuery()->find($id))
                    $data [$id] = $formData;
            }
            $data = empty($data) ? array(array()) : $data;
        }

        return array(
            'table' => $schema,
            'forms' => Forms::where('models_id', '=', $schema->id)->orderBy('rank', 'desc')->get(),
            'vm'    => $vm,
            'data'  => $data,
        );
    }

    /**
     * 加载关联表单信息
     * @param SchemaBuilder $table
     * @param string $type 用于判更新表单还是创建表单
     * @param Array $tableData
     *
     * @return array
     *
     * array => array(
     *   array => (
     *      'table'=> '',
     *      'forms'=> '',
     *      'vm'   => '',
     *      'data' =>'',
     *   )
     *)
     */
    public static function loadRelationForm(SchemaBuilder $table, $type = "update", $tableData = array())
    {
        $children_field = $table->getForeignField();
        $forms = array();
        if ($children_field) {
            foreach ($children_field as $children_table => $field) {
                $dataId = empty($tableData->$field) ? array() : $tableData->$field;
                if ($ff = self::loadForm($children_table, $field, $type, $dataId))
                    $forms[$field] = $ff;
            }
        }

        return $forms;
    }

    public function fireXEloquentSavingEvent($model)
    {
        $defaultValue = $model->default_value;
        $map = array();
        if ($model->type == 'ajaxInput') {
            if (!empty($defaultValue['target'])) {
                $defaultValue['map'] = $model->formatAjaxData($defaultValue['data']);
                $defaultValue['data'] = '';
            } else {
                if (!empty($defaultValue['data'])) {
                    $data = array_slice($defaultValue['data'], 0, 1);
                    $defaultValue['map'] = $model->formatAjaxData($data);
                    $defaultValue['data'] = $data[0]['data'];
                }
            }

            $model->default_value = json_encode($defaultValue);
        }
        return true;
    }

    public function formatAjaxData($defaultValue)
    {
        $map = array();
        foreach ($defaultValue as $data) {
            foreach ($data['map'] as $m) {
                $map[$data['data']][] = implode(':', $m);
            }
        }
        return $map;
    }
}