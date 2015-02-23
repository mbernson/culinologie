<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ingredient;
use Parsedown;

final class Recipe extends Model {

    protected $table = 'recipes';
    protected $fillable = ['title', 'people', 'year', 'season',
                'description', 'presentation', 'lang',
                'cookbook', 'category', 'temperature'];

    public function ingredients() {
        return $this->hasMany('App\Models\Ingredient');
    }

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

    public function getHtmlDescription() {
        return Parsedown::instance()->text($this->description);
    }

    public function getHtmlPresentation() {
        return Parsedown::instance()->text($this->presentation);
    }

}
