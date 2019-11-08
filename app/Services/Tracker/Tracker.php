<?php
namespace App\Services\Tracker;

use App\Http\Controllers\Tracker\IndexController;
use App\Services\Contracts\TrackerInterface;

class Tracker extends IndexController implements TrackerInterface
{
    public function track($eventName, $args, $push)
    {
        $requestParameters = ['event' => $eventName, 'visitor' => $this->generateRandomString()];

        foreach ($args as $key => $arg) {
            $requestParameters['ce_' . $key] = $arg;
        }

        request()->merge($requestParameters);
        app(IndexController::class)->index(request());
    }

    public function generateRandomString($length = 12) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public function identity($identity)
    {
        return true;
    }
}

