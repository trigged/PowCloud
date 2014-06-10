<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToGeoShortcut extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('geo_shortcut', function(Blueprint $table)
		{
            $table->integer('type')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('geo_shortcut', function(Blueprint $table)
		{
            $table->dropColumn('type');
		});
	}

}