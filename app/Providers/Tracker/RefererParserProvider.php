<?php
namespace App\Providers\Tracker;

use App\Services\Tracker\Contracts\RefererParserInterface;
use Illuminate\Support\ServiceProvider;
use Snowplow\RefererParser\Parser;

class RefererParserProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(RefererParserInterface::class, function ($app, $args) {
            $parser = new Parser();
            $referer = @$parser->parse(
                $app->request->referer,
                $app->request->ce_uri
            );
            
            return $referer;
        });
    }
}
