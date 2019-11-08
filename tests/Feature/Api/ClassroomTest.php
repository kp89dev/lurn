<?php

namespace Feature\Classroom;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClassroomTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function notes_are_saved()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);
        $user = factory(User::class)->create();

        $user->enroll($course);

        $notes = ['notes' => 'testing 1, 2, 3'];

        $this
            ->actingAs($user)
            ->post("/api/notes/$course->id", $notes + ['lesson' => $lesson->id])
            ->assertStatus(200);

        $this->assertDatabaseHas('lesson_user_notes', $notes);
    }

    /**
     * @test
     */
    public function notes_arent_saved_if_not_enrolled()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);
        $user = factory(User::class)->create();

        $notes = ['notes' => 'testing 1, 2, 3'];

        $this
            ->actingAs($user)
            ->post("/api/notes/$course->id", $notes + ['lesson' => $lesson->id])
            ->assertStatus(200);

        $this->assertDatabaseMissing('lesson_user_notes', $notes);
    }

    /**
     * @test
     */
    public function notes_arent_saved_if_lesson_missing()
    {
        $course = factory(Course::class)->create();
        $user = factory(User::class)->create();
        $notes = ['notes' => 'testing 1, 2, 3'];

        $this
            ->actingAs($user)
            ->post("/api/notes/$course->id", $notes + ['lesson' => 0])
            ->assertStatus(200);

        $this->assertDatabaseMissing('lesson_user_notes', $notes);
    }

    /**
     * @test
     */
    public function lesson_and_course_gets_completed()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id]);
        $lesson = factory(Lesson::class, 2)->create(['module_id' => $module->id, 'status' => 1]);
        $user = factory(User::class)->create();

        $user->enroll($course);

        $this
            ->actingAs($user)
            ->post(
                "/api/complete-lesson/$course->id",
                ['lesson' => $lesson[0]->id, 'link' => false, 'sidebar' => $lesson[1]->id]
            )
            ->assertStatus(200);

        $this->assertDatabaseHas('lesson_subscriptions', [
            'user_id'   => $user->id,
            'lesson_id' => $lesson[0]->id,
        ]);

        $this
            ->actingAs($user)
            ->post(
                "/api/complete-lesson/$course->id",
                ['lesson' => $lesson[1]->id, 'link' => false]
            )
            ->assertStatus(200);

        $this->assertDatabaseHas('lesson_subscriptions', [
            'user_id'   => $user->id,
            'lesson_id' => $lesson[1]->id,
        ]);

        $this->assertDatabaseHas('user_courses', ['user_id' => $user->id, 'course_id' => $course->id]);
    }

    /**
     * @test
     */
    public function lesson_doesnt_get_completed_if_not_enrolled()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' => $course->id]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);

        $user = factory(User::class)->create();

        $this
            ->actingAs($user)
            ->post("/api/complete-lesson/$course->id", ['lesson' => $lesson->id])
            ->assertStatus(200);

        $this->assertDatabaseMissing('lesson_subscriptions', [
            'user_id'   => $user->id,
            'lesson_id' => $lesson->id,
        ]);
    }

    /**
     * @test
     */
    public function lesson_doesnt_get_completed_if_lesson_missing()
    {
        $course = factory(Course::class)->create();
        $user = factory(User::class)->create();

        $user->enroll($course);

        $this
            ->actingAs($user)
            ->post("/api/complete-lesson/$course->id", ['lesson' => 0])
            ->assertStatus(200);

        $this->assertDatabaseMissing('lesson_subscriptions', [
            'user_id'   => $user->id,
            'lesson_id' => 0,
        ]);
    }
}