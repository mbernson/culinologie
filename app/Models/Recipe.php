<?php

namespace App\Models;

use App\Traits\HasVisibilities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Parsedown;

 class Recipe extends Model
 {
     protected $fillable = ['title', 'people', 'year', 'season',
        'description', 'presentation', 'lang', 'cookbook',
        'category', 'temperature', 'visibility', 'tracking_nr'];

     // Relations

     /**
      * @return HasMany
      */
     public function ingredients()
     {
         return $this->hasMany(Ingredient::class);
     }

     /**
      * @return BelongsTo
      */
     public function cookbookRel()
     {
         return $this->belongsTo(Cookbook::class, 'cookbook', 'slug');
     }

     /**
      * @return HasMany
      */
     public function comments()
     {
         return $this->hasMany(Comment::class, 'recipe_tracking_nr', 'tracking_nr');
     }

     /**
      * @return BelongsToMany
      */
     public function categories()
     {
         return $this->belongsToMany(Category::class, 'recipe_categories');
     }

     // Global scopes

     use HasVisibilities;

     // Helpers

     public function getRating($output = 'average')
     {
         $ratings = $this->comments();
         $count = $ratings->whereNotNull('rating')->count();
         $average = number_format($ratings->avg('rating'), 1);
         switch ($output) {
            case 'average':
                return $average;
                break;
            case 'count':
                return $count;
                break;
            case 'array':
                return ['review_count' => $count, 'average' => $average];
                break;
            case 'html_stars':
                $output = '';
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $average) {
                        $output .= '<i class="fa fa-star"></i>';
                    } else {
                        $output .= '<i class="fa fa-star-o"></i>';
                    }
                }
                return $output;
                break;
        }
     }

     // Getters

     public function getDescriptionAttribute()
     {
         if (array_key_exists('description', $this->attributes)) {
             return preg_replace('/### ?(\w|\d| )+\n?$/', '', $this->attributes['description']);
         }

         return '';
     }

     /**
      * @return string
      */
     public function getFormattedCategoriesAttribute()
     {
         $categories = $this->categories->pluck('name')->toArray();
         return implode(', ', $categories);
     }

     public function textIngredients()
     {
         return join("\n", $this->ingredients->pluck('text')->all());
     }

     public static function parseIngredientsFromText($text)
     {
         $header = null;
         $ingredients = [];

         foreach (preg_split("/((\r?\n)|(\r\n?))/", (string) $text) as $line) {
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
