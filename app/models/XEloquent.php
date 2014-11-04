<?php
/**
 *
 * Class XEloquent
 */

class XEloquent extends Eloquent
{

    public $errors = '';

    public $oldData = array();

    /**
     * 设置 为false的时候   表示不进行任何验证
     * 设置 成对应的场景  进行对应的验证 哎
     * @var string
     */
    public $scene = 'create';

    public static function boot()
    {
        parent::boot();

        //注册saving事件
        self::saving(function ($model) {

            $model->oldData = $model->getOriginal(); //保留原始值

            if (!$model->validator()) {
                return false;
            }

            return $model->fireXEloquentSavingEvent($model);

        });
    }

    /**
     * Model属性 对应的规则要求
     */
    protected function rules()
    {

        return array();
    }

    public function messages()
    {

        return array();
    }

    /**
     * 在saving 调用此方法进行表单属性进行验证
     * @return bool
     *
     */
    public function validator()
    {
        $validator = Validator::make($this->getAttributes(), $this->getValidatorRules(), $this->messages());
        if ($validator->fails()) {
            $this->errors = $validator->messages()->getMessages();
            return false;
        }
        return true;
    }


    /**
     * 根据scene来进行选择性的验证规则
     * @return array
     */
    public function getValidatorRules()
    {

        if (!$rules = $this->rules())
            return array();

        $default = empty($rules['default']) ? array() : $rules['default'];

        if (!$this->scene)
            return $default;

        $scene = empty($rules[$this->scene]) ? array() : $rules[$this->scene];


        return array_merge($default, $scene);

    }

    public function fireXEloquentSavingEvent($model)
    {

        return true;
    }
}