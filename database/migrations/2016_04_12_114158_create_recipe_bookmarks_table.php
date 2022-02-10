<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecipeBookmarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipe_bookmarks', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->string('list')->default('Saved');
            $table->integer('recipe_id')->unsigned();
            $table->primary(['user_id', 'list', 'recipe_id']);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('recipe_bookmarks');
    }
}
