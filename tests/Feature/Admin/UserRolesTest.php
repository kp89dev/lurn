<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\UserRole;

class UserRolesTest extends \SuperAdminLoggedInTestCase
{
    protected $user;

    /**
     * @test
     */
    public function roles_page_is_guarded_and_available()
    {
        $this->assignRolesAccess();
        $response = $this->get(route('roles.index'));

        $response->assertSee('User Roles')->assertStatus(200);
    }

    /**
     * @test
     */
    public function roles_page_is_guarded_and_is_not_available()
    {
        $this->restrictRolesAccess();
        $response = $this->get(route('roles.index'));
        $response->assertRedirect(route('admin'));
    }

    /**
     * @test
     */
    public function role_edit_create_page_is_guarded_and_available()
    {
        $this->assignRolesAccess();
        $response = $this->get(route('roles.create'));

        $response->assertSee('User Roles')
            ->assertStatus(200);

        $testRole = factory(UserRole::class)->create();
        $response = $this->get(route('roles.edit', ['id' => $testRole->id]));

        $response->assertSee('User Roles')
            ->assertSee($testRole->title)
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function role_edit_create_page_is_guarded_and_is_not_available()
    {
        $this->restrictRolesAccess();
        $response = $this->get(route('roles.create'));
        $response->assertRedirect(route('admin'));

        $testRole = factory(UserRole::class)->create();
        $response = $this->get(route('roles.edit', ['id' => $testRole->id]));
        $response->assertRedirect('admin')
            ->assertDontSee($testRole->title);
    }

    /**
     * @test
     */
    public function role_store_update_is_guarded_and_created()
    {
        $this->assignRolesAccess();

        $newrole = factory(UserRole::class)->create(['permissions' => []]);

        $response = $this->post(route('roles.store'), [
            'title'       => $newrole->title,
            'permissions' => json_encode($newrole->permissions),
        ]);

        $this->assertDatabaseHas('roles', [
            'title'       => $newrole->title,
            'permissions' => json_encode($newrole->permissions),
        ]);

        $response->assertRedirect(route('roles.index'));
    }

    /**
     * @test
     */
    public function role_store_update_is_guarded_and_is_not_available()
    {
        $this->restrictRolesAccess();

        $response = $this->post(route('roles.store'), [
            'title'       => $title = str_random(),
            'permissions' => [],
        ]);

        $this->assertDatabaseMissing('roles', [
            'title'       => $title,
            'permissions' => '[]',
        ]);

        $response->assertRedirect(route('admin'));
    }

    private function restrictRolesAccess()
    {
        $this->user->adminRole()->sync([]);
    }

    private function assignRolesAccess()
    {
        $role = factory(UserRole::class)->create(['permissions' => ['user-roles' => ['read', 'write']]]);
        $this->user->adminRole()->attach($role);
    }

}
