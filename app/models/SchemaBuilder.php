<?php
use Utils\DBMaker;

/**
 * @property mixed restful
 * @property mixed user_id
 * @property mixed table_name
 * @property mixed index
 * @property mixed update
 * @property mixed create
 * @property mixed delete
 * @property mixed path_id
 */
class SchemaBuilder extends XEloquent
{


    protected $table = 'models';

    protected $fillable = array('models_options', 'table_name', 'table_alias', 'path_id', 'restful', 'property', 'index', 'update', 'create', 'delete', 'group_name');

    protected $guarded = array('id');

    protected $softDelete = true;

    public static $keyField = array('id', 'created_time', 'deleted_time', 'timing_state', 'timing_time');

    public function rules()
    {

        return array(
            'default' => array(
                'table_name'  => 'required|unique:models|regex:#[a-zA-z0-9_-]#',
                'table_alias' => 'required|unique:models',
                'path_id'     => 'required',
                'property'    => 'required',
            ),
            'update'  => array(
                'table_name'  => '',
                'table_alias' => 'required',
                'path_id'     => '',
                'property'    => 'required',
            ),
        );

    }

    public static function boot()
    {
        parent::boot();

        self::saved(function ($model) {
            $model->addRoute($model);
        });
    }

    //如果restful是1则加入api路由
    public function addRoute(Eloquent $model)
    {
        $path_id = (int)$model->path_id === -1 ? (empty($model->oldData['path_id']) ? $model->path_id : $model->oldData['path_id']) : $model->path_id;

        if ((int)$path_id !== -1) {
            $path = Path::find($path_id);
            $pathName = $path ? $path->name : '/';
            $pathExpire = $path ? $path->expire : 0;
            //todo before add check path has sub
            RouteManager::addRouteWithRestFul($pathName, $model->table_name, $pathExpire, array(
                    'index'  => (int)$model->index,
                    'store'  => (int)$model->create,
                    'update' => (int)$model->update,
                    'delete' => (int)$model->delete)
            );
        }
    }

    public function messages()
    {
        return array(
            'table_name.unique'  => '表已存在',
            'table_alias.unique' => '表别名已存在',
            'path_id.unique'     => '路径已经被绑定,请选择其它路径',
            'table_name.regex'   => '表名只能由字母、数字、下划线组成'
        );
    }

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function path()
    {

        return $this->belongsTo('Path');
    }

    public function forms()
    {

        return $this->hasMany('Forms', 'models_id');
    }

    /**
     * 变换表单数据格式
     */
    public function transFormProperty()
    {

        if (!$this->property) {
            $this->property = array();
            return json_encode($this->property);
        }

        if (is_string($this->property) && json_decode($this->property) !== null) {

            return $this->property;
        }
        $tmpProperty = array();
        foreach ($this->property as $property) {
            if ($property['name'] && $property['attributes']) {
                $tmpProperty [$property['name']] = $this->analyzeFieldValue($property['attributes']);
            }
        }
        return $this->property = json_encode($tmpProperty);
    }

    public function analyzeFieldValue($fieldValue)
    {
        $result = explode('|', $fieldValue);
        $default = null;
        if (count($result) === 2) {
            $fieldValue = $result[0];
            $default = $result[1];
        }
        $fieldValue = explode(' ', $fieldValue);
        if ($default) {
            $fieldValue['default'] = $default;
        }

        return $fieldValue;
    }

    public function fireXEloquentSavingEvent($model)
    {
        $model->user_id = Auth::user()->id;
        if ($model->scene === 'create') {
            $model->transFormProperty();
            $pro = json_decode($model->property, true);
            if (empty($pro['id'])) {
                $pro['id'] = array('increments');
            }
            if (empty($pro['rank'])) {
                $pro['rank'] = array('integer');
            }
            if (empty($pro['deleted_at'])) {
                $pro['deleted_at'] = array('dateTime');
            }
            if (empty($pro['updated_at'])) {
                $pro['updated_at'] = array('dateTime');
            }
            if (empty($pro['created_at'])) {
                $pro['created_at'] = array('dateTime');
            }
            if (empty($pro['user_name'])) {
                $pro['user_name'] = array('string');
            }
            if (empty($pro['timing_state'])) {
                $pro['timing_state'] = array('smallInteger', 'default' => \Operator\RedisKey::PUB_ONLINE);
            }
            if (empty($pro['timing_time'])) {
                $pro['timing_time'] = array('dateTime');
            }
            $result = DBMaker::createTable($model->table_name, $pro);
            if ($result !== true) {
                $model->errors['table_name'] = $result;
                return false;
            }

            DBMaker::addIndex($model->table_name, 'id');
            DBMaker::addIndex($model->table_name, 'rank');
            DBMaker::addIndex($model->table_name, 'timing_state');
            return true;
        }
        return true;
    }

    public function getForeignField()
    {
        $property = json_decode($this->property, true);
        $children_field = array();
        if (isset($property['children']) && !empty($property['children']['default'])) {
            $children_array = explode(',', $property['children']['default']);
            foreach ($children_array as $children) {
                list($filed, $table) = explode(':', $children);
                $children_field[$table] = $filed;
            }
        }

        return $children_field;
    }
}