<?php

namespace Feature\Account;

use App\Events\User\UserEmailChanged;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

class SettingsTest extends \TestCase
{
    /**
     * @test
     */
    public function settings_page_is_available()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)
            ->get(route('profile'));

        $response->assertStatus(200)
            ->assertSee('Profile Picture')
            ->assertSee('Notifications')
            ->assertSee('Account Information');
    }

    /**
     * @test
     */
    public function settings_page_errors_when_old_password_is_incorrect()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('test123'),
        ]);

        $response = $this->actingAs($user)
            ->post(route('profile.store'), [
                'password_new'              => 'something',
                'password_old'              => 'wrong pass',
                'password_new_confirmation' => 'something',
            ]);

        $response->assertStatus(302)
            ->assertSessionHas('errors');
    }

    /**
     * @test
     */
    public function settings_page_saves_details_correctly_without_password()
    {
        Event::fake();
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->post(route('profile.store'), [
                'name'            => 'User Test',
                'email'           => 'user@test12345.com',
                'receive_updates' => 1,
            ]);

        $response->assertStatus(302)
            ->assertSessionMissing('errors');

        $this->assertDatabaseHas('users', [
            'name'  => 'User Test',
            'email' => 'user@test12345.com',
        ]);

        $this->assertDatabaseHas('user_settings', [
            'user_id'         => $user->id,
            'receive_updates' => 1,
        ]);

        Event::assertDispatched(UserEmailChanged::class, function ($e) use ($user) {
            return $e->user->email === $user->email;
        });
    }

    /**
     * @test
     */
    public function settings_store_changes_password_successfully()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt('test123'),
        ]);

        $response = $this->actingAs($user)
            ->post(route('profile.store'), [
                'receive_updates'           => 0,
                'password_old'              => 'test123',
                'password_new'              => 'something',
                'password_new_confirmation' => 'something',
            ]);

        $response->assertStatus(302)
            ->assertSessionMissing('errors');

        self::assertTrue(password_verify('something', $user->password));
    }

    /**
     * @test
     */
    public function user_can_successfully_change_his_avatar()
    {
        Storage::fake('static');

        $user = factory(User::class)->create();
        $setting = factory(UserSetting::class)->create([
            'user_id' => $user->id,
            'image'   => 'user/an_old_user_avatar.png',
        ]);

        Storage::disk('static')
            ->putFileAs(
                'user',
                UploadedFile::fake()->image('an_old_user_avatar.png'),
                'an_old_user_avatar.png'
            );

        $response = $this->actingAs($user)
            ->post(route('profile.store'), [
                'receive_updates' => 0,
                'image'           => UploadedFile::fake()->image('image.png'),
            ]);

        $response->assertStatus(302)
            ->assertSessionMissing('errors');

        $setting->refresh();

        Storage::disk('static')
            ->assertExists($setting->image);

        Storage::disk('static')
            ->assertMissing('user/an_old_user_avatar.png');
    }
}
