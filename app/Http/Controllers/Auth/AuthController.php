<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Validator;
use App\Models\Cookbook, App\User;

class AuthController extends Controller
{

    private $redirectTo = '/';

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
     */

    use AuthenticatesAndRegistersUsers {
        getRegister as laravelGetRegister;
        postRegister as laravelPostRegister;
    }

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    public function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $title = "{$data['name']} kookboek";
        Cookbook::create([
            'title' => $title,
            'slug' => Str::slug($title),
            'user_id' => $user->id,
        ]);

        return $user;
    }

    public function getRegister()
    {
        if (env('SIGNUP_ENABLED') == true) {
            return $this->laravelGetRegister();
        } else {
            return view('auth.register_disabled');
        }
    }

    public function postRegister(Request $request)
    {
        if (env('SIGNUP_ENABLED') == true) {
            return $this->laravelPostRegister($request);
        } else {
            abort(404);
        }
    }
}
