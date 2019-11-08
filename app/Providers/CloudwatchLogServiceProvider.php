<?php
namespace App\Providers;

use App\Services\Logger\Cloudwatch as CloudwatchLogger;
use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Illuminate\Support\ServiceProvider;
use Maxbanton\Cwh\Handler\CloudWatch;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\WebProcessor;

class CloudwatchLogServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function register()
    {
        $this->app->bind(CloudwatchLogger::class, function ($app, $args) {
            if (env('APP_ENV') === 'testing') {
                $log = new Logger('log');
                $log->pushHandler(new StreamHandler(storage_path('laravel.log'), Logger::ERROR));

                return $log;
            }

            $config = config('cloudwatch');

            $client = new CloudWatchLogsClient($config);
            $groupName = 'lurn-central.' . env('APP_ENV');
            $streamName = $args['streamName'] ?? 'infusionsoft';
            $retentionDays = 30;

            $clodwatchHandler = new CloudWatch(
                $client,
                $groupName,
                $streamName,
                $retentionDays,
                10000,
                ['scope' => 'lurn-central.' . env('APP_ENV')]
            );
            $clodwatchHandler->setFormatter(new LineFormatter("%level_name%: %message% %context% %extra%\n", null, true));

            $extraDataProcessor = new WebProcessor();

            return new Logger('Cloudwatch', [$clodwatchHandler], [
                $extraDataProcessor,
                function (array $record) {
                    $record['message'] = preg_replace_callback(
                        '/<member><name>CardNumber<\/name><value><string>(.*)<\/string><\/value><\/member>/iU',
                        function ($matches) {
                            return '**** ' . substr($matches[1], -4);
                        },
                        $record['message']
                    );

                    if (user()) {
                        $record['extra']['user_id'] = user()->id;
                        $record['extra']['email']   = user()->email;
                    }

                    return $record;
                }
            ]);
        });
    }
}
