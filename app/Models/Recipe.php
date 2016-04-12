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
    
    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'recipe_tracking_nr', 'tracking_nr');
    }

    // Global scopes

    use HasVisibilities;

    // Helpers
    
    public function getRating($output = "average")
    {
        $ratings = $this->comments();
        $count = $ratings->whereNotNull('rating')->count();
        $average = number_format($ratings->avg('rating'),1);
        switch($output){
            case 'average':
                return $average;
                break;
            case 'count':
                return $count;
                break;
            case 'array':
                return ['review_count'=>$count, 'average'=>$average];
                break;
            case 'html_stars':
                $output ='';
                for($i=1;$i<=5;$i++) {
                  if($i <= $average) {
                	$output .= '<i class="fa fa-star"></i>';
                  } else {
                	$output .= '<i class="fa fa-star-o"></i>';
                  }
                }
                return $output;
                break;
        }
    }
    
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

    public static function parseIngredientsFromText($text) {
        $header = null;
        $ingredients = [];

        foreach (preg_split("/((\r?\n)|(\r\n?))/", $text) as $line) {
            $line = trim($line);

            if (preg_match('/^###?#? ?[\w|\d| ]+/', $line)) {
                $header = trim(preg_replace('/^###?#?/', '', $line));
            } else {
                $ingredients[] = Ingredient::createFromLine($line, $header);
            }
        }

        return $ingredients;
    }

    public function saveIngredientsFromText($text)
    {
        if (empty($text)) {
            return true;
        }

        return $this->ingredients()->saveMany(static::parseIngredientsFromText($text));
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
