<?php
/**
 * Created by PhpStorm.
 * User: troyfan
 * Date: 13-12-12
 * Time: 上午10:34
 */

class CodeFragmentController extends SystemController
{

    public $nav = 'advanced';

    /**
     * 挂件列表
     * @return \Illuminate\View\View
     */
    public function widget()
    {
        $this->menu = 'codeFragment.widget';
        $widgets = Widget::paginate(10);

        return $this->render('codeFragment.widget.widget', array('widgets' => $widgets));
    }

    public function editWidget($id)
    {
        if (!$id || !($widget = Widget::find($id))) app::abort(404);
        $this->menu = 'codeFragment.widget';

        return $this->render('codeFragment.widget.edit', array('widget' => $widget))->nest('code_common', 'codeFragment.code_common');
    }

    public function createWidget()
    {
        $this->menu = 'codeFragment.widget';

        return $this->render('codeFragment.widget.create')->nest('code_common', 'codeFragment.code_common');
    }

    public function updateWidget($id)
    {

        if (!$id || !($widget = Widget::find($id))) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_NOT_EXISTS);
//            $this->ajaxResponse(array(''), 'fail', '更新的数据不存在');
        }


        if ($widget->name !== Input::get('name', '') || $widget->table_name !== Input::get('table_name', '') || $widget->action !== Input::get('action', '')) {
            if (Widget::whereRaw('table_name = ? AND name = ? AND action = ?', array(
                    Input::get('table_name', ''),
                    Input::get('name', ''),
                    Input::get('action', '')
                ))->count() > 0
            )
                $this->ajaxResponse(array('name' => '挂件已经存在'), 'fail', '修改失败');
        }

        if (!\Plugin\Widget::check_code(Input::get('code'))) {

            $this->ajaxResponse(array('name' => '代码有危害的代码' . implode('|', \Plugin\Widget::$Sensitive_Code)), 'fail', '修改失败');
        }
        $widget->oldData = $widget->toArray();
        if ($widget->update(array_except(Input::all(), array('id'))))
            $this->ajaxResponse(array(''), 'success', '更新成功', URL::action('CodeFragmentController@widget'));
        $this->ajaxResponse($widget->errors, 'fail', '更新失败');
    }

    public function storeWidget()
    {

        if (Widget::whereRaw('table_name = ? AND name = ? AND action = ?', array(
                Input::get('table_name', ''),
                Input::get('name', ''),
                Input::get('action', '')
            ))->count() > 0
        )
            $this->ajaxResponse(array('name' => '挂件已经存在'), 'fail', '添加失败');

        if (!\Plugin\Widget::check_code(Input::get('code'))) {
            $this->ajaxResponse(array('name' => '代码有危害的代码' . implode('|', \Plugin\Widget::$Sensitive_Code)), 'fail', '修改失败');
        }

        $widget = new Widget(Input::all());

        if ($widget->save())
            $this->ajaxResponse(array(), 'success', '添加成功', URL::action('CodeFragmentController@widget'));
        $this->ajaxResponse($widget->errors, 'fail', '添加失败');
    }

    public function widgetDetail($id)
    {
        $this->menu = 'codeFragment.widget';

        $currentVersion = Widget::find($id);
        if (!$currentVersion)
            App::abort(404);
        $records = Record::whereRaw('connect = ? AND table_name=? AND content_id=?', array('', 'widget', $id))->paginate(10);

        return $this->render('codeFragment.widget.detail', array(
            'currentVersion' => $currentVersion,
        ))->nest('record', 'record._record', array('records' => $records));
    }


    //hook begin
    public function hook()
    {
        $this->menu = 'codeFragment.hook';
        $hooks = Hook::paginate(10);

        return $this->render('codeFragment.hook.hook', array('hooks' => $hooks));
    }

    public function editHook($id)
    {
        if (!$id || !($hook = Hook::find($id))) app::abort(404);
        $this->menu = 'codeFragment.hook';

        return $this->render('codeFragment.hook.edit', array('hook' => $hook))->nest('code_common', 'codeFragment.code_common');
    }

    public function createHook()
    {
        $this->menu = 'codeFragment.hook';

        return $this->render('codeFragment.hook.create')->nest('code_common', 'codeFragment.code_common');
    }

    public function updateHook($id)
    {

        if (!$id || !($hook = Hook::find($id)))
            $this->ajaxResponse(array(''), 'fail', '更新的数据不存在');


        if ($hook->name !== Input::get('name', '') || $hook->table_name !== Input::get('table_name', '')) {
            if (Hook::whereRaw('table_name = ? AND name = ?', array(
                    Input::get('table_name', ''),
                    Input::get('name', ''),
                ))->count() > 0
            )
                $this->ajaxResponse(array('name' => 'hook已经存在'), 'fail', '修改失败');
        }

        if (!\Plugin\Widget::check_code(Input::get('code'))) {

            $this->ajaxResponse(array('name' => '代码有危害的代码' . implode('|', \Plugin\Widget::$Sensitive_Code)), 'fail', '修改失败');
        }

        $hook->oldData = $hook->toArray();
        if ($hook->update(array_except(Input::all(), array('id'))))
            $this->ajaxResponse(array(''), 'success', '更新成功', URL::action('CodeFragmentController@hook'));
        $this->ajaxResponse($hook->errors, 'fail', '更新失败');
    }

    public function storeHook()
    {

        if (Hook::whereRaw('table_name = ? AND name = ?', array(
                Input::get('table_name', ''),
                Input::get('name', ''),
            ))->count() > 0
        )
            $this->ajaxResponse(array('name' => 'hook已经存在'), 'fail', '添加失败');

        if (!\Plugin\Widget::check_code(Input::get('code'))) {
            $this->ajaxResponse(array('name' => '代码有危害的代码' . implode('|', \Plugin\Widget::$Sensitive_Code)), 'fail', '修改失败');
        }

        $hook = new Hook(Input::all());

        if ($hook->save())
            $this->ajaxResponse(array(), 'success', '添加成功', URL::action('CodeFragmentController@hook'));
        $this->ajaxResponse($hook->errors, 'fail', '添加失败');
    }

    public function hookDetail($id)
    {
        $this->menu = 'codeFragment.hook';

        $currentVersion = Hook::find($id);
        if (!$currentVersion)
            App::abort(404);
        $records = Record::whereRaw('connect = ? AND table_name=? AND content_id=?', array('', 'hook', $id))->paginate(10);

        return $this->render('codeFragment.hook.detail', array(
            'currentVersion' => $currentVersion,
        ))->nest('record', 'record._record', array('records' => $records));
    }


    protected $_mysqlFilter = '#^(select|desc|explain|update)#is';

    /**
     * Mysql 代码执行
     */
    public function mysql()
    {
        $this->menu = 'codeFragment.mysql';
        $data['connection'] = Input::get('connection', 'mysql');

        if (($sql = Input::get('sql')) && !(preg_match($this->_mysqlFilter, $sql, $match))) {
            $data['error']['errorMessage'] = '输入sql 有误,只能执行select|desc|explain';
            $data ['sql'] = $sql;
            return $this->render('codeFragment.execute.mysql', array('data' => $data));
        }
        if ($sql && strpos($sql, 'limit') === false && strpos($sql, 'update') === false) {
            $sql = $sql . ' limit 0 ,10';
        }
        $data['sql'] = htmlentities($sql);
        if ($data['sql']) {
            try {
                if (strpos($sql, 'update') !== false)
                    $returns = DB::connection($data['connection'])->update($sql);
                else
                    $returns = DB::connection($data['connection'])->select($sql);

                if ($returns) {
                    foreach ($returns as $return) {
                        $data['return'][] = (array)$return;
                    }
                } else {
                    $data['return'][] = array('无结果');
                }
            } catch (\Exception $e) {
                $data['error']['errorMessage'] = $e->getMessage();
            }
        }
        return $this->render('codeFragment.execute.mysql', array('data' => $data));
    }

    /**
     * Redis 代码执行
     */
    protected $_redisFilter = array(
        'connect',
        'open',
        'pconnect',
        'popen',
        'close',
        'setoption',
        'multi',
        'exec',
        'slaveof',
        'migrate',
        'dump',
        'restore',
        'flushdb',
        'flushall',
        'del',
        'delete',
        'lrem',
        'lremove',
        'move',
        'rename',
        'renameKey',
        'keys',
        'getKeys',
        'debug segfault',
        'shutdown'
    );

    public function redis()
    {
        $this->menu = 'codeFragment.redis';
        $redisCommandStr = Input::get('redis_command', '');
        $data['redis_command'] = $redisCommandStr;

        if (!$redisCommandStr) {
            $data['error']['errorMessage'] = '请正确填写redis_command';
            return $this->render('codeFragment.execute.redis', array('data' => $data));
        }
        \Utils\CMSLog::debug('执行command.redis:' . $redisCommandStr);

        //数组第一个元素为 要执行的方法
        //数组第二个元素为 要查询的KEY
        //其它元素为参数
        $redisCommandArray = array_filter(explode(' ', trim($redisCommandStr)), 'strlen');

        if (in_array(strtolower($redisCommandArray[0]), $this->_redisFilter) || empty($redisCommandArray[1])) {
            $data['error']['errorMessage'] = '请正确填写redis命令';
            return $this->render('codeFragment.execute.redis', array('data' => $data));
        }

        //防止查询过多redis数据造成卡死 特别是 执行zrange,zrevrange 方法
        if (isset($redisCommandArray[2]) && is_numeric($redisCommandArray[2])
            && isset($redisCommandArray[3]) && is_numeric($redisCommandArray[3])
        ) {
            if (($redisCommandArray[3] - $redisCommandArray[2] > 30)) {
                $data['error']['errorMessage'] = '请不要一次查询多条redis数据';
                return $this->render('codeFragment.execute.redis', array('data' => $data));
            }
        }

        $redis = \Operator\ReadApi::redis();
        try {
            $data['return'] = call_user_func_array(array($redis, $redisCommandArray[0]), array_slice($redisCommandArray, 1));
            $data['return'] = $data['return'] === null ? 'nil' : $data['return'];
        } catch (\Exception $e) {
            $data['return'] = '报错：' . $e->getMessage();
        }

        return $this->render('codeFragment.execute.redis', array('data' => $data));
    }
}