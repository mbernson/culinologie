<?php

namespace App\Composers;

use App\Models\Category;
use App\Models\Cookbook;
use Illuminate\Contracts\View\View;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Auth;

final class RecipesComposer
{
    public function __construct(private readonly DatabaseManager $db)
    {
    }

    private array $static_data = [
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
        $categories = Category::orderBy('name', 'ASC')->get();
        $view->with('categories', $categories);
    }

    public function allCookbooks(View $view)
    {
        $view->with(
            'cookbooks',
            Cookbook::select('id', 'title', 'slug')
            ->orderBy('id', 'desc')
            ->get()
        );
    }

    public function userCookbooks(View $view)
    {
        $view->with(
            'cookbooks',
            $this->db->table('cookbooks')
            ->where('user_id', '=', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get()
        );
    }
}
