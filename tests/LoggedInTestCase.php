<?php

use App\Models\User;

abstract class LoggedInTestCase extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create(['status' => 'confirmed']);
        $this->actingAs($this->user);
    }
}
