<?php
/**
 * Date: 3/20/18
 * Time: 10:30 AM
 */

namespace App\Providers;

use App\Engines\Survey\SurveyEngine;
use App\Models\Survey;
use Illuminate\Support\ServiceProvider;

class SurveysServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'SurveysLibrary',
            function () {
                return new SurveyEngine();
            }
        );
    }
}