<?php

use Illuminate\Database\Migrations\Migration;

class CreateTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS cookbooks_recipes_sum_insert;');
        DB::unprepared('DROP TRIGGER IF EXISTS cookbooks_recipes_sum_delete;');
            
            
        \DB::unprepared('CREATE TRIGGER cookbooks_recipes_sum_insert AFTER INSERT ON recipes
        FOR EACH ROW
        BEGIN
        
        UPDATE cookbooks c SET c.recipes_count = (
        	SELECT count(r.id) FROM recipes r WHERE r.cookbook = NEW.cookbook
        ) WHERE c.slug = NEW.cookbook;
        
        END'); 
        
        \DB::unprepared('CREATE TRIGGER cookbooks_recipes_sum_delete AFTER DELETE ON recipes
        FOR EACH ROW
        BEGIN
        
        UPDATE cookbooks c SET c.recipes_count = (
        	SELECT count(r.id) FROM recipes r WHERE r.cookbook = OLD.cookbook
        ) WHERE c.slug = OLD.cookbook;
        
        END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS cookbooks_recipes_sum_insert;');
        DB::unprepared('DROP TRIGGER IF EXISTS cookbooks_recipes_sum_delete;');

    }
}
