<?php

use App\Models\User;
use App\Models\UserRole;
use Faker\Factory;
use Faker\Generator;

abstract class AdminLoggedInTestCase extends TestCase
{
    protected $user;

    /** @var Generator */
    protected $faker;

    public function setUp()
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->user = factory(User::class)->create(['status' => 'admin']);

        // Create a super admin role and attach it to the user.
        $roles = ['categories', 'courses', 'course-containers', 'course-upsells', 'surveys', 'read', 'write'];
        $permissions = array_pluck(array_where(UserRole::$availablePermissions, function ($value) use ($roles) {
        	return in_array($value['name'], $roles);
        }), 'permissions', 'name');

        $role = factory(UserRole::class)->create(compact('permissions'));

        $this->user->adminRole()->attach($role);

        $this->actingAs($this->user);
    }
}
