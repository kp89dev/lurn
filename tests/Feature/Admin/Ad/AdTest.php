<?php

namespace Tests\Feature\Admin\Banner;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Models\Ad;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdTest extends \SuperAdminLoggedInTestCase
{   
    /**
     * @test
     */
    public function ad_page_is_available()
    {
        $response = $this->get(route('ads.index'));
        
        $response->assertStatus(200)
            ->assertSee('Ads');
    }
    
    /**
     * @test
     */
    public function ads_get_listed()
    {
        $ads = factory(Ad::class, 5)->create();
        $response = $this->get(route('ads.index'));

        foreach ($ads as $ad) {
            $response->assertSee($ad->admin_title);
        }
    }
    
    /**
     * @test
     */
    public function ads_create_page_available()
    {
        $this->get(route('ads.create'))
            ->assertSeeText('Ads')
            ->assertSeeText('Ad Details');
    }
    
    /**
     * @test
     */
    public function successfully_add_a_new_ad()
    {

        $image = \Illuminate\Http\UploadedFile::fake()->image('adTesting.png');
        
        $adDetails = [
            'admin_title' => 'testing 1 2 3',
            'link'        => 'http://test.com',
            'location'    => 'home',
            'position'    => 'first',
            'image'       => $image
            ];

        $response = $this->post(route('ads.store'), $adDetails);
        
        $this->assertDatabaseHas('ads', [
            'admin_title' => 'testing 1 2 3',
            'link'        => 'http://test.com',
            'location'    => 'home',
            'position'    => 'first'
        ]);

        $response->assertRedirect(route('ads.index'))
            ->assertSessionMissing('errors');
    }
    
    /**
     * @test
     */
    public function ad_edit_page_is_available()
    {
        $ad = factory(Ad::class)->create();

        $response = $this->get(route('ads.edit', [
                'ad' => $ad->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee($ad->admin_title)
                 ->assertSee($ad->link);
    }
    
    /**
     * @test
     */
    public function successfully_edit_a_ad()
    {
        $ad = factory(Ad::class)->create();

        $response = $this->put(
            route('ads.update', ['ad' => $ad->id]), 
            ['admin_title' => 'new admin title',
            'link'        => 'http://test.com',
            'status'      => 0,
        ]);

            $this->assertDatabaseHas('ads', [
                'admin_title'       => 'new admin title',
                'link'        => 'http://test.com',
                'status'      => 0,
            ]);

            $response->assertRedirect(route('ads.index'))
                ->assertSessionMissing('errors');
    }
}
