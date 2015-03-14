<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('recipes.index', 'App\Composers\RecipesComposer@compose');
        view()->composer('recipes.index', 'App\Composers\RecipesComposer@allCookbooks');

        view()->composer('recipes.create', 'App\Composers\RecipesComposer@compose');
        view()->composer('recipes.create', 'App\Composers\RecipesComposer@categories');
        view()->composer('recipes.create', 'App\Composers\RecipesComposer@userCookbooks');

        view()->composer('recipes.show', 'App\Composers\RecipesComposer@compose');
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
