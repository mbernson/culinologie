<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ingredient;
use App\Traits\HasVisibilities;
use Parsedown;

final /**
 * App\Models\Recipe
 *
 * @property int $id
 * @property int $tracking_nr
 * @property string $language
 * @property string $title
 * @property int $people
 * @property string|null $temperature
 * @property string|null $season
 * @property int $year
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $description
 * @property string|null $presentation
 * @property string|null $cookbook
 * @property int|null $visibility
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Cookbook|null $cookbookRel
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ingredient[] $ingredients
 * @property-read int|null $ingredients_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereCookbook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe wherePeople($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe wherePresentation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereSeason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereTrackingNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Recipe whereYear($value)
 * @mixin \Eloquent
 */
class Recipe extends Model
{

    protected $fillable = ['title', 'people', 'year', 'season',
        'description', 'presentation', 'lang', 'cookbook',
        'category', 'temperature', 'visibility', 'tracking_nr'];

    // Relations

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cookbookRel()
    {
        return $this->belongsTo(Cookbook::class, 'cookbook', 'slug');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'recipe_tracking_nr', 'tracking_nr');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
                return ['review_count'=>$count, 'average'=>$average];
                break;
            case 'html_stars':
                $output ='';
                for ($i=1; $i<=5; $i++) {
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
