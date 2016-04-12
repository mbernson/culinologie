<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCookbooksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cookbooks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title')->default('');
			$table->string('slug')->default('')->unique('slug');
			$table->integer('user_id')->unsigned()->index('fk_user_id');
			$table->integer('recipes_count')->unsigned()->default(0);
			$table->boolean('visibility')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cookbooks');
	}

}
