<?php

use Illuminate\Database\Migrations\Migration;

class SetOnDeleteCascadeConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE recipes DROP FOREIGN KEY `fk_recipe_user_id`;');
        DB::statement('ALTER TABLE recipes ADD CONSTRAINT `fk_recipe_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;');

        DB::statement('ALTER TABLE cookbooks DROP FOREIGN KEY `fk_user_id`;');
        DB::statement('ALTER TABLE cookbooks ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
