<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccountMergeToken extends Model
{
    protected $guarded = ['updated_at'];

    /**
     * @param User  $user
     * @param Model $anotherAccount
     *
     * @return string
     */
    public function getNewToken(User $user, Model $anotherAccount)
    {
        $this->deleteExisting($anotherAccount);

        $token = $this->createNewToken();

        $this->fill([
            'email_owner'    => $user->email,
            'email_to_merge' => $anotherAccount->email,
            'token'          => password_hash($token, PASSWORD_BCRYPT)
        ])->save();

        return $token;
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }

    /**
     * @param $token
     *
     * @return bool
     */
    public function valid($token)
    {
        return ! $this->tokenExpired($this->created_at) &&
                password_verify($token, $this->token);
    }

    /**
     * @param $user
     *
     * @return mixed
     */
    protected function deleteExisting($user)
    {
        return $this->where('email_to_merge', $user->email)->delete();
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds(60)->isPast();
    }
}
