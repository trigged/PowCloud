<?php
namespace Plugin;

use ApiModel;
class Hook extends Widget{

    public $name = '';//hook name;
    /**
    function __construct(\Hook $model)
     * @param $model
     */
    function __construct(\Hook $model)
    {
        if ($model instanceof \Hook) {
            $this->model = $model;
            $this->code = $model->code;
            $this->table_name = $model->table_name;
            $this->name = $model->name;
        } elseif (is_string($model)) {
            $this->table_name = $model;
            $this->model = new ApiModel($this->table_name);
        }
    }

    public static function createHook(\Hook $model = NUll)
    {
        return new Hook($model);
    }
}