<?php
namespace Tests\Feature;

class BusinessBuilderTest extends \LoggedInTestCase
{

    /**
     * @test
     */
    public function bb_page_returns_200()
    {
        $this->get(route('business-builder'))
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function bb_pa_page_returns_200()
    {
        $this->get(route('business-builder-publish-academy'))
            ->assertStatus(200);
    }
}
