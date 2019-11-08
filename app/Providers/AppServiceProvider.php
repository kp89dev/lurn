<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use Rollbar\Laravel\RollbarServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapThree();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('production')) {
            $this->app->register(RollbarServiceProvider::class);
        }

        if (env('LOG_MYSQL_QUERIES', 0) == 1) {
            DB::listen(function($executedQuery) {
                $q = str_replace('?', '\'%s\'', $executedQuery->sql);
                Log::debug("Query Debug: ", [
                    vsprintf($q, $executedQuery->bindings),
                    $executedQuery->time
                ]);
            });
        }

        if ($this->app->environment('local')) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
