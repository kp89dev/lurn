<?php
namespace Feature\Home;

use Carbon\Carbon;
use TestCase;

class outreachPageTest extends TestCase
{

    /**
     * @test
     */
    public function outreach_page_is_available()
    {
        $this->get(route('outreach'))
            ->assertStatus(200);
    }
}
