<?php
namespace App\Providers;

use App\Services\Sendlane\Sendlane;
use Aws\CloudWatchLogs\CloudWatchLogsClient;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;

class SendlaneServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(Sendlane::class, function ($app, $args) {
            $monolog = new MonologLogger('sendlane');
            $monolog->pushHandler($this->getLocalLogHandler());
            if (App::environment('production', 'staging')) {
                $monolog->pushHandler($this->getCloudwatchHandler());
            }

            $stack = HandlerStack::create();
            $stack->push(
                Middleware::log(
                    $monolog,
                    new MessageFormatter(MessageFormatter::DEBUG)
                )
            );

            $client = new Client([
                'headers' => ['User-Agent' => 'Lurn/1.0'],
                'handler' => $stack
            ]);

            return new Sendlane($client, $args);
        });
    }

    private function getLocalLogHandler()
    {
        Storage::disk('local')->makeDirectory('storage/logs/sendlane', 0775, true);

        return new StreamHandler(storage_path('logs/sendlane/requests.log'));
    }

    private function getCloudwatchHandler()
    {
        $config     = config('cloudwatch');
        $client     = new CloudWatchLogsClient($config);
        $groupName  = 'lurn-central';
        $streamName = 'sendlane.' . App::environment();
        $retentionDays = 30;

        return new CloudWatch(
            $client,
            $groupName,
            $streamName,
            $retentionDays,
            10000,
            ['scope' => 'somescope']
        );
    }
}
