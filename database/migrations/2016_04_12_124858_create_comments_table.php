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
			$table->integer('user_id')->unsigned();
			$table->integer('recipe_tracking_nr')->unsigned();
			$table->timestamps();
			$table->index(['recipe_tracking_nr','rating'], 'recipe_tracking_nr');
			$table->foreign('recipe_tracking_nr', 'comments_ibfk_2')->references('tracking_nr')
					->on('recipes')
					->onUpdate('CASCADE')
					->onDelete('CASCADE');
			$table->foreign('user_id', 'comments_ibfk_3')->references('id')
					->on('users')
					->onUpdate('CASCADE')
					->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('comments', function(Blueprint $table) {
			$table->dropForeign('comments_ibfk_2');
			$table->dropForeign('comments_ibfk_3');
		});
		Schema::drop('comments');
	}

}
