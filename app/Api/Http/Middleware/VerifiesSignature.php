<?php
namespace App\Api\Http\Middleware;

use App\Api\Http\Helpers\ApiResponseHelper;
use App\Models\Source;
use App\Services\AuthProvider\Signature;
use App\Services\AuthProvider\SourceUrlHandler;
use Closure;
use Illuminate\Http\Request;

class VerifiesSignature
{
    use ApiResponseHelper;

    public function handle(Request $request, Closure $next)
    {
        if (! $request->headers->has('X-Lurn-Signature')) {
            return $this->message('Signature was not found')
                ->statusBadRequest()
                ->respondWithError();
        }

        if (! $request->filled('token')) {
            return $this->message('Token was not found')
                ->statusBadRequest()
                ->respondWithError();
        }

        $urlHandler = app()->make(SourceUrlHandler::class);
        /** @type Signature $signature */
        $signature  = new Signature($urlHandler->urlSafeDecode($request->headers->get('X-Lurn-Signature')));
        $source     = Source::whereUrl($request->headers->get('referer'))->first();

        if (! $signature->verify($source, $urlHandler->urlSafeDecode($request->get('token')))) {
            return $this->message('Invalid signature')
                ->statusForbidden()
                ->respondWithError();
        }

        return $next($request);
    }
}
