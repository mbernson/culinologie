<?php

namespace App\Http\Controllers;

use App\Models\Cookbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CookbooksController extends Controller
{
    private static int $per_page = 20;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $cookbooks = Cookbook::with('owner')
            ->orderBy('title')
            ->orderBy('id')
            ->paginate(self::$per_page);

        return view('cookbooks.index')
            ->withCookbooks($cookbooks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $title = $request->get('title');

        $cookbook = new Cookbook([
            'title' => $title,
            'slug' => Str::slug($title),
        ]);
        $cookbook->user_id = Auth::user()->id;

        $this->validate($request, [
            'title' => 'required|max:255',
        ]);

        if ($cookbook->save()) {
            return redirect()->route('cookbooks.index')->with('status', 'Kookboek aangemaakt.');
        } else {
            abort(500);
        }
    }
}
