<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/', 'RecipesController@index')->name('home.index');

Route::group(['prefix' => 'cookbooks/{slug}', 'as' => 'cookbooks.', 'middleware' => 'auth'], function () {
    Route::resource('recipes', 'RecipesController');
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
Route::get('/help', 'DocsController@index')->name('help.index');
Route::get('/help/{path?}', 'DocsController@show')->where('path', '.+')->name('help.show');

Route::get('/logout', 'Auth\LoginController@logout');
