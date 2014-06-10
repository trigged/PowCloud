<?php
/**
 * Created by JetBrains PhpStorm.
 * User: trigged
 * Date: 12/10/13
 * Time: 2:46 PM
 * To change this template use File | Settings | File Templates.
 */
namespace Plugin;

use ApiModel;

interface IWidget
{
    public function getData();

    public function create(ApiModel $model);

    public function update($id, $data);

    public function show($id);

    public function delete($id);

}

class Widget implements IWidget
{

    static $Sensitive_Code = array('DB::', 'SYSTEM',);

    public $table_name = null;

    public $code = null;

    public $action = null;

    /**
     * @var ApiModel
     */
    public $model = null;


    /**
      function __construct(\Widget $model)
     * @param $model
     */
    function __construct($model)
    {
        if ($model instanceof \Widget) {
            $this->model = $model;
            $this->code = $model->code;
            $this->table_name = $model->table_name;
            $this->action = $model->action;
        } elseif (is_string($model)) {
            $this->table_name = $model;
            $this->model = new ApiModel($this->table_name);
        }
    }

    public static function cast(\Widget $model)
    {
        return new Widget($model);
    }

    /**
     * this method just can load the [cms-modules] data ,can't load cms data.
     * @param int $page
     * @param int $limit
     * @param null $where like (0=>(field=>'', operator =>'=|<|>', value =>'' ),...)
     * @return array|static
     */
    public function getData($page = 0, $limit = 20, $where = null)
    {

        $model = new ApiModel($this->table_name);
        $query = $model->limit($limit)->skip($page);
        if ($where) {
            foreach ($where as $value) {
                $query = $query->where($value['field'], $value['operator'], $value['value']);
            }
        }
        $sql = $query->toSql();
        Log::info(sprintf('sql was :%s ', $sql));
        $data = $query->get();
        return $data;
    }

    public function create(ApiModel $model)
    {
        if ($model->save()) {

        }
    }

    public function update($id, $data)
    {
        $this->model = $data;
        $this->model->save();
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

    public function delete($id)
    {
        $this->model->delete();
    }

    public function run($params = array())
    {
        if ($this->check_code($this->code)) {
            if($params && is_array($params))
                extract($params);
            eval("?> $this->code <?php ");
        } else {
            echo '代码含有敏感变量或操作,请检查!';
        }
    }


    /**
     * escape sensitive code
     * @param $code
     * @return bool
     */
    public static function check_code($code){

        $pattern = '#'.implode('|',self::$Sensitive_Code).'#is';
        preg_match($pattern, strtoupper($code), $matches);
        if ($matches) {
            return false;
        }
        return true;
    }
}

