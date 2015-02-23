<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Database\DatabaseManager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;

use App\Models\Recipe;
use App\Models\Ingredient;

class RecipesController extends Controller {

    private $recipes;

    private static $per_page = 20;

    public function __construct(DatabaseManager $recipes) {
        $this->recipes = $recipes;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($cookbook = '*')
	{
            $lang = Input::get('lang', 'uk');
            $params = [
                'lang' => $lang,
                'cookbook' => null,
                'category' => null,
                'title' => null,
            ];

            $categories = $this->recipes->table('recipes')
                ->select('category')
                ->where('language', '=', $lang)
                ->groupBy('category')
                ->lists('category');

            $recipes = $this->recipes->table('recipes')
                ->where('language', '=', $lang)
                ->orderBy('tracking_nr', 'asc')
                ->orderBy('created_at', 'desc');

            $title = null;
            if(Input::has('title')) {
                $title = Input::get('title');
                $recipes->where('title', 'like', "%$title%");
                $params['title'] = $title;
            }

            $category = null;
            if(Input::has('category')) {
                $category = Input::get('category');
                if($category != '*') {
                    $recipes->where('category', '=', $category);
                    $params['category'] = $category;
                }
            }

            if($cookbook == '*') {
                $cookbooks = $this->recipes->table('cookbooks')->get();
            } else {
                $cookbooks = null;
            }

            if(Input::has('cookbook')) {
                $cookbook = Input::get('cookbook');
            }

            if($cookbook != '*') {
                $recipes->where('cookbook', '=', $cookbook);
                $params['cookbook'] = $cookbook;
            }

            Session::flash('return_url', route('recipes.index', $params));

            return view('recipes.index')
                ->with('recipes', $recipes->paginate(static::$per_page)
                    ->appends($params))
                ->with('language', $lang)
                ->with('title', $title)

                ->with('categories', $categories)
                ->with('cookbooks', $cookbooks)
                ->with('params', $params);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
            return view('recipes.create')->withRecipe(new Recipe);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($cookbook = null)
	{
            $recipe = new Recipe();

            if($cookbook != null)
                $recipe->cookbook = $cookbook;

            if($this->updateRecipe($recipe))
                return redirect()->route('recipes.show', ['recipes' => $recipe->tracking_nr]);
            else
                dd($recipe);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            $query = Recipe::where('tracking_nr', '=', $id);

            if(Input::has('lang'))
                $query->where('language', '=', Input::get('lang'));

            $recipe = $query->first();

            if(!$recipe) abort(404);

            $ingredients = $this->recipes->table('ingredients')
                ->select('text', 'header', 'amount', 'unit')
                ->where('recipe_id', '=', $recipe->id)
                ->get();

            $cookbook = $this->recipes->table('cookbooks')
                ->where('slug', '=', $recipe->cookbook)
                ->first();

            $groups = Collection::make($ingredients)->groupBy('header');

            return view('recipes.show')
                ->with('recipe', $recipe)
                ->with('cookbook', $cookbook)
                ->with('ingredients', $ingredients)
                ->with('ingredient_groups', $groups);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
            $lang = Input::get('lang', 'uk');
            $recipe = Recipe::where('tracking_nr', '=', $id)
                ->where('language', '=', $lang)
                ->first();

            if(!$recipe) abort(404);

            return view('recipes.create')
                ->withRecipe($recipe);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $tracking_nr
	 * @return Response
	 */
	public function update($tracking_nr)
	{
            $lang = Input::get('lang');
            $recipe = Recipe::where('tracking_nr', '=', $tracking_nr)
                ->where('language', '=', $lang)
                ->first();

            if(!$recipe) abort(404);

            $del = $this->recipes->table('ingredients')
                ->where('recipe_id', '=', $recipe->id)->delete();

            if($this->updateRecipe($recipe))
                return redirect()->route('recipes.show', ['recipes' => $recipe->tracking_nr]);
            else
                dd($recipe);
	}

        private function updateRecipe(Recipe $recipe) {
            $input = Input::only('title', 'people',
                'presentation', 'year', 'season',
                'cookbook', 'category', 'temperature');

            $recipe->fill($input);

            if(empty($recipe->tracking_nr))
                $recipe->tracking_nr = $this->recipes->table('recipes')->max('tracking_nr') + 1;

            if(Input::has('lang'))
                $recipe->language = Input::get('lang');

            if(Input::has('directions'))
                $recipe->description = Input::get('directions');

            // Override the category if the user provided one.
            if(!empty(Input::get('category_alt')))
                $recipe->category = Input::get('category_alt');

            $saved = $recipe->save();

            $ingredients_saved = $recipe->addIngredientsFromText(Input::get('ingredients'));

            return $saved && $ingredients_saved;
        }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
