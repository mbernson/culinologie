<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToIngredientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ingredients', function(Blueprint $table)
		{
			$table->foreign('recipe_id', 'fk_recipe_id')->references('id')->on('recipes')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ingredients', function(Blueprint $table)
		{
			$table->dropForeign('fk_recipe_id');
		});
	}

}
