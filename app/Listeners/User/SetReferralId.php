<?php

namespace App\Listeners\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class SetReferralId
{
    /**
     * Handle the event.
     *
     * @param User $user
     */
    public function handle(User $user)
    {
        // If it doesn't already have a referral set.
        if (! $user->referral) {
            // Select the referral from the on-boarding Refer a Friend scenario, if it exists.
            $referral = DB::table('scenario_user')
                ->where('details', $user->email)
                ->where('scenario_id', 5)
                ->first();

            $referral and $user->referral_id = $referral->user_id;
        }
    }
}
