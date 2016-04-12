<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToRecipesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('recipes', function(Blueprint $table)
		{
			$table->foreign('cookbook', 'fk_cookbook_slug')->references('slug')->on('cookbooks')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('user_id', 'fk_recipe_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('recipes', function(Blueprint $table)
		{
			$table->dropForeign('fk_cookbook_slug');
			$table->dropForeign('fk_recipe_user_id');
		});
	}

}
