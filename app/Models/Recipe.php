<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ingredient;
use App\Traits\HasVisibilities;
use Parsedown;

final class Recipe extends Model {

    const VISIBILITY_PUBLIC = 0;
    const VISIBILITY_PRIVATE = 1;
    const VISIBILITY_LOGGED_IN = 2;

    protected $fillable = ['title', 'people', 'year', 'season',
                'description', 'presentation', 'lang',
                'cookbook', 'category', 'temperature', 'visibility'];

    // Relations

    public function ingredients() {
        return $this->hasMany('App\Models\Ingredient');
    }

    public function cookbook_rel() {
        return $this->belongsTo('App\Models\Cookbook', 'cookbook', 'slug');
    }

    // Scopes
   
    // Global scopes

    use HasVisibilities; 

    // Helpers
    
    public static function categories(array $languages) {
        return static::select('category')
            ->whereIn('language', $languages)
            ->groupBy('category')
            ->lists('category');
    }

    // Getters

    public function textIngredients() {
        $output = '';
        foreach($this->ingredients as $ingredient) {
            $output .= $ingredient->text . "\n";
        }
        return $output;
    }

    public function addIngredientsFromText($text) {
        if(empty($text)) return true;

        $ingredients = [];

        foreach(preg_split("/((\r?\n)|(\r\n?))/", $text) as $line){
            $line = trim($line);
            if(!empty($line))
                $ingredients[] = Ingredient::createFromLine($line);
        }

        return $this->ingredients()->saveMany($ingredients);
    }

    public function getImages() {
        return [
            "/uploads/pictures/{$this->tracking_nr}.jpg" => 'Foto',
            "/uploads/detail/{$this->tracking_nr}.jpg" => 'Detail foto',
        ];
    }

    public function getHtmlDescription() {
        return Parsedown::instance()->text($this->description);
    }

    public function getHtmlPresentation() {
        return Parsedown::instance()->text($this->presentation);
    }

}
