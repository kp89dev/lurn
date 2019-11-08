<?php
namespace App\Providers\Tracker;

use DeviceDetector\DeviceDetector;
use Illuminate\Support\ServiceProvider;
class DeviceDetectorProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(DeviceDetector::class, function ($app, $args) {
            return new DeviceDetector($app->request->header('User-Agent'));
        });
    }
}
