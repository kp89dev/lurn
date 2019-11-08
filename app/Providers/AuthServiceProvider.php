<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('access-niche-detective','App\Policies\ToolPolicies@nicheDetective');
        Gate::define('access-launchpad','App\Policies\ToolPolicies@launchpad');
        Gate::define('access-dla-creator','App\Policies\ToolPolicies@dlaCreator');
        Gate::define('access-business-builder','App\Policies\ToolPolicies@businessBuilder');
        Gate::define('access-business-builder-pa','App\Policies\ToolPolicies@businessBuilderPA');
        Gate::define('access-business-builder-dpe','App\Policies\ToolPolicies@businessBuilderDpe');
    }
}
