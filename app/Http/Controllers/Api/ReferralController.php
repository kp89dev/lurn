<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ReferralController
{
    public function index(Request $request, $code)
    {

        if (! $request->hasCookie('referral') && $code) {
            // Search for the user by the referral code coming from the CPA network.
            $getHash = "md5(concat('$', id, '.', substring(convert(created_at, CHAR), 1, 10)))";
            $user = User::whereRaw("$getHash = ?", $code)->first();

            if ($user) {
                return redirect(url(''))->withCookie(
                    cookie('referral', $user->id, 86400 * 30)
                );
            }
        }

        return redirect(url(''));
    }
}