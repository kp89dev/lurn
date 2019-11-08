<?php
namespace App\Providers;

use App\Services\Contracts\TrackerInterface;
use App\Services\Tracker\Tracker;
use App\Services\Tracker\TrackerComposite;
use Illuminate\Support\ServiceProvider;

class TrackerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(TrackerInterface::class, function ($app, $args) {
            if (env('APP_ENV') === 'testing' || $app->session->has('admin_impersonator'))  {
                return $this->mockTracker();
            }

            $woopra       = $this->setupWoopra();
            $localTracker = $this->setupLocalTracker();

            return new TrackerComposite([$woopra, $localTracker]);
        });
    }

    private function mockTracker()
    {
        return new class() {
            public function push() {
                return true;
            }

            public function track() {
                return true;
            }
        };
    }

    private function setupWoopra()
    {
        $woopra = new \WoopraTracker([
            'domain' => 'lurn.com',
            'ping'   => false
        ]);

        if (user()) {
            $woopra->identify([
                'name' => user()->name,
                'email' => user()->email,
            ]);
        }

        return $woopra;
    }

    private function setupLocalTracker()
    {
        return new Tracker();
    }
}
