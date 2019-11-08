<?php

namespace App\Listeners\Auth;

use App\Models\User;
use App\Services\Tracker\Contracts\LocationReader;
use Illuminate\Auth\Events\Failed;

class IncrementFailedLogins
{
    public function handle(Failed $event)
    {
        if (! $event->user instanceof User) {
            return true;
        }

        $locationReader = app()->make(LocationReader::class);
        $event->user->logins()->create([
            'ip'          => request()->ip(),
            'user_agent'  => getenv('HTTP_USER_AGENT'),
            'successful'  => false,
            'city'        => $locationReader->city->name,
            'country'     => $locationReader->country->name,
            'countryCode' => $locationReader->country->isoCode,
            'region'      => @$locationReader->subdivisions[0]->isoCode,
            "regionName"  => @$locationReader->subdivisions[0]->name,
            'timezone'    => $locationReader->location->timeZone,
            'zip'         => $locationReader->postal->code
        ]);
    }
}
