<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function bookmarks()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_bookmarks', 'user_id', 'recipe_id');
    }

    public function lovedRecipes()
    {
        return $this->bookmarks()->wherePivot('list', 'Loved');
    }

    public function hasLovedRecipe(Recipe $recipe): bool
    {
        return $this->lovedRecipes()->where('id', $recipe->id)->exists();
    }

    protected function password(): Attribute
    {
        return new Attribute(
            set: fn($value) => bcrypt($value),
        );
    }
}
