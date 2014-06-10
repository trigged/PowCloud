<?php

use Illuminate\Database\Migrations\Migration;

class AlterFormsColumnDefaultValue extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::update('ALTER TABLE `forms` MODIFY COLUMN `default_value` TEXT');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::update("ALTER TABLE `forms` MODIFY COLUMN `default_value` VARCHAR(500) DEFAULT '' ");
	}
}