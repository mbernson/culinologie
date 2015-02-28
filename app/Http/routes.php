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

Route::group(['prefix' => 'cookbooks/{slug}'], function() {
    Route::resource('recipes', 'RecipesController', ['only' =>
        ['index', 'show']]);
});

Route::group(['prefix' => 'cookbooks/{slug}', 'middleware' => 'auth'], function() {
    Route::resource('recipes', 'RecipesController', ['only' =>
        ['create', 'edit', 'store', 'update', 'destroy']]);
});

Route::group(['middleware' => 'auth'], function() {
    Route::resource('recipes', 'RecipesController', ['only' =>
        ['create', 'edit', 'store', 'update', 'destroy']]);

    Route::resource('cookbooks', 'CookbooksController', ['only' =>
        ['create', 'edit', 'store', 'update', 'destroy']]);
});

Route::resource('recipes', 'RecipesController', ['only' => ['index', 'show']]);

Route::resource('cookbooks', 'CookbooksController', ['only' => ['index', 'show']]);

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
