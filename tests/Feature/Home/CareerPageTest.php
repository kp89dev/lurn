<?php
namespace Feature\Home;

use Carbon\Carbon;
use TestCase;

class careerPageTest extends TestCase
{

    /**
     * @test
     */
    public function career_page_is_available()
    {
        $this->get(route('career'))
            ->assertStatus(200);
    }
}
