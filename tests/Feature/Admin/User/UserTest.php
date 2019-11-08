<?php
namespace Tests\Admin\User;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class UserTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;
    /**
     * @test
     */
    public function users_are_listed_correctly()
    {
        $users = factory(User::class, 5)->create();
        
        $reponse = $this->get(route('users.index'));

        $reponse->assertStatus(200);
        foreach ($users as $user) {
            $reponse->assertSee($user->email)
                    ->assertSee(htmlspecialchars($user->name, ENT_QUOTES));
        }
    }

    /**
     * @test
     */
    public function user_create_page_is_available()
    {
        $reponse = $this->get(route('users.create'));

        $reponse->assertStatus(200);
    }

    /**
     * @test
     */
    public function user_is_created_succesfully()
    {
        $courses = factory(Course::class, 3)->create(['status' => 1]);
        $user = [
            'email' => 'test@test.com',
            'name'  => 'Some Name'
        ];
        $userCourses = $courses->pluck('id')->take(2)->toArray();
        $response = $this->post(route('users.store'), $user + [
            'password'        => 'test1234',
            'repeat_password' => 'test1234',
            'courses'         => $userCourses,
            'status'          => 1,
        ]);
        
        $this->assertDatabaseHas('users', $user);
        $user = User::where('email', $user['email'])->first();

        foreach ($userCourses as $cId) {
            $this->assertDatabaseHas('user_courses', [
                'user_id'    => $user->id,
                'added_by'   => $this->user->id,
                'course_id'  => $cId,
                'status'     => 0
            ]);
        }

        $this->assertDatabaseMissing('user_courses', [
            'user_id'    => $user->id,
            'added_by'   => $this->user->id,
            'course_id'  => $courses->pluck('id')->splice(2)[0]
        ]);
    }

    /**
     * @test
     */
    public function user_edit_page_available()
    {
        $user = factory(User::class)->create();

        $response = $this->get(route('users.edit', ['user' => $user->id]));
        
        $response->assertStatus(200)
                ->assertSee(htmlspecialchars($user->name, ENT_QUOTES))
                ->assertSee($user->email);
    }

    /**
     * @test
     */
    public function user_edits_are_saved_successfully()
    {
        $courses = factory(Course::class, 3)->create(['status' => 0]);
        $user = factory(User::class)->create();
        $user->courses()->sync($courses->pluck('id')->take(2)); //first course

        $userCourses = $courses->pluck('id')->splice(1); //last 2 courses
        $response = $this->put(route('users.update', ['user' => $user->id]), [
            'name'    => 'New Name',
            'email'   => 'email@email.com',
            'courses' => $userCourses
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'name'  => 'New Name',
            'email' => 'email@email.com'
        ]);

        $this->assertDatabaseHas('user_courses', [
            'user_id' => $user->id,
            'course_id' => $courses[0]->id,
            'status' => 1,
            'cancelled_by' => $this->user->id
        ]);

        $this->assertDatabaseHas('user_courses', [
            'user_id'      => $user->id,
            'course_id'    => $courses[1]->id,
            'status'       => 0,
            'cancelled_by' => null,
            'added_by'     => 0
        ]);

        $this->assertDatabaseHas('user_courses', [
            'user_id'      => $user->id,
            'course_id'    => $courses[2]->id,
            'status'       => 0,
            'cancelled_by' => null,
            'added_by'     => $this->user->id
        ]);
    }

    /**
     * @test
     */
    public function admin_cannot_delete_its_own_account()
    {
        $response = $this->delete(
                             route('users.destroy', ['user' => $this->user->id]),
                             [],
                             ['HTTP_REFERER' => route('users.index')]
                         );

        $response->assertRedirect(route('users.index'))
                 ->assertSessionHas(['alert-danger']);
    }

    /**
     * @test
     */
    public function admin_can_succesfully_soft_delete_a_user_account()
    {
        $userToDelete = factory(User::class)->create();

        $response = $this
            ->delete(
                route('users.destroy', ['user' => $userToDelete->id]),
                [],
                ['HTTP_REFERER' => route('users.index')]
            );

        $response->assertRedirect(route('users.index'))
                 ->assertSessionHas(['alert-success']);

        $this->assertDatabaseHas('users', [
            'id'    => $userToDelete->id
        ]);
        
        $this->assertDatabaseMissing('users', [
            'id' => $userToDelete->id,
            'deleted_at' => null
        ]);
    }
    
    /**
     * @test
     */
    public function admin_can_see_login_as_user_option()
    {
        $user = factory(User::class)->create();
        
        $response = $this->get(route('users.show', ['user' => $user->id]));
        
        $response->assertStatus(200)
            ->assertSee('Login As User');
    }
    
    /**
     * @test
     */
    public function admin_can_not_see_login_as_admin_option()
    {
        $user = factory(User::class)->create(['status' => 99]);
        
        $response = $this->get(route('users.show', ['user' => $user->id]));
    
        $response->assertStatus(200)
            ->assertDontSee('Login As User');
    }

    /**
     * @test
     */
    public function admin_can_see_onboarding_status()
    {
        $course = factory(Course::class)->create(['status' => 0]);
        $user = factory(User::class)->create();
        $user->courses()->sync($course); //first course
        factory(Module::class)->create([
            'course_id' => $course->id,
            'slug' => 'on-boarding'
        ]);
        $response = $this->get(route('users.edit', ['user' => $user->id]));

        $response->assertStatus(200)
            ->assertSee(htmlspecialchars($user->name, ENT_QUOTES))
            ->assertSee(route('users.toggle-onboarding', ['user' => $user->id]));
    }

    /**
     * @test
     */
    public function admin_can_disable_or_enable_onboarding_status()
    {
        $course = factory(Course::class)->create(['status' => 0]);
        $user = factory(User::class)->create();
        $user->courses()->sync($course); //first course
        $module = factory(Module::class)->create([
            'course_id' => $course->id,
            'slug' => 'on-boarding',
            'status' => 1
        ]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 1]);


        $response = $this->post(route('users.toggle-onboarding', ['user' => $user->id]), [
            'course' => $course->id,
            'action' => 1
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lesson_subscriptions', [
            'user_id'      => $user->id,
            'lesson_id'    => $lesson->id
        ]);



        $response = $this->post(route('users.toggle-onboarding', ['user' => $user->id]), [
            'course' => $course->id,
            'action' => 0
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('lesson_subscriptions', [
            'user_id'      => $user->id,
            'lesson_id'    => $lesson->id
        ]);
    }
}
