<?php
namespace App\Api\Http\Controllers;

use App\Api\Http\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\SourceEmail;
use App\Models\SourceToken;
use App\Services\AuthProvider\SourceUrlHandler;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseHelper;

    public function user(Request $request, SourceUrlHandler $sourceUrlHandler)
    {
        $token = trim($sourceUrlHandler->urlSafeDecode($request->get('token')));
        $sourceToken = SourceToken::whereToken(substr($token,0,50))->first();

        if (! $sourceToken instanceof SourceToken) {
            return $this->message('Invalid token provided.')
                ->statusForbidden()
                ->respondWithError();
        }

        if ($sourceToken->used !== 0) {
            return $this->message('Token already used')
                ->statusForbidden()
                ->respondWithError();
        }

        if (Carbon::now()->gt($sourceToken->created_at->addMinute())) {
            return $this->message('Token expired')
                ->statusForbidden()
                ->respondWithError();
        }

        $sourceToken->used = 1;
        $sourceToken->save();

        $sourceEmail = (new SourceEmail())
                        ->where('user_id', $sourceToken->user_id)
                        ->where('source_id', $sourceToken->source_id)
                        ->first();

        if (! $sourceEmail) {
            $sourceEmail = (new SourceEmail())->create([
                'user_id'   => $sourceToken->user_id,
                'source_id' => $sourceToken->source_id,
                'email'     => $sourceToken->user->email
            ]);
        }

        return $this->data([
            'id' => $sourceEmail->user_id,
            'name' => $sourceEmail->user->name,
            'email' => $sourceEmail->email
        ])->respond();
    }
}
