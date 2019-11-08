<?php
namespace Tests\Admin\User;

use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SearchTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;
    /**
     * @test
     */
    public function results_from_user_table_are_returned()
    {
        $users = new Collection();
        for($i=0; $i<6; $i++) {
            $users[] = factory(User::class)->create([
                'email' => str_random(5) . 'test@domain.com'
            ]);
        }

        $response = $this->get(route('users.search', ['term' => 'test']));

        $response->assertStatus(200);
        $responseArray = $response->decodeResponseJson();

        foreach ($users as $user) {
            if ($this->assertUserIsPresent($user, $responseArray)) {
                continue;
            }

            self::assertTrue(
                false,
                "Failed when trying to see if the returned json contains user " . $user
            );
        }
    }

    /**
     * @test
     */
    public function results_from_users_table_and_imported_are_returned()
    {
        $users = factory(User::class, 2)->create([
            'email' => function(){ return str_random(5) . 'test@domain.com';}
        ]);
        
        $importedUsers = factory(ImportedUser::class, 2)->create([
            'email' => function(){ return str_random(5) . 'test@domain.com';}
        ]);

        $response = $this->get(route('users.search', ['term' => 'test']));

        $response->assertStatus(200);
        $responseArray = $response->decodeResponseJson();

        foreach ($users as $user) {
            if ($this->assertUserIsPresent($user, $responseArray)) {
                continue;
            }

            self::assertTrue(
                false,
                "Failed when trying to see if the returned json contains user " . $user
            );
        }

        foreach ($importedUsers as $user) {
            if ($this->assertImportedUserIsPresent($user, $responseArray)) {
                continue;
            }

            self::assertTrue(
                false,
                "Failed when trying to see if the returned json contains user " . $user
            );
        }
    }

    private function assertUserIsPresent($user, $responseArray)
    {
        foreach ($responseArray as $item) {
            if (
                isset($item['id'], $user['id']) &&
                $item['id']    === $user['id'] &&
                $item['email'] === $user['email'] &&
                $item['name']  === $user['name']
            ) {
                return true;
            }
        }
    }

    private function assertImportedUserIsPresent($user, $responseArray)
    {
        foreach ($responseArray as $item) {
            if (
                isset($item['an_id'], $user['an_id']) &&
                $item['an_id']    === $user['an_id'] &&
                $item['email'] === $user['email'] &&
                $item['name']  === $user['name']
            ) {
                return true;
            }
        }
    }
}
