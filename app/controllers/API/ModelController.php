<?php
use Illuminate\Routing\Controllers\Controller;
use Operator\CacheController;
use Operator\ReadApi;
use Utils\CMSLog;
use Utils\UseHelper;


/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 11/9/13
 * Time: 2:50 PM
 * To change this template use File | Settings | File Templates.

 */

class ModelController extends Controller
{

    //region params
    /**
     * @var  mixed|null|表名
     */
    public $table_name = null;

    /**
     * @var 访问权限
     */
    public $right = null;

    /**
     * @var 版本
     */
    public $version = null;

    /**
     * @var 格式
     */
    public $format = null;

    /**
     * @var 页数
     */
    public $page = null;

    /**
     * @var 个数
     */
    public $count = null;

    /**
     * @var 用户地域 code
     */
    public $geo = array();

    /**
     * @var 缺省字段
     */
    public $less = array();

    /**
     * @var 过期时间
     */
    public $expire = 60;

    /**
     * @var 缺省渠道号
     */
    public $channel_id = null;

    /**
     * @var app 池
     */
    public $app_pool = null;

    /**
     * @var 随机返回数据
     */
    public $random = false;

    /**
     * @var 用户池
     */
    public $token = null;

    /**
     * @var 调试模式
     */
    public $debug = false;

    /**
     * @var 返回结果
     */
    public $result = array(
        'code'    => 1,
        'message' => 'success',
        'data'    => array()
    );

    /**
     * @var 记录处理的字段数
     */
    public $num = 0;

    /**
     * @var 开始处理时间
     */
    public $start = null;

    /**
     * @var 处理方法 api 监控使用
     */
    public $method = null;

    public $skip_field = null;

    public $skip_data = null;

    public $incrby = null;

    public $incrva = null;

    public $max_count = null;

    //endregion

    public function __construct()
    {
        $that = $this;
        $this->start = microtime(true);

        $this->beforeFilter(function () use ($that) {
            $that->token = Input::get('token', Config::get('app.default_token'));
            $that->app_pool = (int)UseHelper::checkToken($that->token, UseHelper::$default_key);
            if ($that->app_pool == 0) {
                return $that->getResult(-1, 'token error', null);
            }
            \Utils\AppChose::updateConf($that->app_pool);

            $name = Route::currentRouteName();
            $methods = explode('.', $name);
            $method = end($methods);
            $that->format = Input::get('format');
            //字段 加减
            $incrby = Input::get('incrby');
            $incrva = Input::get('incrva');
            if ($incrby && $incrva) {
                $that->incrby = $incrby;
                $that->incrva = (int)$incrva;
            }
            $that->count = (int)Input::get('count');
            if ($that->count == null) {
                $that->count = 20;

            }
            //条件输出省略
            $skip_field = Input::get('skip_field');
            $skip_data = Input::get('skip_data');
            $that->max_count = $that->count;
            if ($skip_field && $skip_data) {
                $that->skip_data = explode(',', $skip_data);
                $that->skip_field = $skip_field;
                $that->count += count($that->skip_data);
            }

            //字段输出省略
            $less = Input::get('less');
            $that->debug = Input::get('debug') === '9527' ? true : false;
            $that->random = Input::get('random');
            $that->channel_id = Input::get('channel_id');
            if ($less) {
                $that->less = explode(',', $less);
            }
            //prepare for paging
            $that->page = (int)Input::get('page');
            if ($that->format == null) {
                $that->format = 'json';
            }
            //统计监控
            $that->method = $method;
            $that->table_name = Request::get('model');
            $that->expire = (int)Request::get('expire');
            $that->right = Request::get('right');
            //权限检查
            if (!$method or !isset($that->right[$method]) or $that->right[$method] !== 1) {
                return $that->getResult(-1, 'not allow', null);
            }
        });
    }

    public function getResult($code = 1, $message = 'success', $data = array())
    {
        $this->result['code'] = $code;
        $this->result['message'] = $message;
        if ($data != null) {
            $data = array_merge(array(), $data);
        }
        $data_count = count($data);
        if ($this->skip_field && $this->skip_data && $data_count > $this->max_count) {
            $data = array_slice($data, 0, $this->max_count);
        }
        $this->result['data'] = $data;
        $http_code = $code > 0 ? 200 : 404;
        $value = round(microtime(true) - $this->start, 3);
        CMSLog::debug(sprintf('runtime: %s:%s :%s', $this->table_name, $this->method, $value));
        \Operator\WriteApi::addApiMonitor($this->table_name, $this->method, $value * 1000);
        $header = array(
            'Expires'        => date(DATE_RFC822, time() + $this->expire) . ' GMT',
            'Pragma'         => 'public',
            'Last-Modified:' => 'Fri, 14 Feb 2014 04:03:53 GMT',
            'Cache-Control'  => 'public, max-age=' . $this->expire,
        );
        if ($this->format === 'xml') {
            return Response::make($this->array_to_xml($this->result), $http_code, $header);
        } elseif ($this->format === 'plan') {
            if ($this->max_count = 1) {
                $data = array($data);
            }
            return Response::view('output.plan', array('data' => $data));
        }

        if ($this->debug) {
            $this->result['debug']['db'] = Config::get('database.redis.default.database');

            $this->result['debug']['host'] = gethostname();
            $this->result['debug']['request_id'] = CMSLog::$requestHandler;

            $fields = get_object_vars($this);
            if (isset($fields['result'])) {
                unset($fields['result']);
            }
            $this->result['debug']['field'] = $fields;
        }

        return Response::json($this->result, $http_code, $header)->setCallback(Input::get('callback'));
    }

    function array_to_xml($array, $xml = false)
    {
        if ($xml === false) {
            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><result/>');
        }
        foreach ($array as $key => $value) {
            if (is_array($value) || is_object($value)) {
                if (!is_numeric($key)) {
                    $this->array_to_xml($value, $xml->addChild($key));
                } else {
                    $item = $xml->addChild('item', null);
                    $value = $this->array_to_xml($value, $item);
//                    $this->array_to_xml($value, $xml);
                }
            } else {
                $xml->addChild($key, $value);
            }
        }
        return $xml->asXML();
    }

    /*
     * 列表访问*/

    public function index()
    {
        if ($this->random === 'true') {
            $data = $this->getObjectsByName($this->table_name);
            $data = $this->process($data);
            if ($this->count > 1) {
                $data_count = count($data);
                if ($data_count < $this->count) {
                    $this->count = $data_count;
                }
                $index = array_rand($data, $this->count);
                $result = array();
                if (count($index) === 1) {
                    $result[] = $data[$index];
                    return $this->getResult(1, 'success', $result);
                }

                foreach ($index as $item) {
                    $result[] = $data[$item];
                }
                return $this->getResult(1, 'success', $result);

            }
            return $this->getResult(1, 'success', $data[array_rand($data)]);
        } else {
            $data = CacheController::getRange($this->table_name, $this->page, $this->count);
            //load from cache ignore default count
            if (count($data) < $this->count) {
                $value = CacheController::handlerPaging($this->table_name, $this->page, $this->count);
                if ($value !== false) {
                    $data = $value;
                }
            }

            $data = ReadApi::getDataByIDs($this->table_name, $data);
        }
        CMSLog::debug('load data from redis');

        return $this->getResult(1, 'success', $this->process($data));
    }


    //region process logic

    /**
     * @param $name
     * @return array|static
     * 根据表名获取表的所有有效数据
     */
    public function getObjectsByName($name)
    {
        return ReadApi::getAllTableObject($name, true);
    }

    /**
     * @param $data
     * @param bool $type
     * @return array|null
     */
    public function process($data, $type = true)
    {
        $start = microtime();
        //mult data
        if (is_array($data) && $type) {
            foreach ($data as $key => &$value) {
                //result may have three state: true,false,array
                //array means no need loop just return
                $result = $this->processData($value);
                if (is_array($result)) {
                    return $result;
                } elseif (!$result) {
                    unset($data[$key]);
                }
            }
        } //single data
        else {
            $result = $this->processData($data);
            if (is_array($result)) {
                return $result;
            } else if (!$result) return null;
        }

        $result = array();
        foreach ($data as $key => $item) {
            if (is_string($key)) {
                $result[$key] = $item;
            } else {
                $result[] = $item;
            }

        }
        CMSLog::debug(vsprintf('convert %s filed,used %s, ', array($this->num, microtime() - $start)));

        return $result;
    }

    public function processData(&$data)
    {
        //try to convert int type field
        //get all filed type
        if (is_object($data)) {
            $data = (array)$data;
        }
        $data["id"] = (int)$data["id"];
        $table_info = ReadApi::getTableInfo($this->table_name);
        if ($table_info && isset($table_info['property'])) {
            $property = json_decode($table_info['property'], true);
            foreach ($property as $key => $pro) {
                if ($pro[0] == 'integer') {
                    $data[$key] = (int)$data[$key];
                } elseif ($pro[0] == 'double') {
                    $data[$key] = (double)$data[$key];

                }
            }
        }


        if (is_array($data)) {
            if ($this->skip_data && $this->skip_field && $skip_value = $data[$this->skip_field]) {
                if (in_array($skip_value, $this->skip_data)) {
                    return false;
                }
            }


            if (isset($data['imgs'])) {
                $data['imgs'] = json_decode($data['imgs'], true);
            }
            $result = $this->processLimit($data);
            if ($result === false) {
                return false;
            }
            if (!$this->debug) {
                foreach ($this->less as $less) {
                    unset($data[$less]);
                }
                unset($data['deleted_at']);
                unset($data['updated_at']);
                unset($data['created_at']);
                unset($data['timing_time']);
                unset($data['timing_state']);
                unset($data['user_name']);
                unset($data['parent']);
                unset($data['rank']);
                unset($data['action_limit']);
                unset($data['action_flag']);
            }


            //check children
            $this->processChildrenData($data);
            $this->processOnlinePlay($data);
            if (isset($data['template'])) {
                //check template
                //cause template don't need much data just handler one and return
                return $this->processTemplateData($data);
            }
            //check area block
            $result = $this->processDataBlock($data);
            if ($result === false) {
                return false;
            }
            return $this->processChannelVisible($data);
        }
        return true;
    }

    public function processLimit(&$data)
    {
        //version:<:3 ; play|display|field:value
        if ((isset($data['action_limit']) && $action_limit = $data['action_limit']) && (isset($data['action_flag']) && $flag = $data['action_flag'])) {
            $action_limit = $data['action_limit'];
            $output_condition = explode(':', $action_limit);
            if (count($output_condition) == 3) {
                $filed = $output_condition[0];
                $types = $output_condition[1];
                $condition = $output_condition[2];
                $key = Input::get($filed, null);
                //all params get
                if ($filed && $types && $condition && $key && $flag) {
                    if ($types === '>') {
                        if ($key > $condition) {
                            return $this->setLimitData($data, $flag);
                        }
                    } elseif ($types === '<') {
                        if ($key < $condition) {
                            return $this->setLimitData($data, $flag);
                        }
                    } elseif ($types === '=') {
                        if ($key = $condition) {
                            return $this->setLimitData($data, $flag);
                        }
                    } elseif ($types === '<=') {
                        if ($key <= $condition) {
                            return $this->setLimitData($data, $flag);
                        }
                    } elseif ($types === '>=') {
                        if ($key >= $condition) {
                            return $this->setLimitData($data, $flag);
                        }
                    }
                }
            }
        }

        return true;
    }

    public function setLimitData(&$data, $flag)
    {
        if ($flag === 'play') {
            return true;
        } elseif ($flag === 'display') {
            return false;
        } else {
            //field:value
            $flag = explode(':', $flag);
            if (count($flag) === 2) {
                $field = $flag[0];
                $value = $flag[1];
                $data[$field] = $value;
            }
        }
        return true;
    }

    /**
     * @param $data
     * 处理含有外键关系的数据  special_video
     */
    public function processChildrenData(&$data)
    {
        if (isset($data['children'])) {
            $has = explode(":", $data['children']);
            if (!$this->debug) {
                unset($data['children']);
            }
            if (count($has) === 2) {
                $field = $has[0];
                $table_name = $has[1];
                if (!isset($data[$field])) {
                    return;
                }
                if ($data[$field] == "") {
                    $data[$field] = array();
                    return;
                }
                if (isset($data[$field])) {
                    $keys = json_decode($data[$field]);
                    if ($keys !== null) {
                        $value = ReadApi::getDataByIDs($table_name, $keys);
                        $result = $this->process($value);
                        if ($this->random === 'true') {
                            if ($this->count > 1) {
                                $count = count($result);
                                if ($count > $this->count) {
                                    $count = $this->count;
                                }
                                shuffle($result);
                                $indexs = array_rand($result, $count);
                                if (count($indexs) === 1) {
                                    $data[$field] = array($result[$indexs]);
                                } else {
                                    $tmp = array();
                                    foreach ($indexs as $index) {
                                        $tmp[] = $result[$index];
                                    }
                                    $data[$field] = $tmp;
                                }
                            } else {
                                $data[$field] = $result[array_rand($result, $this->count)];
                            }

                        } else {
                            $data[$field] = $result;
                        }
                    }
                }
            } else {
                foreach ($has as $field => $name) {
                    if (isset($data[$field])) {
                        $keys = json_decode($data[$field]);
                        if ($keys !== null) {
                            $value = ReadApi::getDataByIDs($name, $keys);
                            $data[$field] = $this->process($value);
                            if ($data[$has[0]] == "") {
                                $data[$has[0]] = array();
                            }
                        }
                    }
                }
            }
        }
    }

    public function processOnlinePlay(&$data)
    {
        if (isset($data['online_play'])) {
            $online_play = $data['online_play'];
            $online_play = explode(':', $online_play);
            if (count($online_play) === 3) {
                $data['online_play'] = (int)$online_play[2];
            }
        }
    }

    /**
     * @param $data
     * @return array
     * 处理含有模板的数据
     */
    public function processTemplateData(&$data)
    {
        //若有模板则忽略模板表本身的数据
        $result = array();
        $tmp = array();
        $template = explode(',', $data['template']);
        if (!$this->debug) {
            unset($data['template']);
        }
        foreach ($template as $table) {
            if (!isset($tmp[$table])) {
                //save all table object in the tmp
                //todo here will get all table you should optimize here
                $tmp[$table] = $this->process($this->getObjectsByName($table));
            }
            $value = array_shift($tmp[$table]);
            if ($value) {
                $result[] = $value;
            }
        }
        return $result;

    }

    /**
     * @param $data
     * @return bool
     * 处理含有地域屏蔽的数据
     */
    public function processDataBlock(&$data)
    {
        $geo = null;
        if (isset($data['geo'])) {
            $geo = json_decode($data['geo'], true);
            if (!$this->debug) {
                unset($data['geo']);
            }
        }
        return true;
    }

    public function processChannelVisible(&$data)
    {
        $invisible = null;
        if (!empty($this->channel_id) && isset($data['channel_invisible'])) {
            $channel_invisible = $data['channel_invisible'];
            if (!$this->debug) {
                unset($data['channel_invisible']);
            }
            $invisible = explode(',', $channel_invisible);
            if (in_array($this->channel_id, $invisible)) {
                return false;
            }
        }
        if (!$this->debug) {
            unset($data['channel_invisible']);
        }
        return true;
    }

    //endregion

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    //TODO:  consider the children and parent situation ! and the default value


    public function create()
    {
        $data = Input::all();
//        $table_struct = ReadApi::getTableInfo($this->table_name);
//        if (isset($table_struct['property'])) {
//            $property = $table_struct['property'];
//            if (is_string($property)) {
//                $property = (array)$property;
//            }
//            if (isset($property['children'])) {
//                $value = explode(':', $property['children']);
//                if (count($value) >= 2) {
//                    $field = $value[0];
//                    $table = $value[1];
//                }
//            }
//        }

        $model = new ApiModel($this->table_name, $data);
        try {
            if ($model->save()) {
                return $this->getResult(1, 'success', $model->toArray());
            }
            return $this->getResult(1, 'error', $model->toArray());
        } catch (Exception $e) {
            CMSLog::debug(sprintf('create error %s', $e));
            return $this->getResult(-1, sprintf('create error ,more : %s', $e));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $data = Input::get('data');
        if (empty($data)) {
            return $this->getResult(-1, 'missing data');
        }
        $data = json_decode($data, true);
        if (empty($data)) {
            return $this->getResult(-1, 'data was not standard json');
        }

        $flag = Input::get('flag');
        try {
            if (!empty($flag)) {
                //check first
                $flag = json_decode($flag, true);
                if (empty($flag)) {
                    return $this->getResult(-1, 'flag was not standard json');
                }
                $result = $this->find($flag);
                $result_count = count($result);
                if ($result_count > 1) {
                    return $this->getResult(-1, "too many found total found $result_count");
                } elseif ($result_count == 1) {
                    $vm = new ApiModel($this->table_name);
                    $table_model = $vm->newQueryWithDeleted()->find($result[0]->id);
                    $table_model->setTable($this->table_name);
                    $table_model->update($data);
                    return $this->getResult(1, 'success', $this->process($table_model->toArray()));

                }
            }
            $model = new ApiModel($this->table_name, $data);
            $model->setTable($this->table_name);

            if ($model->save()) {
                return $this->getResult(1, 'success', $model->toArray());
            }
            return $this->getResult(-1, 'error', $model->toArray());
        } catch (Exception $e) {
            CMSLog::debug(sprintf('create error %s', $e));
            return $this->getResult(-1, sprintf('create error ,more : %s', $e->getMessage()));
        }
    }

    private function find($conditions)
    {
        $query = DB::connection('models')->table($this->table_name);
        foreach ($conditions as $key => $value) {
            if ($value === 0 || !empty($value)) {
                $query = $query->where($key, 'like', "%" . $value . "%");

            }
        }
        $query =  $query->where('deleted_at', '0000-00-00 00:00:00');
        CMSLog::debug("find sql: " . $query->toSql());
        return $query->get();

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        if ($id === 'search') {
            return $this->search();
        } else if ($id === 'incrby') {
            return $this->incrby();
        } else {
            $result = CacheController::info($this->table_name, $id);
            if (is_string($result)) {
                return $this->getResult(-1, $result);
            }
            return $this->getResult(1, 'success', $this->process($result, false));
        }
    }

    public function search()
    {
        $params = Input::all();
        $self_field = get_object_vars($this);
        $conditions = array_diff_key($params, $self_field);

        try {
            $result = $this->find($conditions);
            if (count($result) == 0) {
                return $this->getResult(-1, "not found");
            }
            return $this->getResult(1, "success", $this->process($result));
        } catch (Exception $e) {
            return $this->getResult(-1, "failed", array(
                "Exception" => $e->getMessage(),
            ));
        }


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function incrby()
    {
        $data_id = Input::get('id');

        if (!$this->incrby || !$this->incrva || !$data_id) {
            return $this->getResult("-1", "miss params");
        }
        $data = ApiModel::Find($this->table_name, $data_id);
        if (!$data) {
            return $this->getResult("-1", "not found");
        }

        $data->{$this->incrby} += $this->incrva;
        \Operator\WriteApi::incrby($this->table_name, $data_id, $this->incrby, $this->incrva);
        $data->save();
        return $this->getResult(1, "success", $this->process($data->toArray(), false));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {

    }

    private function checkArrayData($value, $data, $key = null)
    {
        if ($data === null) {
            return false;
        }
        if ($key) {
            //if key not in data still will return true
            if (isset($data[$key])) {
                return in_array($value, $data[$key]);
            }
            return true;
        }
        return in_array($value, $data);
    }

}
