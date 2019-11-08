<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SupportMessageRequest;
use App\Mail\Support;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function message(SupportMessageRequest $request)
    {
        $email = new Support($request->user, $request->message);
        Mail::to(collect(['email' => config('support.email')]))->send($email);

        return response()->json(['success' => true]);
    }
}
