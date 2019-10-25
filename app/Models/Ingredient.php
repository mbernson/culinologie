<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final /**
 * App\Models\Ingredient
 *
 * @property int $id
 * @property int $recipe_id
 * @property string $text
 * @property string|null $amount
 * @property string|null $unit
 * @property string|null $header
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Recipe $recipe
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ingredient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ingredient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ingredient query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ingredient whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ingredient whereHeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ingredient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ingredient whereRecipeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ingredient whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ingredient whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Ingredient whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ingredient extends Model
{

    protected $table = 'ingredients';
    public $timestamps = false;
    protected $dates = ['updated_at'];

    public static function createFromLine($text, $header = null)
    {
        $ingredient = new static();
        // Strip markdown list characters
        $text = preg_replace('/^(\*|-)\ /', '', $text);
        $ingredient->text = $text;
        $ingredient->header = $header;
        $ingredient->parse();

        return $ingredient;
    }

    public function recipe()
    {
        return $this->belongsTo('App\Models\Recipe');
    }

    public function parse()
    {
        $this->parse_amount();
        $this->parse_unit();
    }

    private function parse_amount()
    {
        $matches = [];
        if (preg_match('/^[\d|\.|,]+/', $this->text, $matches)) {
            $this->amount = $matches[0];
        }
    }

    private function parse_unit()
    {
        $matches = [];
        if (preg_match('/^[\d|\.|,]+\ ?\w+\ /', $this->text, $matches)) {
            $parts = explode(' ', trim($matches[0]));
            if(count($parts) == 1) {
                preg_match('/[A-Za-z]+/', $parts[0], $matches);
                $this->unit = $matches[0];
            } elseif(count($parts) > 1) {
                $this->unit = $parts[1];
            }
        }
    }

}
