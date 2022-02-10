<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final /**
 * App\Models\Comment
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $body
 * @property int|null $rating
 * @property int $user_id
 * @property int $recipe_tracking_nr
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $author
 * @property-read \App\Models\Recipe $recipe
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereRecipeTrackingNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereUserId($value)
 * @mixin \Eloquent
 */
class Comment extends Model
{

    protected $fillable = ['title', 'body', 'user_id','recipe_id','rating'];

    public $timestamps = true;

    public function author()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
    
    public function recipe()
    {
        return $this->belongsTo(\App\Models\Recipe::class, 'recipe_tracking_nr', 'tracking_nr');
    }
    
    public function getHtmlStars()
    {
        $rating = $this->rating;
        $output ='';
        for($i=1;$i<=5;$i++) {
          if($i <= $rating) {
        	$output .= '<i class="fa fa-star"></i>';
          } else {
        	$output .= '<i class="fa fa-star-o"></i>';
          }
        }
        return $output;
        
    }
}
