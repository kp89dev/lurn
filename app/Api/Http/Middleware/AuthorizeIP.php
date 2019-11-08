<?php
namespace App\Api\Http\Middleware;

use App\Api\Http\Helpers\ApiResponseHelper;
use App\Models\Source;
use Closure;
use Illuminate\Http\Request;

class AuthorizeIP
{
    use ApiResponseHelper;

    public function handle(Request $request, Closure $next)
    {
        if (!$request->headers->has('referer')) {
            return $this->message('Referer was not found')
                ->statusBadRequest()
                ->respondWithError();
        }

        $source = Source::whereUrl($request->headers->get('referer'))->first();
        if (! $source instanceof Source) {
            return $this->message('Requester is not allowed to access the data #1')
                ->statusForbidden()
                ->respondWithError();
        }

        if ($source->ip !== $request->ip()) {
            return $this->message('Requester is not allowed to access the data #2')
                ->statusForbidden()
                ->respondWithError();
        }

        return $next($request);
    }
}
