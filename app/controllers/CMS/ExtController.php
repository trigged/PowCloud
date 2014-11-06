<?php
/**
 * 全局通用扩展
 * Class ExtController
 */
class ExtController extends BaseController
{

    /**
     * 地区列表
     * @return \Illuminate\View\View
     */
    public function areaList()
    {
        $shortCut = GeoShortcut::all();
        $type = Input::get('type', 0);
        $data = Input::get('data', array());
        $force = Input::get('force', array());
        $namespace = Input::get('namespace', '');
        if ((int)$type === 1) {
            $data = array_keys(array_except(Config::get('params.areaFilterList'), $data));
            $force = array_keys(array_except(Config::get('params.areaFilterList'), $force));
        }
        return View::make('ext.areaList', array(
            'shortCut' => $shortCut,
            'data'     => $data,
            'type'     => $type,
            'force'    => $force,
            'name'     => $namespace ? $namespace . '[geo]' : 'geo',
        ));
    }

    public function areaRadioList()
    {
        $shortCut = GeoShortcut::all();
        $type = Input::get('type', 0);
        $data = Input::get('data');
        $namespace = Input::get('namespace', '');

        return View::make('ext.areaRadioList', array(
            'shortCut' => $shortCut,
            'data'     => $data,
            'type'     => $type,
            'name'     => $namespace ? $namespace . '[geo]' : 'geo',
        ));
    }

    /**
     * 用于排序
     */
    public function rank()
    {
        $rank = Input::get('rank', '');
        $table = Input::get('table', '');
        $ids = Input::get('id', '');
        if (!$rank || !$table || !$ids)
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
//            $this->ajaxResponse(array(), 'success', '更新成功');

        try {
            DB::connection('models')->getPdo()->beginTransaction();
            $vm = new ApiModel($table);
            foreach ($ids as $rankIndex => $id) {

                $vmData = $vm->newQuery()->find($id);
                if ($vmData) {
                    $vmData->setTable($table);
                    $vmData->processGeo = false;
                    $vmData->update(array('rank' => $rank[$rankIndex]));
                }
            }
            DB::connection('models')->getPdo()->commit();
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS);
//            $this->ajaxResponse(array(), 'success', '更新成功');
        } catch (Exception $e) {
            DB::connection('models')->getPdo()->rollBack();
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED);
//            $this->ajaxResponse(array(), 'fail', '更新失败');
        }
    }

    /**
     * 所有ajax 对外请求 路由
     */
    public function ajax_analyse()
    {
        $field = Input::get('field', '');
        $target = Input::get('target', '');
        $dataSource = urldecode(Input::get('data', ''));

        $form_field = Forms::find($field);

        if ($form_field->default_value && ($default_value = json_decode($form_field->default_value, true)) != null) {

            if ($dataSource && isset($default_value['map'][$dataSource])) {
                $dataProvider = \Library\DataSource\DataProvider::factory($dataSource, trim($target), '');
                if ($mapLocal = $dataProvider->mapLocal($default_value['map'][$dataSource])) {
                    $this->ajaxResponse($mapLocal, 'success', '分析成功');
                }
                $this->ajaxResponse($mapLocal, 'fail', '分析失败');
            }
        }
        $this->ajaxResponse(array(), 'fail', '分析失败:无映身对像');
    }

    public function suggest()
    {

        $type = Input::get('type', '');
        $query = Input::get('query', '');
        if ($type && $type === 'SchemaBuilder') {

            $data['suggestions'] = SchemaBuilder::whereRaw('table_name like "%' . $query . '%"')->lists('table_name', 'table_name');
            $data['data'] = array('1', 2, 3, 4, 5);
            $data['query'] = $query;
            return new \Illuminate\Http\JsonResponse($data);
        }
    }

    public function top()
    {
        $id = Input::get('id', '');
        $table = Input::get('table', '');
        if (!$id || !$table)
            $this->ajaxResponse(array(), 'fail', '置顶失败');
        $vm = new ApiModel($table);
        $max = $vm->newQueryWithDeleted()->orderBy('rank', 'desc')->first(array('id', 'rank'));
        if ($vm && (int)$max->id !== (int)$id) {
            if ($current = $vm->newQuery()->find($id)) {
                $current->setTable($table);
                $current->processGeo = false;
                if ($current->update(array('rank' => $max->rank + 1)))
                    $this->ajaxResponse(array(), 'success', '置顶成功');
            }
        }
        $this->ajaxResponse(array(), '', 'fail', '无效置顶');
    }

    public function synchroData()
    {
        $app_id = \Utils\AppChose::getCurrentAppID();
        if (!$app_id) {
            $this->ajaxResponse(array(), 'failed', '登陆信息失效', '失败');
        }
        $env = getenv('CMS_ENV') ? getenv('CMS_ENV') : null;
        if (!$env) {
            $env = "pub";
        }

        $phpPath = Config::get('app.phpPath');
        if (!isset($phpPath)) {
            $phpPath = '/usr/local/php5/bin/php ';
        }
        $filePath = dirname(app_path());
        exec(sprintf('nohup %s %s/artisan  --type=save check --env=%s --app=%s  >> /dev/null  &', $phpPath, $filePath, $env, $app_id));
        sleep(1);
        return Redirect::action('CmsController@index');
    }

    public function refreshTimingCheck()
    {
        $app_id = \Utils\AppChose::getCurrentAppID();
        if (!$app_id) {
            $this->ajaxResponse(array(), 'failed', '登陆信息失效', '失败');
        }
        $env = getenv('CMS_ENV') ? getenv('CMS_ENV') : null;
        if (!$env) {
            $env = "pub";
        }

        $phpPath = Config::get('app.phpPath');
        if (!isset($phpPath)) {
            $phpPath = '/usr/local/php5/bin/php ';
        }
        $filePath = dirname(app_path());
        exec(sprintf('nohup %s %s/artisan  check --env=%s --app=%s>> /dev/null  &', $phpPath, $filePath, $env, $app_id));
        sleep(1);
        $this->ajaxResponse(array(), 'success', 'success', '成功');
    }


}

