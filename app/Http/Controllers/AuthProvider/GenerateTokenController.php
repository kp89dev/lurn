<?php
namespace App\Http\Controllers\AuthProvider;

use App\Http\Controllers\Controller;
use App\Models\Source;
use App\Services\AuthProvider\Signature;
use App\Services\AuthProvider\SourceUrlHandler;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Services\Logger\Cloudwatch as CloudwatchLogger;

class GenerateTokenController extends Controller
{
    public function index(Request $request, Guard $auth, SourceUrlHandler $sourceUrlHandler)
    {
        $source = $request->get('source') ?? $request->session()->get('source');
        $signature = $request->get('s') ?? $request->session()->get('signature');
        $randomToken = $request->get('t') ?? $request->session()->get('randomToken');
        $logger = app(CloudwatchLogger::class, ['streamName' => 'tool-authentication']);

        if (!$source || !$signature || !$randomToken) {
            $logger->info(
                'No source, signature or token found.',
                $request->toArray() +
                $request->headers->all() +
                ['ip' => $request->ip()]
            );

            return response()->view('errors.401', [], 401);
        }
        
        if (!$localSource = Source::whereUrl($source)->first()) {
            $logger->info(
                'Local source not found',
                $request->toArray() +
                $request->headers->all() +
                ['ip' => $request->ip()]
            );

            return response()->view('errors.401', [], 401);
        }

        $receivedSignature = new Signature($sourceUrlHandler->urlSafeDecode($signature));

        if (!$receivedSignature->verify($localSource, $sourceUrlHandler->urlSafeDecode($randomToken))) {
            $logger->info(
                'Signature verification failed',
                $request->toArray() +
                $request->headers->all() +
                ['ip' => $request->ip()]
            );
            return response()->view('errors.401', [], 401);
        }

        if (!$auth->check()) {
            $request->session()->put('source', $source);
            $request->session()->put('signature', $signature);
            $request->session()->put('randomToken', $randomToken);
            $request->session()->put('url.intended', route('idp.login'));

            $logger->info(
                'User redirected to login',
                $request->toArray() +
                $request->headers->all() +
                ['ip' => $request->ip()]
            );

            return redirect()->route('login');
        }

        if (Gate::allows($localSource->access_word)) {
            return redirect()->away($sourceUrlHandler->getLoginUrl($auth->user(), $localSource));
        }

        $logger->info(
            'User not allowed to access tool',
            $request->toArray() +
            $request->headers->all() +
            ['ip' => $request->ip()]
        );
        return response()->view('errors.401', [], 401);
    }
}
