<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class ReferralTest extends \LoggedInTestCase
{
    /**
     * @test
     */
    public function check_correct_referral_code()
    {
        $code = user()->getReferralCode();
        $response = $this->get(route('referral.index', ['referral' => $code]));
        $response->assertCookie('referral')
            ->assertStatus(302);
    }

    /**
     * @test
     */
    public function check_incorrect_referral_code()
    {
        $code = user()->getReferralCode();
        $response = $this->get(route('referral.index', ['referral' => $code]));
        $response->assertStatus(302);
    }
}
