<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Ingredient extends Model
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
        if (preg_match('/^[\d|\.|,]+\ ?\w\ /', $this->text, $matches)) {
            $this->unit = substr(trim($matches[0]), -1);
        }
    }
}
