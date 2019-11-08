<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Arcanedev\SeoHelper\Entities\Title;

class ComposerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(
            'widgets.featured-courses', 'App\Http\ViewComposers\FeaturedCourseComposer'
        );

        View::composer(
            'parts.onboarding-popup', 'App\Http\ViewComposers\FreeCourseComposer'
        );
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
