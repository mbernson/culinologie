<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Recipe extends Model {

    protected $table = 'recipes';
    protected $fillable = ['title', 'people', 'year',
                'description', 'presentation', 'lang',
                'cookbook', 'category', 'temperature'];

    public function ingredients() {
        return $this->hasMany('App\Models\Ingredient');
    }
}
