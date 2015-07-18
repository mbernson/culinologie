<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Models\Cookbook, App\User;

class AuthController extends Controller
{

    protected $redirectPath = '/';

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
            'password' => 'required|confirmed|min:8',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param Request $request
     * @return User
     */
    public function create(Request $request)
    {
        $user = User::create($request->only('name', 'email', 'password'));

        $title = "{$user->name} kookboek";
        Cookbook::create([
            'title' => $title,
            'slug' => str_slug($title),
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postRegister(Request $request)
    {
        if (env('SIGNUP_ENABLED') != true) {
            return abort(404);
        }

        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        Auth::login($this->create($request));

        return redirect()->to('/')
            ->with('status', 'Bedankt voor je aanmelding! Let op: pas als je account is goedgekeurd kun je recepten aanmaken.');
    }
}
