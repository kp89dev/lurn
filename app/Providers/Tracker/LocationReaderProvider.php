<?php
namespace App\Providers\Tracker;

use App\Services\Tracker\Contracts\LocationReader;
use Exception;
use GeoIp2\Database\Reader;
use Illuminate\Support\ServiceProvider;

class LocationReaderProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(LocationReader::class, function ($app, $args) {
            $reader = new Reader(storage_path('GeoLite2-City.mmdb'));
            
            try {
                return $reader->city($app->request->ip());
            } catch (\Exception $e) {
                return $reader->city('72.229.28.185');
            }
        });
    }
}
