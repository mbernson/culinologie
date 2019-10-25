<?php

use App\Models\Recipe;
use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Wessel Stam <wessel@blendis.nl>
 */
class RecipeCategoryManyToMany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('recipe_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('recipe_id')->unsigned();
            $table->integer('category_id')->unsigned();

            $table->foreign('recipe_id')->references('id')->on('recipes');
            $table->foreign('category_id')->references('id')->on('categories');
        });

        // Get all the unique categories and migrate them to the new categories table
        $categories = Recipe::all()->pluck('category')->toArray();
        $categories = array_filter(array_unique($categories));

        // Store the new category models into a map to convert the old values later
        $newCategoryMap = [];
        foreach ($categories as $category) {
            $newCategoryMap[$category] = Category::create(['name' => $category]);
        }

        /* @var Recipe[] $recipes */
        $recipes = Recipe::all();

        foreach ($recipes as $recipe) {
            if ($recipe->category && array_key_exists($recipe->category, $newCategoryMap)) {
                $recipe->categories()->attach($newCategoryMap[$recipe->category]->id);
            }
        }

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Dont event bother...
    }
}
