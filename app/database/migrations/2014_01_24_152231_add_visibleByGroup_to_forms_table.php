<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVisibleByGroupToFormsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('forms', function(Blueprint $table)
		{
			$table->integer('visibleByGroup')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('forms', function(Blueprint $table)
		{
			$table->dropColumn('visibleByGroup');
		});
	}

}