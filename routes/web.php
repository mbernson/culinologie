<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'RecipesController@index');
Route::get('/recipes', 'RecipesController@index');

Route::group(['prefix' => 'cookbooks/{slug}'], function () {
    Route::resource('recipes', 'RecipesController', ['only' =>
        ['index', 'show']]);
});

Route::group(['prefix' => 'cookbooks/{slug}', 'middleware' => 'auth'], function () {
    Route::resource('recipes', 'RecipesController', ['only' =>
        ['create', 'edit', 'store', 'update', 'destroy']]);
    Route::get('recipes/{recipes}/fork', 'RecipesController@fork');
    Route::post('recipes/{recipes}/bookmark', 'RecipesController@bookmark');
    Route::post('recipes/{recipes}/unbookmark', 'RecipesController@unbookmark');
});

Route::group(['middleware' => 'auth'], function () {
    Route::resource('recipes', 'RecipesController', ['only' =>
        ['create', 'edit', 'store', 'update', 'destroy']]);

    Route::get('recipes/{recipes}/fork', 'RecipesController@fork');
    Route::post('recipes/{recipes}/bookmark', 'RecipesController@bookmark');
    Route::post('recipes/{recipes}/unbookmark', 'RecipesController@unbookmark');

    Route::resource('cookbooks', 'CookbooksController', ['only' =>
        ['create', 'edit', 'store', 'update', 'destroy']]);
    Route::post('recipes/{recipe}/postComment', ['as'=>'recipes.postComment', 'uses'=>'RecipesController@postComment']);
    Route::delete('recipes/{recipe}/deleteComment/{comment_id}', ['as'=>'recipes.deleteComment', 'uses'=>'RecipesController@deleteComment']);
});

Route::group(['middleware' => 'admin'], function () {
    Route::resource('users', 'UsersController', ['only' =>
        ['index', 'store', 'destroy']]);
    Route::post('users/{users}/approve', 'UsersController@approve');
});

Route::get('recipes/random', 'RecipesController@random');
Route::resource('recipes', 'RecipesController', ['only' => ['index', 'show']]);

Route::resource('cookbooks', 'CookbooksController', ['only' => ['index', 'show']]);

// Help/docs
Route::get('/help', 'DocsController@index');
Route::get('/help/{path?}', 'DocsController@show')->where('path', '.+');

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
Route::get('/logout', 'Auth\LoginController@logout');