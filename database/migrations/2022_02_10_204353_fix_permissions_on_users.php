<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_approved')->default(false);
        });
        User::query()->cursor()->each(function (User $user) {
            $user->is_approved = $user->approved === 1;
            $user->save();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('approved');
        });
    }
};
