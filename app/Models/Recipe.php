<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ingredient;
use App\Traits\HasVisibilities;
use Parsedown;

final class Recipe extends Model
{

    protected $fillable = ['title', 'people', 'year', 'season',
        'description', 'presentation', 'lang', 'cookbook',
        'category', 'temperature', 'visibility', 'tracking_nr'];

    // Relations

    public function ingredients()
    {
        return $this->hasMany('App\Models\Ingredient');
    }

    public function cookbook_rel()
    {
        return $this->belongsTo('App\Models\Cookbook', 'cookbook', 'slug');
    }

    // Global scopes

    use HasVisibilities;

    // Helpers
    
    public static function categories(array $languages)
    {
        return static::select('category')
            ->distinct()
            ->whereIn('language', $languages)
            ->whereRaw('category is not null')
            ->where('category', '!=', '')
            ->orderBy('category')
            ->orderBy('language')
            ->lists('category')
            ->all();
    }

    // Getters

    public function getDescriptionAttribute()
    {
        if (array_key_exists('description', $this->attributes)) {
            return preg_replace('/### ?(\w|\d| )+\n?$/', '', $this->attributes['description']);
        } else {
            return '';
        }
    }

    public function textIngredients()
    {
        return join("\n", $this->ingredients->lists('text')->all());
    }

    private function parseIngredients() {

    }

    public function addIngredientsFromText($text)
    {
        if (empty($text)) {
            return true;
        }

        $header = null;
        $ingredients = [];

        foreach (preg_split("/((\r?\n)|(\r\n?))/", $text) as $line) {
            $line = trim($line);

            if (preg_match('/^###?#? ?[\w|\d| ]+ ?$/', $line)) {
                $header = trim(preg_replace('/^###?#? /', '', $line));
            } else {
                $ingredients[] = Ingredient::createFromLine($line, $header);
            }
        }

        return $this->ingredients()->saveMany($ingredients);
    }

    public function getImages()
    {
        return [
            "/uploads/pictures/{$this->tracking_nr}.jpg" => 'Overzicht',
            "/uploads/detail/{$this->tracking_nr}.jpg" => 'Detail',
        ];
    }

    public function getHtmlDescription()
    {
        return Parsedown::instance()->text($this->description);
    }

    public function getHtmlPresentation()
    {
        return Parsedown::instance()->text($this->presentation);
    }
}
