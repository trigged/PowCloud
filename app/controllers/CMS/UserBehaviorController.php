<?php

class UserBehaviorController extends CmsBaeController
{


    static $Sql = "

  USE `%s`;
  CREATE TABLE IF NOT EXISTS `user_%s` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NULL,
  `data_id` INT NULL,
  `user` VARCHAR(45) NULL COMMENT '冗余',
  `user_name` VARCHAR(45) NULL,
  `rank` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,

  PRIMARY KEY (`id`),
  INDEX `user_id` (`user_id` ASC),
  INDEX `data_id` (`data_id` ASC))
ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    public $nav = 'system';

    public static function getBehaviorName($behavior)
    {
        return sprintf('user_%s', $behavior);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $tables = SchemaBuilder::where('types', 'user')->get();
        Path::getPathTree();
        $paths = Path::$pathTreeListOptions;
        unset($paths[-1]);
        return $this->render('UserBehavior.index', array('tables' => $tables, 'pathTreeListOptions' => $paths));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //设置默认值
        $input = array_merge(array('index' => 1, 'update' => 1, 'delete' => 1, 'create' => 1),
            Input::all());

        $table = new SchemaBuilder($input);
        $table->scene = 'create';
        $table->types = 'user';

        //TODO add event for after validator
        if ((int)$table->path_id !== -1) {
            if (SchemaBuilder::checkPathAndTableName($table->path_id, $table->table_name)) {
                $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS, '路径已被绑定,请重新选择路径');
            }
        } elseif (SchemaBuilder::where('table_name', $table->table_name)->count()) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);
        }

        try {
            DB::connection('models')->getPdo()->beginTransaction();
            if (!$re = $table->save()) {
                throw new Exception($re);
            }
            $group_id = $this->getGroupID();
            $g_option = new GroupOperation();
            $g_option->group_id = $group_id;
            $g_option->read = (int)GroupOperation::HAS_RIGHT;
            $g_option->edit = (int)GroupOperation::HAS_RIGHT;
            $g_option->models_id = (int)$table->id;
            $g_option->save();
            if ($table->errors) {
                throw new Exception($table->errors[0]);
            }
            \Utils\DBMaker::runSql(sprintf(self::$Sql, \Utils\AppChose::getDbModelsName($this->app_id), $table->table_name));
            DB::connection('models')->getPdo()->commit();
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS);
        } catch (Exception $e) {
            DB::connection('models')->getPdo()->rollBack();
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_DO_FAILED, $e->getMessage());
        }


        //todo create user_xx table

        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
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
        //
    }


}