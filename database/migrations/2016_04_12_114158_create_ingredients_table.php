<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIngredientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ingredients', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->integer('recipe_id')->unsigned()->index('index_recipe_id');
			$table->text('text', 65535);
			$table->string('amount')->nullable();
			$table->string('unit', 32)->nullable();
			$table->string('header')->nullable();
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ingredients');
	}

}
