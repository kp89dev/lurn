<?php
namespace Tests;

use App\Models\User;

abstract class UserLoggedInTestCase extends \TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }
}
