<?php
use Operator\CacheController;
use Operator\RedisKey;
use Operator\WriteApi;


/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/19/13
 * Time: 10:53 AM
 * To change this template use File | Settings | File Templates.
 */

class ApiModel extends Eloquent
{


    public $timestamps = true;

    public $rankTail = 0;

    public $disableEvent = false;

    public $cascadeDelete = true;

    public $processGeo = true;

    public $childSets = array();

    /**
     * 保存更新之前的数据
     * @var array
     */
    public $oldData = array();

    public $hiddenField = array('id', 'rank', 'timing_state', 'timing_time', 'user_name', 'parent', 'foreign', 'created_at', 'updated_at', 'deleted_at');

    protected $table = null;

    protected $connection = 'models';

    protected $guarded = array('id', 'foreign', 'parent');

    protected $softDelete = true;

    public function __construct($name = null, array $attributes = array())
    {
        if ($name === null)
            throw new InvalidArgumentException('请设置table_name');
        $this->table = $name;

        parent::__construct($attributes);
    }

    public static function boot()
    {
        parent::boot();

        self::saving(function ($model) {
            if ($model->disableEvent === true)
                return true;

            if (isset($model->timing_time) && isset($model->timing_time['start']) && isset($model->timing_time['hour']) && isset($model->timing_time['minute']) && $model->hasTiming(false)) {
                $model->timing_time = $model->timing_time['start'] . ' ' . $model->timing_time['hour'] . ':' . $model->timing_time['minute'];
                $model->cascadeDelete = false;
                if ((int)$model->timing_state !== RedisKey::HAS_PUB_OFFLINE) {
                    $model->deleted_at = date('Y-m-d H:i:s', time());
                }
            }
            if (Auth::user())
                $model->user_name = Auth::user()->name;
            if (!$model->exists) //新创建的数据 会更新rank
            $model->rank = time() + $model->rankTail;

            if ($model->processGeo)
                $model->processGeo();
        });

        self::saved(function ($model) {
            if ($model->disableEvent === true)
                return true;
            $timing_state = (int)$model->timing_state;
            if ($model->hasTiming()) {
                $model->cascadeDelete = false;
                if ($timing_state !== RedisKey::HAS_PUB_OFFLINE) {
                    $model->delete();
                }
                WriteApi::addTimingData($timing_state, $model->getTable(), $model->id, isset($model->title) ? $model->title : '', strtotime($model->timing_time));
            } elseif (!isset($model->timing_state) || $model->hasNormalData() || $timing_state == RedisKey::PUB_ONLINE) {
                CacheController::create($model->getTable(), $model->toArray());
                Log::info('创建更新:' . $model->getTable() . ':' . $model->toJson());
            }
            return true;
        });

        self::deleted(function ($model) {
            if ($model->disableEvent === true)
                return true;

            if ($model->cascadeDelete)
                $model->cascadeDelete($model);

            if ($model->hasTiming()) {
            }
            WriteApi::delTimingData($model->getTable(), $model->id);
            CacheController::delete($model->getTable(), $model->toArray());
            Log::info('删除:' . $model->getTable() . ':' . $model->toJson());
            return true;
        });

        self::updated(function ($model) {
            //updated 之后会调用 saved
            //更新触发历史记录
            if ($model->disableEvent === true)
                return true;

            Record::recordHistory('ApiModel', $model->getTable(), $model->id, json_encode($model->oldData));
            $model->cascadeUpdate($model);
            return true;
        });
    }

    public function hasTiming($flag = true)
    {
        if (!isset($this->timing_state)) {
            return false;
        }
        $timing_state = (int)$this->timing_state;
        return $timing_state === RedisKey::HAS_PUB_OFFLINE || (($timing_state === RedisKey::HAS_PUB_FIRST || $timing_state === RedisKey::HAS_PUB_ONLINE) && ($flag ? !$this->hasNormalData() : true));
    }

    public function hasNormalData()
    {
        return isset($this->attributes['deleted_at']) && $this->attributes['deleted_at'] === RedisKey::DEFAULT_DELETE_TIME;
    }

    public function hasChildData()
    {
        if (!empty($this->foreign)) {
            $foreign = explode(':', $this->foreign);
            if (!empty($foreign[0])) {
                if (!empty($this->{$foreign[0]})) {
                    return true;
                } else
                    return false;
            }
            return true;
        }
        return true;
    }

    public function getEditColumns()
    {

        $columns = $this->getColumns();
        return array_except($columns, $this->hiddenField);
    }

    public function getColumns()
    {

        $columnArray = array();
        $columns = DB::connection($this->connection)
            ->getDoctrineSchemaManager()
            ->listTableDetails($this->table)
            ->getColumns();

        foreach ($columns as $name => $column) {
            $columnArray[$name] = $column->toArray();
        }

        return $columnArray;
    }

    public function __get($key)
    {
        $value = $this->getAttribute($key);

        if ((is_string($value) && ($json2array = json_decode($value, true)) !== null))
            $value = $json2array;
        return $value;
    }

    public function __set($key, $value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $this->setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if (is_string($value) && ($json2array = json_decode($value, true)) !== null)
            $value = $json2array;
        return $value;
    }

    /**
     * 重写父类方法  增加数据判断 如果是数据则 to json
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute($key, $value)
    {
        if (is_array($value))
            $value = json_encode($value);
        parent::setAttribute($key, $value);
    }

    /**
     * 处理GEO数据
     */
    public function processGeo()
    {
        $geo = array('type' => 0, 'data' => array());
        if (isset($this->geo)) {
            if (empty($this->geo['data']) || !isset($this->geo['type']) || $this->geo['type'] == 0)
                $this->geo = '';
            elseif ($this->geo['type'] == 1) {
                $geo['type'] = (int)$this->geo['type'];
                $geo['data'] = isset($this->geo['data']) ? array_keys(array_except(Config::get('params.areaFilterList'), $this->geo['data'])) : array_keys(Config::get('params.areaFilterList'));
                if (is_string($this->geo['force'])) {
                    $force = explode(',', $this->geo['force']);
                } else {
                    $force = $this->geo['force'];
                }
                $geo['force'] = isset($this->geo['force']) ? array_keys(array_except(Config::get('params.areaFilterList'), $force)) : array_keys(Config::get('params.areaFilterList'));
                $this->geo = $geo;
            } elseif ($this->geo['type'] == 2) {
                $geo['type'] = (int)$this->geo['type'];
                $geo['data'] = $this->geo['data'];
                if (is_string($this->geo['force'])) {
                    $geo['force'] = explode(',', $this->geo['force']);
                } else {
                    $geo['force'] = $this->geo['force'];
                }
                $this->geo = $geo;
            }

        }
    }

    /**
     * TODO 继承DataSource接口
     * @param $map
     * @return array
     */
    public function mapLocal($map)
    {
        $local_data = array();
        if ($map) {
            foreach ($map as $m) {
                list($local, $remote) = explode(':', $m);
                if (isset($this->$remote))
                    $local_data[$local] = $this->$remote;
            }
        }
        return $local_data;
    }


    /*
     * 根据每张表里的parent foreign
     * 级联删除作为
     */

    public function cascadeDelete($model)
    {
        //判断有没有父表
        if (isset($this->parent) && $this->parent) {
            list($field, $table, $id) = explode(':', $this->parent);
            $vm = new ApiModel($table);
            $data = $vm->newQuery()->find($id);
            if ($data && $data->toArray() && $data->$field) {
                $field_ids = $data->$field;
                foreach ($field_ids as $index => &$id) {
                    if ((int)$id == (int)$this->id) {
                        unset($field_ids[$index]);
                        break;
                    }
                }

                $data->$field = json_encode($field_ids);
                $data->setTable($table);
                if (!$data->save())
                    Log::error('更新:' . $table . ':foreign:失败');
            }
        }

        //判断有没有子表
        if (isset($this->foreign) && $this->foreign) {
            list($field, $table) = explode(':', $this->foreign);
            if (isset($this->$field) && $this->$field) {
                foreach ($this->$field as $field_id) {
                    $this->apiModelDelete($table, $field_id, $field);
                }
            }
        }
    }

    /*
     * 根据每张表里的parent foreign
     * 级联更新
    */

    public function apiModelDelete($table, $id, $field = '')
    {
        $vm = new ApiModel($table);
        $vm = $vm->newQuery()->find($id);
        if ($vm && $vm->exists) {
            $vm->setTable($table);
            if (!$vm->delete())
                Log::error('删除子表' . $table . ':' . 'field:' . $field . ':' . $id . '失败');
        }
    }

    public function cascadeUpdate($model)
    {

        //var_dump($this->oldData);die();
        $diff = array();
        if ($this->oldData && isset($this->foreign) && $this->foreign) {
            list($field, $table) = explode(':', $this->foreign);
            if (isset($this->oldData[$field]) && json_decode($this->oldData[$field]) != null) {
                $this->oldData[$field] = json_decode($this->oldData[$field], true);
                $diff = array_diff($this->oldData[$field], $this->$field);
            }

            if ($diff) {
                foreach ($diff as $d) {
                    $this->apiModelDelete($table, $d, $field);
                }
            }
        }
    }

    public function setDefaultValue($foreignFields)
    {
        $foreign = array();
        foreach ($foreignFields as $foreignTable => $foreignField) {
            $this->$foreignField = json_encode(array());
            $foreign[] = $foreignField . ':' . $foreignTable;
        }
        $this->foreign = implode(',', $foreign);
    }

    public function setChildSets($childStore)
    {
        foreach ($childStore as $tableName => $tableData) {
            foreach ($tableData as $d) {
                if (array_filter($d)) {
                    if (empty($d['id']))
                        $this->childSets[$tableName][] = array(
                            'id'       => '',
                            'instance' => new ApiModel($tableName, $d),
                            'newData'  => $d,
                        );
                    else {
                        $instance = new ApiModel($tableName);
                        $this->childSets[$tableName][] = array(
                            'id'       => $d['id'],
                            'instance' => $instance->newQuery()->find($d['id']),
                            'newData'  => $d
                        );
                    }
                }
            }
        }
    }

    public function XSave($table, $foreignField)
    {
        if (!$this->childSets) {
            return $this->save();
        }
        //进行事物处理
        DB::connection('models')->beginTransaction();
        try {
            $this->cascadeDelete = false;
            $this->save();
            $this->getChildIds($table, $foreignField, true);
            $this->exists = true;
            $this->disableEvent = true;
            $this->save();
            DB::connection('models')->commit();
            $timing_state = (int)$this->timing_state;
            if ($this->hasNormalData() || $timing_state == RedisKey::PUB_ONLINE || $this->attributes['deleted_at'] === null) {
                Log::info('Xsave创建更新:' . $this->getTable() . ':' . $this->toJson());
                CacheController::create($this->getTable(), $this->toArray());
            }
            return true;
        } catch
        (\Exception $e) {
            DB::connection('models')->rollBack();
            return false;
        }
    }

    protected function getChildIds($table, $foreignField, $rank = false)
    {
        $rankTail = count($this->childSets);
        foreach ($this->childSets as $tableName => $childSet) {
            foreach ($childSet as $d) {
                if (!$d['instance']) continue;
                $d['instance']->parent = $foreignField[$tableName] . ':' . $table->table_name . ':' . $this->id;
                if ($d['id']) {
                    $d['instance']->setTable($tableName);
                    if (empty($d['newData']['geo']))
                        $d['instance']->processGeo = false;
                    $d['instance']->update(array_except($d['newData'], array('id')));
                    $childIds [$tableName][] = (int)$d['id'];
                } else {
                    $d['instance']->rankTail = $rankTail--;
                    $d['instance']->save();
                    $childIds [$tableName][] = (int)$d['instance']->id;
                }
            }
        }
        if ($foreignField) {
            foreach ($foreignField as $foreignTable => $field) {
                if (isset($childIds[$foreignTable]) && $childIds[$foreignTable]) {
                    $this->$field = json_encode($childIds[$foreignTable]);
                }
            }
        }
    }

    public function XUpdate($table, $foreignField, $tableStore)
    {

        if (!$this->childSets)
            return $this->update($tableStore);
        DB::connection('models')->beginTransaction();
        try {
            $this->getChildIds($table, $foreignField);
            $this->update(array_except($tableStore, $foreignField));
            DB::connection('models')->commit();
            return true;
        } catch (\Exception $e) {
            DB::connection('models')->rollBack();
            return false;
        }
    }

    public function getDataList(SchemaBuilder $table, $status, $pageSize, $field = 'title', $keyword = '')
    {
        $fields = json_decode($table->property, true);
        $field = Input::get('field', '') ? Input::get('field', '') : $field;
        $keyword = $keyword ? $keyword : addslashes(Input::get('keyword', ''));
        $addCondition = '';
        if ($field && $keyword && (isset($fields[$field]) || in_array($field, $this->hiddenField))) {
            if ($fields[$field][0] !== 'string')
                $addCondition = ' AND (`' . $field . '`="' . $keyword . '"';
            else {
                $addCondition = ' AND `' . $field . '` like "%' . $keyword . '%"';
            }
        }
        if ((int)Auth::user()->roles === 3 && $status === 'deleted') {
            $dataList = $this->newQueryWithDeleted()->whereRaw(ApiModel::DELETED_AT . ' != "' . RedisKey::DEFAULT_DELETE_TIME . '"  and  timing_state =' . RedisKey::DELETED . $addCondition)->orderByRaw('rank DESC,' . ApiModel::DELETED_AT . ' ASC')->paginate($pageSize);
        } elseif ($status === 'waiting') {
            $dataList = $this->newQueryWithDeleted()->whereRaw('timing_state = 3 ' . $addCondition)->orderBy('rank', 'desc')->paginate($pageSize);
        } elseif ($status === 'timing') {
            $dataList = $this->newQueryWithDeleted()->whereRaw(sprintf('timing_state  in (%s) ', join(',', array(RedisKey::HAS_PUB_FIRST, RedisKey::HAS_PUB_OFFLINE, RedisKey::HAS_PUB_ONLINE))) . $addCondition)->orderBy('rank', 'desc')->paginate($pageSize);
        } else
            $dataList = $this->whereRaw(sprintf('deleted_at =  "%s" ', RedisKey::DEFAULT_DELETE_TIME)
            . $addCondition)->orderBy('rank', 'desc')->paginate($pageSize);
        return $dataList;
    }


}