<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Parsedown;

class DocsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $content = Storage::get('docs/index.md');
        return view('docs.show', [
            'title' => 'Help',
            'content' => Parsedown::instance()->text($content),
        ]);
    }

    public function show($path)
    {
        $parts = explode('/', (string) $path);
        $trail = $parts;
        array_unshift($parts, 'docs');
        $md_path = join('/', $parts) . '.md';

        if (!Storage::exists($md_path)) {
            abort(404);
        }

        $content = Storage::get($md_path);
        return view('docs.show', [
            'title' => 'Help',
            'trail' => $trail,
            'content' => Parsedown::instance()->text($content),
        ]);
    }
}
