<?php namespace App\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\DatabaseManager;

final class RecipesComposer {

    private $db;

    public function __construct(DatabaseManager $db) {
        $this->db = $db;
    }

    private $static_data = [
        'languages' => [
            'nl' => 'Nederlands',
            'uk' => 'Engels (Groot Brittanië)',
            'us' => 'Engels (Amerikaans)',
            'cs' => 'Spaans',
            'ct' => 'Catalaans',
        ],
        'visibilities' => [
            0 => 'Voor iedereen',
            1 => 'Alleen voor jezelf',
            2 => 'Alleen voor ingelogde gebruikers',
        ],
        'temperatures' => [
            'HOT' => 'Warm gerecht',
            'ROOM' => 'Kamertemperatuur',
            'COLD' => 'Koud gerecht',
            'FROZEN' => 'Bevroren gerecht',
        ],
        'seasons' => [
            'ALL YEAR' => 'Het hele jaar door',
            'SPRING' => 'Lente',
            'SUMMER' => 'Zomer',
            'FALL' => 'Herfst',
            'WINTER' => 'Winter',
        ]
    ];

    public function compose(View $view) {
        foreach($this->static_data as $k => $v) {
            $view->with($k, $v);
        }
    }

    public function categories(View $view) {
        $view->with('categories', 
            $this->db->table('recipes')
            ->select('category')
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
