<?php

class UserBehaviorController extends CmsBaeController {


    static $Sql = "CREATE TABLE IF NOT EXISTS `user_xx` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NULL,
  `data_id` INT NULL,
  `user_name` VARCHAR(45) NULL COMMENT '冗余',
  PRIMARY KEY (`id`),
  INDEX `user_id` (`user_id` ASC),
  INDEX `data_id` (`data_id` ASC))
ENGINE = InnoDB";

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
        $input = array_merge(array('index' => null, 'update' => null, 'delete' => null, 'create' => null),
            Input::all());

        $table = new SchemaBuilder($input);
        $table->scene = 'create';
        //TODO add event for after validator
        if ((int)$table->path_id !== -1) {
            if (SchemaBuilder::checkPathAndTableName($table->path_id,$table->table_name)) {
                $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS, '路径已被绑定,请重新选择路径');
            }
        } elseif (SchemaBuilder::where('table_name', $table->table_name)->count()) {
            $this->ajaxResponse(BaseController::$FAILED, BaseController::$MESSAGE_HAS_EXISTS);
        }


        if ($table->save()) {
            Log::info(Auth::user()->name . '更新表' . $table->table_name);
            //set group option
            $group_id = $this->getGroupID();
            $g_option = new GroupOperation();
            $g_option->group_id = $group_id;
            $g_option->read = (int)GroupOperation::HAS_RIGHT;
            $g_option->edit = (int)GroupOperation::HAS_RIGHT;
            $g_option->models_id = (int)$table->id;
            $g_option->save();
            $this->ajaxResponse(BaseController::$SUCCESS, BaseController::$MESSAGE_DO_SUCCESS, '', URL::action('SchemaBuilderController@index'));

        }
        if($table->errors){
            $this->ajaxResponse(BaseController::$FAILED, $table->errors[0]);;
        }



		//todo create user_xx table

        //
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}