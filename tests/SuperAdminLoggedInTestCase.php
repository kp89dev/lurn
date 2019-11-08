<?php

use App\Models\User;
use App\Models\UserRole;
use Faker\Factory;
use Faker\Generator;

abstract class SuperAdminLoggedInTestCase extends TestCase
{
    protected $user;

    /** @var Generator */
    protected $faker;

    public function setUp()
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->user = factory(User::class)->create(['status' => 'super-admin']);

        // Create a super admin role and attach it to the user.
        $permissions = array_pluck(UserRole::$availablePermissions, 'permissions', 'name');
        $role = factory(UserRole::class)->create(compact('permissions'));

        $this->user->adminRole()->attach($role);

        $this->actingAs($this->user);
    }
}
