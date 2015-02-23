<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
            view()->composer('recipes.index', 'App\Composers\RecipesComposer@compose');
            view()->composer('recipes.seasons', 'App\Composers\RecipesComposer@seasons');

            view()->composer('recipes.create', 'App\Composers\RecipesComposer@compose');
            view()->composer('recipes.create', 'App\Composers\RecipesComposer@categories');
            view()->composer('recipes.create', 'App\Composers\RecipesComposer@temperatures');
            view()->composer('recipes.create', 'App\Composers\RecipesComposer@seasons');
            view()->composer('recipes.create', 'App\Composers\RecipesComposer@cookbooks');

            view()->composer('recipes.show', 'App\Composers\RecipesComposer@categories');
            view()->composer('recipes.show', 'App\Composers\RecipesComposer@temperatures');
            view()->composer('recipes.show', 'App\Composers\RecipesComposer@seasons');
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
