<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use function bcrypt;

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
    ];

    public function bookmarks()
    {
        return $this->belongsToMany(\App\Models\Recipe::class, 'recipe_bookmarks', 'user_id', 'recipe_id');
    }

    public function lovedRecipes()
    {
        return $this->bookmarks()->wherePivot('list', 'Loved');
    }

    public function hasLovedRecipe(Recipe $recipe)
    {
        return $this->lovedRecipes()->where('id', $recipe->id)->count() > 0;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function isAdmin()
    {
        return $this->admin == 1;
    }

    public function isApproved()
    {
        return $this->approved == 1;
    }
}
