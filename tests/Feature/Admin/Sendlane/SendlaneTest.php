<?php
namespace Feature\Admin\Sendlane;

use App\Models\Sendlane;

class SendlaneTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function index_page_returns_db_accounts()
    {
        $accounts = factory(Sendlane::class, 3)->create();

        $response = $this->get(route('sendlane.index'));

        $response->assertStatus(200);

        foreach ($accounts as $account) {
            $response->assertSee($account->email)
                     ->assertSee($account->api);
        }
    }

    /**
     * @test
     */
    public function add_new_account_page_is_available()
    {
        $response = $this->get(route('sendlane.create'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function successfully_adds_new_sendlane_account()
    {
        $data = [
            'email' => 'test@sendlane.com',
            'subdomain' => 'sendlanesub',
            'api'   => 'api_string_goes_here',
            'hash'  => 'hash_string'
        ];

        $response = $this->post(route('sendlane.store'), $data);

        $response->assertStatus(302)
                 ->assertSessionHas('alert-success');

        $this->assertDatabaseHas('sendlane', $data);
    }

    /**
     * @test
     */
    public function edit_page_sendlane_returns_success_status()
    {
        $existingAcc = factory(Sendlane::class)->create([
            'email' => 'old@sendlane.com',
            'subdomain' => 'oldsendlanesub',
            'api'   => 'old_api',
            'hash'  => 'old_hash'
        ]);

        $response = $this->get(route('sendlane.edit', ['sendlane' => $existingAcc->id]));

        $response->assertStatus(200)
                 ->assertSee($existingAcc->email)
                 ->assertSee($existingAcc->api)
                 ->assertSee($existingAcc->hash);
    }

    /**
     * @test
     */
    public function successfully_edit_sendlane_account()
    {
        $oldData = [
            'email'     => 'old@sendlane.com',
            'api'       => 'old_api',
            'subdomain' => 'oldsendlanesub',
            'hash'      => 'old_hash'
        ];

        $newData = [
            'email'     => 'new@sendlane.com',
            'subdomain' => 'newsendlanesub',
            'api'       => 'new_api',
            'hash'      => 'new_hash'
        ];

        $existingAcc = factory(Sendlane::class)->create($oldData);

        $response = $this->put(route('sendlane.update', ['sendlane' => $existingAcc->id]), $newData);

        $response->assertStatus(302)
                ->assertSessionHas('alert-success');

        $this->assertDatabaseHas('sendlane', $newData);
        $this->assertDatabaseMissing('sendlane', $oldData);
    }
}
