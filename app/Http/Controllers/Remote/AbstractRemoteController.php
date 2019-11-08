<?php

namespace App\Http\Controllers\Remote;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Logger\Cloudwatch as CloudwatchLogger;

class AbstractRemoteController extends Controller
{
    protected $logger;
    protected $expectedFields = [];

    public function __construct(Request $request)
    {
        $this->logger = app(CloudwatchLogger::class, ['streamName' => 'infusionsoft']);

        $this->middleware(function ($request, $next) {
            if (! $this->isValid($request)) {
                return response('', 204);
            }

            return $next($request);
        });
    }

    private function isValid(Request $request)
    {
        if (empty($request->token) || $request->token != env('IS_REQ_TOKEN')) {
            $this->logger->info('Infusionsoft Request Failed Auth', $request->all());

            return false;
        }

        foreach ($this->expectedFields as $expectedField) {
            if (empty($request->$expectedField)) {
                $this->logger->info("Infusionsoft Request Failed - Missing field - $expectedField", $request->all());

                return false;
            }
        }

        return true;
    }
}
