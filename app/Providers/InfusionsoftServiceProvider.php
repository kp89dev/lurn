<?php
namespace App\Providers;

use App\Models\InfusionsoftToken;
use App\Services\Logger\Cloudwatch as CloudwatchLogger;
use Illuminate\Support\ServiceProvider;
use Infusionsoft\Infusionsoft;
use Infusionsoft\Token;

class InfusionsoftServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(Infusionsoft::class, function ($app, $args) {
            $tokenDetails = InfusionsoftToken::whereAccount($args['account'])->first();
            if (! $tokenDetails) {
                throw new \InvalidArgumentException("Account was not defined or db doesn't contain this account");
            }
    
            $is = new Infusionsoft([
                'clientId'     => env('IS_CLIENT_ID'),
                'clientSecret' => env('IS_CLIENT_SECRET'),
                'debug'        => true,
                'redirectUri'  => 'http://local'
            ]);

            $is->setToken(new Token([
                'access_token'  => $tokenDetails->access_token,
                'refresh_token' => $tokenDetails->refresh_token,
                'expires_in'    => (int) $tokenDetails->end_of_life - time()
            ]));

            $logger = $app->makeWith(CloudwatchLogger::class, ['streamName' => 'infusionsoft']);

            $is->setHttpLogAdapter($logger);

            return $is;
        });
    }
}

