<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\VanillaJsConnect\VanillaJsConnect;

class VanillaJsConnectProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(VanillaJsConnect::class, function () {
            return new VanillaJsConnect();
        });
    }
}
