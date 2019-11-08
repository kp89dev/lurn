<?php
namespace App\Listeners\CPA;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Auth\Events\Registered;

class TrackConversion
{
    public function handle($event)
    {
        // Note: Using request()->header() because request()->cookie()
        // was returning null every time. Who knows why.
        
        if (strpos(request()->header('cookie'), 'cpa_referral_code') !== FALSE) {
            preg_match('/cpa_referral_code=([^;]+)/', request()->header('cookie'), $matches);

            $cookie = explode('|', urldecode($matches[1]));
            $name   = explode(' ', $event->user->name);
            $client = new Client(['base_uri' => env('CPANETWORK_API_URL')]);

            try {
                $client->request('POST', '/api/v1/conversions', [
                    'headers' => [
                        'Cache-Control' => 'no-cache',
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'token'       => env('CPANETWORK_API_KEY'),
                        'first_name'  => $name[0],
                        'last_name'   => end($name),
                        'email'       => $event->user->email,
                        'ip_address'  => request()->ip(),
                        'external_id' => $event->user->id,
                        'code'        => $cookie[0],
                        'slug'        => $cookie[1]
                    ]
                ]);

                \Cookie::queue(\Cookie::forget('cpa_referral_code'));
            } catch (GuzzleException $e) {

            }
        }
    }
}
