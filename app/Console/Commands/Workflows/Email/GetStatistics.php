<?php

namespace App\Console\Commands\Workflows\Email;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use Aws\Exception\AwsException;
use App\Models\EmailStatus;
use Aws\Sqs\SqsClient;
use Carbon\Carbon;

class GetStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflows:get-email-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gathers the SNS statstics for emails from SQS.';

    protected $client;

    public function __construct()
    {
        parent::__construct();

        $this->client = new SqsClient([
            'version'     => 'latest',
            'region'      => config('services.sqs.region'),
            'credentials' => [
                'key'    => config('services.sqs.key'),
                'secret' => config('services.sqs.secret'),
            ],
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        while (count($messages = $this->getLatestMessages())) {
            foreach ($messages as $message) {
                $timestamp = Carbon::createFromFormat('Y-m-d\TH:m:i.u\Z', $message->data->timestamp);
                $emailLog = EmailStatus::whereAwsId($message->mail->messageId)
                    ->where('last_timestamp', '<', $timestamp)
                    ->first();

                if (! $emailLog) {
                    continue;
                }

                $emailLog->update([
                    'status'         => $message->notificationType,
                    'last_timestamp' => $timestamp,
                ]);
            }
        }
    }

    protected function getLatestMessages()
    {
        try {
            $response = $this->client->receiveMessage([
                'QueueUrl'              => config('services.sqs.url'),
                'MaxNumberOfMessages'   => 10,
                'MessageAttributeNames' => ['All'],
                'WaitTimeSeconds'       => 3,
            ]);

            return $this->processMessages($response->get('Messages') ?: []);
        } catch (AwsException $e) {
            Log::info('Call to SQS failed in GetStatistics. Reported Error: ' . $e->getMessage());
        }

        return [];
    }

    protected function processMessages(array $messages)
    {
        $processed = [];

        foreach ($messages as $message) {
            $message = json_decode($message['Body']);

            if ($message->Message) {
                $message = json_decode($message->Message);
            }

            $status = strtolower($message->notificationType);

            if (isset($message->$status)) {
                $message->data = $message->$status;
                $processed[] = $message;
            }
        }

        return $processed;
    }
}
