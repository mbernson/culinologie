<?php namespace App\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\DatabaseManager;

final class RecipesComposer {

    private $db;

    public function __construct(DatabaseManager $db) {
        $this->db = $db;
    }

    public function compose(View $view) {
        $view->with('languages', [
            'nl' => 'Nederlands',
            'uk' => 'Engels (Groot Brittanië)',
            'us' => 'Engels (Amerikaans)',
            'cs' => 'Spaans',
            'ct' => 'Catalaans',
        ]);

    }

    public function temperatures(View $view) {
        $view->with('temperatures', [
            'HOT' => 'Warm gerecht',
            'ROOM' => 'Kamertemperatuur',
            'COLD' => 'Koud gerecht',
            'FROZEN' => 'Bevroren gerecht',
        ]);
    }

    public function seasons(View $view) {
        $view->with('seasons', [
            'ALL YEAR' => 'Het hele jaar door',
            'SPRING' => 'Lente',
            'SUMMER' => 'Zomer',
            'FALL' => 'Herfst',
            'WINTER' => 'Winter',
        ]);
    }

    public function categories(View $view) {
        $view->with('categories', 
            $this->db->table('recipes')
                ->select('category')
                /* ->where('language', '=', $lang) */
                ->groupBy('category')
                ->orderBy('language', 'asc')
                ->lists('category')
        );
    }

    public function cookbooks(View $view) {
        $view->with('cookbooks', 
            $this->db->table('cookbooks')
            ->select('title', 'slug')
            ->orderBy('id', 'desc')
            ->get()
        );
    }


}
