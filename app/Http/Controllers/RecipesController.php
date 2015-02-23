<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Database\DatabaseManager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
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
            return view('recipes.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($cookbook = null)
	{
            $input = Input::only('title', 'people',
                'presentation',
                'cookbook', 'category', 'temperature');

            $recipe = new Recipe($input);
            $recipe->tracking_nr = $this->recipes->table('recipes')->max('tracking_nr') + 1;

            $recipe->language = Input::get('lang', 'nl');
            if(Input::has('directions'))
                $recipe->description = Input::get('directions');

            $saved = $recipe->save();

            $ingredients = [];
            foreach(preg_split("/((\r?\n)|(\r\n?))/", Input::get('ingredients')) as $line){
                $line = trim($line);
                if(!empty($line))
                    $ingredients[] = Ingredient::createFromLine($line);
            }

            $recipe->ingredients()->saveMany($ingredients);

            if($saved) {
                return redirect()->route('recipes.show', ['recipes' => $recipe->tracking_nr]);
            } else {
                dd($recipe, $ingredients);
            }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            $lang = Input::get('lang', 'nl');

            $recipe = $this->recipes->table('recipes')
                ->where('tracking_nr', '=', $id)
                ->where('language', '=', $lang)
                ->first();

            if(!$recipe) abort(404);

            $recipe->description_html  = \Parsedown::instance()->text($recipe->description);
            $recipe->presentation_html = \Parsedown::instance()->text($recipe->presentation);

            $ingredients = $this->recipes->table('ingredients')
                ->select('text', 'header', 'amount', 'unit')
                ->where('recipe_id', '=', $recipe->id)
                ->get();

            $groups = Collection::make($ingredients)->groupBy('header');

            return view('recipes.show')
                ->with('recipe', $recipe)
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
            $recipe = $this->recipes->table('recipes')
                ->where('tracking_nr', '=', $id)
                ->where('language', '=', $lang)
                ->first();

            if(!$recipe) abort(404);

            return view('recipes.create')
                ->withRecipe($recipe);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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
