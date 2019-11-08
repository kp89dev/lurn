<?php

namespace Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HomeTest extends \TestCase
{
    /**
     * @test
     */
    public function asset_home_available()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200)
        ->assertSee('For Entrepreneurs');
    }

    /**
     * @test
     */
    public function asset_education_available()
    {
        $response = $this->get(route('education'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function asset_contact_available()
    {
        $response = $this->get(route('contact'));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function asset_about_available()
    {
        $response = $this->get(route('about'));
        $response->assertStatus(200)
        ->assertSee('Transformational Home for Entrepreneurs');
    }
}
