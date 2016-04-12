<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title')->nullable();
			$table->text('body', 65535)->nullable();
			$table->integer('rating')->unsigned()->nullable();
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->integer('recipe_tracking_nr')->unsigned();
			$table->timestamps();
			$table->index(['recipe_tracking_nr','rating'], 'recipe_tracking_nr');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('comments');
	}

}
