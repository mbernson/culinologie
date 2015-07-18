<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasVisibilities;

final class Cookbook extends Model
{

    use HasVisibilities;

    protected $fillable = ['title', 'slug', 'user_id'];

    public $timestamps = false;

    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
