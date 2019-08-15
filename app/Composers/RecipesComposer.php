<?php namespace App\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Auth;
use App\Models\Cookbook;

final class RecipesComposer
{

    private $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    private $static_data = [
        'languages' => [
            'nl' => 'Nederlands',
            'uk' => 'Engels (Groot Brittanië)',
            'us' => 'Engels (Amerikaans)',
            'cs' => 'Spaans',
            'ct' => 'Catalaans',
            'fr' => 'Frans',
            'de' => 'Duits',
            'pl' => 'Pools',
            'pt' => 'Portugees',
        ],
        'visibilities' => [
            0 => 'Voor iedereen',
            1 => 'Alleen voor mijzelf',
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

    public function compose(View $view)
    {
        foreach ($this->static_data as $k => $v) {
            $view->with($k, $v);
        }
    }

    public function categories(View $view)
    {
        $view->with('categories',
            $this->db->table('recipes')
            ->select('category')
            ->groupBy('category')
            ->orderBy('category', 'asc')
            ->pluck('category')
        );
    }

    public function allCookbooks(View $view)
    {
        $view->with('cookbooks',
            Cookbook::select('id', 'title', 'slug')
            ->orderBy('id', 'desc')
            ->get()
        );
    }

    public function userCookbooks(View $view)
    {
        $view->with('cookbooks',
            $this->db->table('cookbooks')
            ->where('user_id', '=', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get()
        );
    }
}
