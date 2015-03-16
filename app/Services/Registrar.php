<?php namespace App\Services;

use App\User;
use App\Models\Cookbook;
use Validator;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;
use Illuminate\Support\Str;

class Registrar implements RegistrarContract
{

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
}
