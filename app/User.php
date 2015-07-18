<?php namespace App;

use App\Models\Recipe;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function bookmarks() {
        return $this->belongsToMany('App\Models\Recipe', 'recipe_bookmarks', 'user_id', 'recipe_id');
    }

    public function lovedRecipes() {
        return $this->bookmarks()->wherePivot('list', 'Loved');
    }

    public function hasLovedRecipe(Recipe $recipe) {
        return $this->lovedRecipes()->where('id', $recipe->id)->count() > 0;
    }

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = bcrypt($value);
    }

    public function isAdmin() {
        return $this->admin == 1;
    }

    public function isApproved() {
        return $this->approved == 1;
    }
}
