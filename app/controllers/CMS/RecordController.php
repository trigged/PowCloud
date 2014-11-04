<?php

class RecordController extends BaseController
{

    protected $operation = array();

    public function __construct()
    {

        if (Auth::guest() || $this->getRoles() !== 3) {
            $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//            $this->ajaxResponse(array(), 'fail', '恢复失败,未登陆');
        }

        $this->operation = $this->getOption();
    }

    public function recover($id)
    {

        $record = Record::find($id);
        $targetData = '';
        if ($record) {
            if (!$record->connect) {
                $class = ucfirst($record->table_name);
                $targetData = $class::find($record->content_id);
                $targetData->oldData = $targetData->toArray();
            } elseif ($record->connect === 'ApiModel') {

                $this->checkOperation($record, 2);

                $target = new ApiModel($record->table_name);
                $targetData = $target->newQuery()->find($record->content_id);
                $targetData->setTable($record->table_name);
                $targetData->oldData = $targetData->toArray();
            }

            if ($targetData->update(array_except(json_decode($record->content, true), array('id')))) {
                Log::info('恢复' . $record->table_name . ':' . $record->content_id . ':' . $record->content);
//                $this->ajaxResponse(array(), 'success', '恢复成功');
                $this->ajaxResponse(BaseController::$_SUCCESS_TEMPLATE);
            }
        }

        $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//        $this->ajaxResponse(array(), 'fail', '恢复失败');
    }

    public function destroy($id)
    {
        $record = Record::find($id);
        if (!$record) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);
        }

        $this->checkOperation($record, 2);

        if ($record->delete()) {
            Log::info('删除record表记录' . $id);
            $this->ajaxResponse(BaseController::$_SUCCESS_TEMPLATE);
//            $this->ajaxResponse(array(), 'success', '删除成功');
        }
        $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//        $this->ajaxResponse(array(), 'fail', '删除失败');
    }

    public function detail($id)
    {

        $record = Record::find($id);

        if ($record) {
            echo '<pre>';
            $content = json_decode($record->content, true);
            if ($content) {
                foreach ($content as $name => $value) {
                    echo '<p>' . $name . ':' . htmlspecialchars($value) . "</p>";
                }
            }

            echo '</pre>';
        }
    }

    protected function checkOperation($record, $operation)
    {

        if ($record->connect === 'ApiModel') {
            if (!$table = SchemaBuilder::whereRaw('table_name="' . $record->table_name . '"')->get()->first()) {
                $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);
//                $this->ajaxResponse(array(), 'fail', '表不存在');
            }
            \Utils\CMSLog::debug(json_encode($this->operation) . ':' . $table->id);
            if (!isset($this->operation[$table->id]) || (int)$this->operation[$table->id]['edit'] !== $operation) {
                $this->ajaxResponse(BaseController::$_FAILED_TEMPLATE);
//                $this->ajaxResponse(array(), 'fail', '无权限');
            }
        }
    }
}