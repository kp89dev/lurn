<?php

namespace App\Listeners\Auth;

use App\Services\Tracker\Contracts\LocationReader;

class IncrementSuccessfulLogins
{
    public function handle($data)
    {
        $locationReader = app()->make(LocationReader::class);
        $data->user && $data->user->logins()->create([
            'ip'          => request()->ip(),
            'user_agent'  => getenv('HTTP_USER_AGENT'),
            'successful'  => true,
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
