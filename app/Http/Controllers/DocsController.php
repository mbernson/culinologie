<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Parsedown;

class DocsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $content = Storage::get('docs/index.md');
        return view('docs.show')
            ->withContent(Parsedown::instance()->text($content))
            ->withTitle('Help');
    }

    public function show($path)
    {
        $parts = explode('/', $path);
        $trail = $parts;
        array_unshift($parts, 'docs');
        $md_path = join('/', $parts).'.md';

        if (!Storage::exists($md_path)) {
            abort(404);
        }

        $content = Storage::get($md_path);
        return view('docs.show')
            ->withContent(Parsedown::instance()->text($content))
            ->withTrail($trail)
            ->withTitle('Help');
    }
}
