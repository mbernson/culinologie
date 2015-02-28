<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Database\DatabaseManager;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Input, Session, Auth;

use App\Models\Recipe;
use App\Models\Ingredient;

class RecipesController extends Controller {

    private $db;

    private static $per_page = 20;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($cookbook = '*')
    {
        $languages = Input::get('lang', ['nl', 'uk']);
        $params = [
            'lang[]' => $languages,
            'cookbook' => null,
            'category' => null,
            'title' => null,
        ];

        $recipes = Recipe::select('tracking_nr', 'recipes.title', 'category', 'cookbook', 'language')
            ->whereIn('language', $languages)
            ->orderBy('tracking_nr', 'asc')
            ->orderBy('created_at', 'desc');

        $title = null;
        if(Input::has('title')) {
            $title = Input::get('title');
            $recipes->where('recipes.title', 'like', "%$title%");
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

        if(Input::has('cookbook')) {
            $cookbook = Input::get('cookbook');
        }

        if($cookbook != '*') {
            $recipes->where('cookbook', '=', $cookbook);
            $params['cookbook'] = $cookbook;
        }

        Session::flash('return_url', route('recipes.index', $params));

        return view('recipes.index')
            ->with('recipes', $recipes->paginate(static::$per_page)->appends($params))
            ->with('langs', $languages)
            ->with('title', $title)
            ->with('categories', Recipe::categories($languages))
            ->with('hide_cookbooks', is_string($cookbook) && $cookbook != '*')
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
        $recipe->user_id = Auth::user()->id;

        if($cookbook != null)
            $recipe->cookbook = $cookbook;

        if($this->updateRecipe($recipe))
            return redirect()->route('recipes.show', ['recipes' => $recipe->tracking_nr]);
        else
            abort(500);
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

        $groups = Collection::make($recipe->ingredients)->groupBy('header');

        return view('recipes.show')
            ->with('recipe', $recipe)
            ->with('cookbook', $recipe->cookbook_rel)
            ->with('ingredients', $recipe->ingredients)
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
            ->with('recipe', $recipe);
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

        $del = $this->db->table('ingredients')
            ->where('recipe_id', '=', $recipe->id)->delete();

        if($this->updateRecipe($recipe))
            return redirect()->route('recipes.show', ['recipes' => $recipe->tracking_nr]);
        else
            abort(500);
    }

    private function updateRecipe(Recipe $recipe) {
        $input = Input::only('title', 'people', 'presentation',
            'year', 'season', 'cookbook', 'category', 'temperature', 'visibility'
        );

        $recipe->fill($input);

        if(empty($recipe->tracking_nr))
            $recipe->tracking_nr = $this->db->table('recipes')->max('tracking_nr') + 1;

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
    public function destroy($id, Request $request)
    {
        if(!Input::has('lang')) return abort(500);

        $recipe = Recipe::where('tracking_nr', '=', $id)
            ->where('language', '=', Input::get('lang'))
            ->first();

        if(!Auth::check()) return abort(401);
        if($recipe->cookbook_rel->user_id != $request->user()->id) return abort(401);

        if($recipe->delete())
            return redirect()->route('recipes.index')
                ->with('status', 'Recept verwijderd.');
        else
            return abort(500);
    }

}
