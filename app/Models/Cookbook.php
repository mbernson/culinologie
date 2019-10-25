<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasVisibilities;

final /**
 * App\Models\Cookbook
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property int $user_id
 * @property int $recipes_count
 * @property int $visibility
 * @property-read \App\User $owner
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cookbook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cookbook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cookbook query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cookbook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cookbook whereRecipesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cookbook whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cookbook whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cookbook whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Cookbook whereVisibility($value)
 * @mixin \Eloquent
 */
class Cookbook extends Model
{

    use HasVisibilities;

    protected $fillable = ['title', 'slug', 'user_id'];

    public $timestamps = false;

    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
