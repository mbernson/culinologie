<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;

class UsersController extends Controller
{

    public final const PER_PAGE = 25;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')
            ->paginate();

        return view('users.index')->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        if($request->get('password') !== $request->get('password_confirmation')) {
            return redirect()->back()->withInput()
                ->with('warning', 'Wachtwoorden komen niet overeen.');
        }

        $user = User::create($request->only(['name', 'email', 'password']));

        return redirect()->route('users.index')
            ->with('status', 'Gebruiker aangemaakt.');
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Grant the user login and access to the application.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function approve($id) {
        $approved = DB::table('users')
            ->where('id', $id)
            ->update(['approved' => 1]);

        if($approved)
            return redirect()->route('users.index')
                ->with('status', 'Gebruiker goedgekeurd!');
        else
            return abort(500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->getKey() == Auth::user()->getKey()) {
            return redirect()->back()
                ->with('warning', 'Je kunt jezelf niet verwijderen.');
        }

        try {
            $user->delete();
            return redirect()->route('users.index')
                ->with('status', 'Gebruiker verwijderd.');
        } catch(\Exception $e) {
            return redirect()->route('users.index')
                ->with('warning', vsprintf('Gebruiker kon niet worden verwijderd. Bestaan er nog recepten die ernaar verwijzen? (%s)', [$e->getMessage()]));
        }
    }
}
