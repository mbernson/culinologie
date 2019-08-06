<?php namespace App\Helper;
	
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

final class RecipeSearch
{
    private $params = [
        'cookbook' => null,
        'category' => null,
        'title' => null,
        'query' => null,
    ];

    private $cookbook = '*';

    public function __construct(array $params = [])
    {
        $this->params = array_merge($this->params, $params);
    }

    public function buildQuery(Request $request, $query = null)
    {
        if ($query == null) {
            $query = Recipe::query();
        }
        
        if ($request->has('liked') && Auth::check()) {
	        $query->whereIn('id', function ($q) {
		        $q->select('recipe_id')
			        ->from('recipe_bookmarks')
			        ->where('user_id', Auth::user()->getKey());
	        });
        }

        if ($request->has('query')) {
            $term = $request->get('query');
            $query->where(function ($q) use ($term) {
                $q->where('recipes.description', 'like', "%$term%")
                  ->orWhere('recipes.presentation', 'like', "%$term%");
            });
            $this->params['query'] = $term;
        }

        if ($request->has('title')) {
            $title = $request->get('title');
            $query->where('recipes.title', 'like', "%$title%");
            $this->params['title'] = $title;
        }

        if ($request->has('category')) {
            $category = $request->get('category');
            if ($category != '*') {
                $query->where('category', '=', $category);
                $this->params['category'] = $category;
            }
        }

        if ($this->cookbook != '*') {
            $query->where('cookbook', '=', $this->cookbook);
            $this->params['cookbook'] = $this->cookbook;
        } elseif ($request->has('cookbook') && $request->get('cookbook') != '*') {
            $cookbook = $request->get('cookbook');
            $query->where('cookbook', '=', $cookbook);
            $this->params['cookbook'] = $cookbook;
        }

        return $query;
    }

    public function shouldHideCookbooks()
    {
        return $this->cookbook != '*';
    }

    public function setCookbook($cookbook)
    {
        $this->cookbook = $cookbook;
    }

    public function getParams()
    {
        return $this->params;
    }
}
