<?php
namespace App\Console\Commands\Infusionsoft;

use App\Models\InfusionsoftToken;
use Illuminate\Console\Command;
use Infusionsoft\Infusionsoft;
use Infusionsoft\Token;

class RefreshTokenInfusionsoft extends Command
{

    protected $signature = 'refresh:tokens';
    protected $description = 'Tries to refresh infusionsoft tokens';

    /**
     * @todo Catch the exception and report it somewhere.
     * @throws \Infusionsoft\InfusionsoftException
     */
    public function handle()
    {
        $accounts = InfusionsoftToken::all();

        foreach ($accounts as $account) {
            /** @var $is \Infusionsoft\Infusionsoft */
            $is = app(Infusionsoft::class, ['account' => $account->account]);

            #if doesn't expire in the next 60 minutes
            if ($account->end_of_life-3600 > time()) {
                continue;
            }

            /** @type Token $isToken */
            $isToken = $is->refreshAccessToken();

            $account->access_token = $isToken->accessToken;
            $account->refresh_token = $isToken->refreshToken;
            $account->end_of_life  = $isToken->endOfLife;
            $account->save();
        }
    }
}

