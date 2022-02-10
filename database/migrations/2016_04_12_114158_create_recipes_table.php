<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tracking_nr')->unsigned()->index('index_tracking_number');
            $table->char('language', 4)->default('EN')->index('index_language');
            $table->string('title')->default('');
            $table->integer('people')->unsigned()->default(0);
            $table->string('temperature')->nullable()->default('');
            $table->string('category')->nullable()->default('');
            $table->string('season')->nullable();
            $table->integer('year')->unsigned();
            $table->timestamps();
            $table->text('description')->nullable();
            $table->text('presentation')->nullable();
            $table->string('cookbook')->nullable()->default('')->index('fk_cookbook_slug');
            $table->boolean('visibility')->nullable()->default(0);
            $table->integer('user_id')->unsigned()->index('fk_recipe_user_id');
            $table->unique(['tracking_nr', 'language'], 'uniq_elbulli_nr_language');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('recipes');
    }
}
