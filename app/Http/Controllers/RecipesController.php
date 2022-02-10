<?php

namespace App\Http\Controllers;

use App\Helper\RecipeSearch;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Requests\SaveRecipeRequest;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB, Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Image;

class RecipesController extends Controller
{
    private static int $per_page = 25;
    private static string $default_language = 'nl';

    public function __construct(private readonly DatabaseManager $db)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request, $cookbook = '*')
    {
        $languages = $request->get('lang', ['nl', 'uk']);
        $search = new RecipeSearch();
        $search->setCookbook($cookbook);

        $recipes = $search->buildQuery($request)
            ->whereIn('language', $languages)
            ->orderBy('created_at', 'desc');
        $count = $recipes->count();
        $url_params = array_merge($search->getParams(), [
            'lang[]' => $languages,
        ]);
        $recipes = $recipes->paginate(static::$per_page)
            ->appends($url_params);

        Session::flash('return_url', route('recipes.index', $search->getParams()));

        $available_languages = $search->buildQuery($request)
            ->select('language')->distinct()
            ->orderBy('language', 'desc')
            ->get()->pluck('language')->all();

        $allCategories = Category::all()->pluck('name')->toArray();

        return view('recipes.index', [
            'recipes' => $recipes,
            'count' => $count,
            'chosen_languages' => $languages,
            'available_languages' => $available_languages,
            'categories' => $allCategories,
            'hide_cookbooks' => $search->shouldHideCookbooks(),
            'params' => $search->getParams()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('recipes.create')->withRecipe(new Recipe());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request, $cookbook = null)
    {
        $recipe = new Recipe();
        $recipe->user_id = Auth::user()->id;

        if ($cookbook != null) {
            $recipe->cookbook = $cookbook;
        }

        return $this->saveRecipe($request, $recipe);
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Request $request, int $id)
    {
        $recipes = Recipe::where('tracking_nr', '=', $id)
            ->orderBy('language', 'asc')->get();

        $language = $request->get('lang', null);
        $recipe = false;
        foreach ($recipes as $r) {
            if ($r->language == $language) {
                $recipe = $r;
            } elseif ($language == null) {
                $recipe = $r;
                break;
            }
        }

        if (!$recipe) {
            abort(404);
        }

        $ingredients = $recipe->ingredients->groupBy('header');

        return view('recipes.show')
            ->with('recipe', $recipe)
            ->with('recipes', $recipes)
            ->with('cookbook', $recipe->cookbookRel)
            ->with('ingredients', $ingredients);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Request $request, int $id)
    {
        $lang = $request->get('lang', static::$default_language);
        $recipe = Recipe::where('tracking_nr', '=', $id)
            ->where('language', '=', $lang)
            ->first();

        if (!$recipe) {
            abort(404);
        }

        return view('recipes.create')
            ->with('recipe', $recipe);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(SaveRecipeRequest $request, int $tracking_nr)
    {
        $lang = $request->get('lang');
        $recipe = Recipe::where('tracking_nr', '=', $tracking_nr)
            ->where('language', '=', $lang)
            ->first();

        if (!$recipe) {
            abort(404);
        }

        $this->db->table('ingredients')
            ->where('recipe_id', '=', $recipe->id)->delete();

        return $this->saveRecipe($request, $recipe);
    }

    private function saveRecipe(SaveRecipeRequest $request, Recipe $recipe)
    {
        $input = $request->only(
            'title',
            'people',
            'presentation',
            'year',
            'season',
            'cookbook',
            'temperature',
            'visibility',
            'tracking_nr'
        );

        $recipe->fill($input);

        if (empty($recipe->tracking_nr)) {
            $recipe->tracking_nr = $this->db->table('recipes')->max('tracking_nr') + 1;
        }

        if ($request->has('lang')) {
            $recipe->language = $request->get('lang');
        }

        if ($request->has('directions')) {
            $recipe->description = $request->get('directions');
        }

        if ($request->has('categories')) {
            $recipe->categories()->sync($request->get('categories'));
        }

        // Override the category if the user provided one.
        if (!empty($request->get('category_alt'))) {
            $newCategory = Category::create(['name' => $request->get('category_alt')]);
            $recipe->categories()->attach($newCategory->id);
        }

        try {
            $recipe_saved = $recipe->save();
        } catch (QueryException) {
            $recipe->tracking_nr = $this->db->table('recipes')->max('tracking_nr') + 1;
            $recipe_saved = $recipe->save();
            Session::flash('warning', 'Let op: je recept is onder een nieuw volgnummer bewaard, omdat het opgegeven nummer al in gebruik was.');
        }

        if ($request->hasFile('picture')) {
            $path = join(DIRECTORY_SEPARATOR, [
                public_path(),
                'uploads',
                'pictures'
            ]);
            $filename = $recipe->tracking_nr . '.jpg';
            $file = $request->file('picture')->move($path, $filename);
            $image = Image::make($file)->widen(480);
            $image->save($path . DIRECTORY_SEPARATOR . $filename);
        }

        $ingredients_saved = $recipe->saveIngredientsFromText($request->get('ingredients'));

        if ($recipe_saved && $ingredients_saved) {
            return redirect()->route('recipes.show', ['recipes' => $recipe->tracking_nr])->with('lang', $recipe->language);
        }

        abort(500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Request $request, int $id)
    {
        if (!$request->has('lang')) {
            return abort(500);
        }

        $recipe = Recipe::where('tracking_nr', '=', $id)
            ->where('language', '=', $request->get('lang'))
            ->first();

        if (!Auth::check()) {
            return abort(401);
        }
        if ($recipe->cookbookRel->user_id != $request->user()->id) {
            return abort(401);
        }

        if ($recipe->delete()) {
            return redirect()->route('recipes.index')
                ->with('status', 'Recept verwijderd.');
        }

        return abort(500);
    }

    public function fork(Request $request, $tracking_nr)
    {
        $lang = $request->get('lang', static::$default_language);
        $recipe = Recipe::where('tracking_nr', '=', $tracking_nr)
            ->where('language', '=', $lang)
            ->first();

        if (!$recipe) {
            abort(404);
        }

        $new_recipe = $recipe->replicate();
        // We can do this since the ingredients are converted to text anyways.
        $new_recipe->ingredients = $recipe->ingredients;

        return view('recipes.create')
            ->with('recipe', $new_recipe);
    }

    public function random(Request $request)
    {
        $language = $request->get('lang', static::$default_language);

        $recipe = Recipe::select('tracking_nr', 'language')
            ->where('language', $language)
            ->orderByRaw('RAND()')
            ->first();

        return redirect()->route('recipes.show', ['recipes' => $recipe->tracking_nr])
            ->with('lang', $recipe->language);
    }

    final public const DEFAULT_LIST = 'Loved';

    public function bookmark(Request $request, $tracking_nr)
    {
        $list = static::DEFAULT_LIST;
        $language = $request->get('language', static::$default_language);

        $recipe = Recipe::select('id')->where('tracking_nr', '=', $tracking_nr)
            ->where('language', '=', $language)
            ->first();

        if (!$recipe) {
            abort(500);
        }

        DB::table('recipe_bookmarks')->insert([
            'user_id' => Auth::user()->id,
            'list' => $list,
            'recipe_id' => $recipe['id']
        ]);

        return redirect()->route('recipes.show', ['recipes' => $tracking_nr])
            ->with('lang', $language);
    }

    public function unbookmark(Request $request, $tracking_nr)
    {
        $list = static::DEFAULT_LIST;
        $language = $request->get('language', static::$default_language);

        $recipe = Recipe::select('id')->where('tracking_nr', '=', $tracking_nr)
            ->where('language', '=', $language)
            ->first();

        if (!$recipe) {
            abort(500);
        }

        DB::table('recipe_bookmarks')
            ->where('recipe_id', $recipe['id'])
            ->where('user_id', Auth::user()->id)
            ->where('list', $list)
            ->delete();

        return redirect()->route('recipes.show', ['recipes' => $tracking_nr])
            ->with('lang', $language);
    }

    public function postComment(Request $request, $trackingnr)
    {
        $data = $request->only('title', 'rating', 'body');
        $comment = new Comment($data);
        $comment->user_id = Auth::user()->id;
        $comment->recipe_tracking_nr = $trackingnr;
        $comment->save();
        return Redirect::to('recipes/' . $trackingnr);
    }

    public function deleteComment(Request $request, $recipe_id, $comment_id)
    {
        $comment = Comment::findOrFail($comment_id);
        if ($comment->user_id == Auth::user()->id) {
            $comment->delete();
            Session::flash('status', 'Reactie verwijderd!');
            return 1;
        }

        Session::flash('warning', 'Kon reactie niet verwijderen');
        return 0;
    }
}
