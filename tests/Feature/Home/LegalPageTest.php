<?php
namespace Feature\Home;

use Carbon\Carbon;
use TestCase;

class legalPageTest extends TestCase
{

    /**
     * @test
     */
    public function privacy_page_is_available()
    {
        $this->get(route('privacy'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function terms_page_is_available()
    {
        $this->get(route('terms'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function dmca_page_is_available()
    {
        $this->get(route('dmca'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function refund_page_is_available()
    {
        $this->get(route('refund'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function antispam_page_is_available()
    {
        $this->get(route('anti-spam'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function sms_privacy_page_is_available()
    {
        $this->get(route('sms-privacy'))
            ->assertStatus(200);
    }
}
