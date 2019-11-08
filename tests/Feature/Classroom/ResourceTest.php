<?php

namespace Feature\Classroom;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Labels;
use App\Models\Lesson;
use App\Models\LessonSubscriptions;
use App\Models\TestResult;
use App\Models\User;
use App\Models\Course;
use App\Models\Module;
use App\Models\Test;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\TestQuestion;
use App\Models\TestQuestionAnswer;
use App\Http\Controllers\Admin\CourseBonuses\ResourceController;
use App\Models\CourseBonus;

class ResourceTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function index_displays_course_list()
    {
        $courses = factory(Course::class, 10)->create();

        $response = $this->get(route('classroom'));

        foreach($courses as $course){
            $response->assertSee(htmlspecialchars($course->title, ENT_QUOTES));
        }
    }
    
    /**
     * @test
     */
    public function index_not_display_disabled_course()
    {
        $courses = factory(Course::class, 10)->create();
        $course = factory(Course::class)->create(['status' => 0, 'title' => 'This is a Unique Title']);

        $response = $this->get(route('classroom'));
        
        foreach($courses as $singleCourse){
            $response->assertSee(htmlspecialchars($singleCourse->title, ENT_QUOTES));
        }

        $response->assertDontSeeText($course->title);
    }
    
    /**
     * @test
     */
    public function index_display_purchasable_courses()
    {
        $courses = factory(Course::class, 10)->create();
        $course = factory(Course::class)->create(['title' => 'This is a Unique Title', 'purchasable' => 0]);

        $response = $this->get(route('classroom'));
        
        foreach($courses as $singleCourse){
            $response->assertSee(htmlspecialchars($singleCourse->title, ENT_QUOTES));
        }

        $response->assertDontSeeText($course->title);
    }

    /**
     * Course Tests.
     */

    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function course_not_found()
    {
        $this->get(route('course', '__non_existent__'))
            ->assertStatus(404);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function course_disabled()
    {
        $course = factory(Course::class)->create([
            'title' => 'title',
            'description' => 'description',
            'snippet' => 'snippet',
            'status' => 0
        ]);

        $this->get(route('course', $course->slug))
            ->assertStatus(404);
    }

    /**
     * @test
     */
    public function course_found_loggedin_not_enrolled()
    {
        $label = factory(Labels::class)->create();
        $course = factory(Course::class)->create([
            'title'  => 'just another test',
            'free'   => 1,
            'status' => 1,
            'label_id' => $label->id
        ]);
        $user = factory(User::class)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('course', $course->slug));

        $response
            ->assertStatus(200)
            ->assertSeeText('just another test')
            ->assertSeeText('Enroll');

        self::assertTrue(str_contains($response->getContent(), $label->title));

        self::assertFalse(user_enrolled($course, $user));
        self::assertFalse(str_contains($response->getContent(), '<h2>Modules</h2>'));
    }

    /**
     * @test
     */
    public function course_found_not_loggedin_not_enrolled()
    {
        $label = factory(Labels::class)->create();
        $course = factory(Course::class)->create([
            'title'  => 'just another test',
            'free'   => 1,
            'status' => 1,
            'label_id' => $label->id
        ]);

        $response = $this
            ->get(route('course', $course->slug));

        $response
            ->assertStatus(200)
            ->assertSeeText('just another test')
            ->assertSeeText('Enroll');

        self::assertTrue(str_contains($response->getContent(), $label->title));

        self::assertFalse(str_contains($response->getContent(), '<h2>Modules</h2>'));
        self::assertFalse(str_contains($response->getContent(), '<div class="progress-bar">'));
    }

    /**
     * @test
     */
    public function course_found_loggedin_enrolled()
    {
        $label = factory(Labels::class)->create();
        $course = factory(Course::class)->create([
            'title'  => 'just another test',
            'status' => 1,
            'label_id' => $label->id
        ]);

        $user = factory(User::class)->create();
        $user->enroll($course);

        $response = $this
            ->actingAs($user)
            ->get(route('course', $course->slug));

        $response
            ->assertStatus(200)
            ->assertSeeText('just another test')
            ->assertDontSeeText('Buy Course');

        self::assertTrue(user_enrolled($course, $user));
        self::assertTrue(str_contains($response->getContent(), '<h2>Modules</h2>'));
        self::assertTrue(str_contains($response->getContent(), '<div class="progress-bar">'));

        self::assertFalse(str_contains($response->getContent(), $label->title));
    }

    /**
     * @test
     */
    public function bonus_course_section_invisible_if_no_courses()
    {
        $course = factory(Course::class)->create([
            'title'  => 'just another test',
            'status' => 1,
        ]);

        $user = factory(User::class)->create();
        $user->enroll($course);

        $response = $this
            ->actingAs($user)
            ->get(route('course', $course->slug));

        $response
            ->assertStatus(200)
            ->assertSeeText('just another test')
            ->assertDontSeeText('Additional Content');
    }

    /**
     * @test
     */
    public function bonus_section_hidden_if_not_enrolled()
    {
        $course = factory(Course::class)->create([
            'title'  => 'just another test',
            'status' => 1,
        ]);

        $bonus = factory(Course::class)->create([
            'title'     => 'Bonus Course',
            'status'    => 1,
        ]);

        $bonusCourseEntry = factory(CourseBonus::class)->create([
            'course_id'         => $course->id,
            'bonus_course_id'   => $bonus->id
        ]);

        $user = factory(User::class)->create();
        $user->enroll($course);

        $response = $this
            ->actingAs($user)
            ->get(route('course', $course->slug));

        $response->assertStatus(200)
            ->assertSee($course->title)
            ->assertDontSee('Additional Content');
    }

    /**
     * @test
     */
    public function bonus_course_visible_if_bonus_enrolled()
    {
        $course = factory(Course::class)->create([
            'title'  => 'just another test',
            'status' => 1,
        ]);

        $bonus = factory(Course::class)->create([
            'title'     => 'Bonus Course',
            'status'    => 1,
        ]);

        $bonusCourseEntry = factory(CourseBonus::class)->create([
            'course_id'         => $course->id,
            'bonus_course_id'   => $bonus->id
        ]);

        $user = factory(User::class)->create();
        $user->enroll($course);
        $user->enroll($bonus);

        $response = $this
            ->actingAs($user)
            ->get(route('course', $course->slug));

        $response->assertStatus(200)
            ->assertSee($course->title)
            ->assertSee('Additional Content')
            ->assertSee($bonus->title);

    }


    /**
     * @test
     */
    public function lesson_followed_by_test_can_see_test_link()
    {
        $course = factory(Course::class)->create([
            'title'  => 'Random Course',
            'status' => 1,
        ]);

        $module = factory(Module::class)->create([
            'title'     => 'Random Module',
            'status'    => 1,
            'type'      => 'Module',
            'course_id' => $course->id,
        ]);

        $lesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 1, 'type' => 'Lesson']);

        factory(Test::class)->create([
            'title'           => 'Fake Test',
            'status'          => 1,
            'after_lesson_id' => $lesson->id,
        ]);

        $user = factory(User::class)->create();
        $user->enroll($course);

        $response = $this->actingAs($user)
            ->get(route('lesson', [$course->slug, $module->slug, $lesson->slug]));

        $response->assertStatus(200)
            ->assertSeeText('Fake Test');
    }
    /**
     * Test tests
     */

    /**
     * @test
     */
    public function test_is_accessible_to_enrolled_user()
    {
        $course = factory(Course::class)->create(['title' => 'Random Course', 'status' => 1]);

        $module = factory(Module::class)->create([
            'title' => 'Random Module',
            'status' => 1,
            'type'  => 'Module',
            'course_id' => $course->id,
        ]);

        $lesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 1]);

        $test = factory(Test::class)->create([
            'course_id' => $course->id,
            'title' => 'Fake Test',
            'status' => 1,
            'after_lesson_id' => $lesson->id
        ]);

        $user = factory(User::class)->create();
        $user->enroll($course);

        $response = $this
            ->actingAs($user)
            ->get(route('test', [$course->slug, $module->slug, $test->id]));

        $response
            ->assertStatus(200)
            ->assertSeeText('Fake Test')
            ->assertSee('Submit');
    }

    public function success_page_shows_to_user_with_passed_test()
    {
        $course = factory(Course::class)->create(['title' => 'Random Course', 'status' => 1]);

        $module = factory(Module::class)->create([
            'title' => 'Random Module',
            'status' => 1,
            'type'  => 'Module',
            'course_id' => $course->id,
        ]);

        $lesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 1]);

        $test = factory(Test::class)->create([
            'title' => 'Fake Test',
            'status' => 1,
            'after_lesson_id' => $lesson->id
        ]);

        factory(TestResult::class)->create([
            'user_id'   => $this->user->id,
            'test_id'   => $test->id,
            'mark'      => 95.0,
        ]);

        $response = $this->get(route('test', [
            'course'    => $course,
            'module'    => $module,
            'test'      => $test->id
        ]));

        $response->assertStatus(200)
            ->assertSee('Congraulations')
            ->assertSee($test->title);
    }

    /**
     * skip
     */
    public function certificate_button_is_there()
    {
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);

        $course = factory(Course::class)->create(['title' => 'Random Course', 'status' => 1]);

        $module = factory(Module::class)->create([
            'title' => 'Random Module',
            'status' => 1,
            'type'  => 'Module',
            'course_id' => $course->id,
        ]);

        $lesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 1]);
        $certificate = factory(Certificate::class)->create([
            'course_id' => $course->id
        ]);

        $test = factory(Test::class)->create([
            'title' => 'Fake Test',
            'status' => 1,
            'after_lesson_id' => $lesson->id,
            'certificate_id'  => $certificate->id
        ]);

        factory(TestResult::class)->create([
            'user_id'   => $this->user->id,
            'test_id'   => $test->id,
            'mark'      => 95.0,
        ]);

        $response = $this->get(route('test', [
            'course'    => $course->slug,
            'module'    => $module->slug,
            'test'      => $test->id
        ]));

        $certUrl = route('test-certificate',[
            'course'=>$course->slug,
            'module'=>$module->slug,
            'test'=>$test->id ]);

        $response->assertStatus(200)
            ->assertSee('Congraulations')
            ->assertSee($test->title)
            ->assertSee($certUrl);
    }

    /**
     * @test
     */
    public function test_check_answers()
    {
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);

        $course = factory(Course::class)->create(['title' => 'Random Course', 'status' => 1]);

        $module = factory(Module::class)->create([
            'title' => 'Random Module',
            'status' => 1,
            'type'  => 'Module',
            'course_id' => $course->id,
        ]);

        $lesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 1]);

        $test = Test::create([
            'title' => 'Fake Test',
            'status' => 1,
            'after_lesson_id' => $lesson->id
        ]);

        for ($i = 0; $i < 4; $i++) {
            $q = factory(TestQuestion::class)->create([
                'test_id'       => $test->id,
                'order'         => $i,
                'question_type' => 'Radio',
                'status'        => 1,
            ]);

            for ($j = 0; $j < 4; $j++) {
                $a = factory(TestQuestionAnswer::class)->create([
                    'question_id' => $q->id,
                    'order'       => $j,
                    'is_answer'   => $isAnswer = intval($i === $j),
                    'status'      => 1,
                ]);

                if ($isAnswer) {
                    $answers[$q->id] = $a->id;
                }
            }
        }

        $onehundred = $test->checkAnswers($answers);

        foreach ($answers as $qid => $aid) {
            $answers[$qid] = -1;
        }

        $zero = $test->checkAnswers($answers);

        $this->assertEquals('100.0', $onehundred['mark']);
        $this->assertEquals('0.0', $zero['mark']);

    }

    /**
     * @test
     */
    public function test_check_answers_checkbox()
    {
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);

        $course = factory(Course::class)->create(['title' => 'Random Course', 'status' => 1]);

        $module = factory(Module::class)->create([
            'title' => 'Random Module',
            'status' => 1,
            'type'  => 'Module',
            'course_id' => $course->id,
        ]);

        $lesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 1]);

        $test = Test::create([
            'title' => 'Fake Test',
            'status' => 1,
            'after_lesson_id' => $lesson->id
        ]);

        $corrects = [];
        for($i = 0; $i < 4; $i++) {
            $q = factory(TestQuestion::class)->create([
                'test_id'   => $test->id,
                'order'     => $i,
                'question_type' => 'Checkbox',
                'status'    => 1
            ]);

            for($j = 0; $j < 4; $j++) {
                $is_answer = 0;
                switch($i) {
                    case 0:
                        //one correct
                        if($j == 1) {
                            $is_answer = 1;
                        }
                        break;
                    case 1:
                        //all correct
                        $is_answer = 1;
                        break;
                        //3 correct
                    case 2:
                        //2 correct
                        if($j < 2) {
                            $is_answer = 1;
                        }
                        break;
                    default:
                        //2 nonconsecutive correct
                        $is_answer = $j % 2;
                }
                $a = factory(TestQuestionAnswer::class)->create([
                    'question_id'   => $q->id,
                    'order'         => $j,
                    'is_answer'     => $is_answer,
                    'status'    => 1
                ]);
                if($is_answer) {
                    if(!isset($corrects[$q->id][$a->id])) {
                        $corrects[$q->id][$a->id] = [];
                    }
                    array_push($corrects[$q->id][$a->id], $a->id.'');
                }
            }
        }

        $onehundred = $test->checkAnswers($corrects);

        foreach($corrects as $qid=>$aid) {
            foreach($aid as $k=>$v) {
                $corrects[$qid][$k*2] = '-1';
            }

        }

        $zero = $test->checkAnswers($corrects);

        $this->assertEquals('100.0', $onehundred['mark']);
        $this->assertEquals('0.0', $zero['mark']);

    }

    /**
     * Module Tests.
     */

    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function module_not_found()
    {
        $course = factory(Course::class)->create(['status' => 1]);

        $this->get(route('module', [$course->slug, '__non_existent__']))
            ->assertStatus(404);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function module_disabled()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 0, 'type' => 'Module']);

        $this->get(route('module', [$course->slug, $module->slug]))
            ->assertStatus(404);
    }

    /**
     * @test
     */
    public function admin_sees_disabled_module()
    {
        $admin = factory(User::class)->create(['status' => 'admin']);

        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create([
            'course_id' => $course->id, 'status' => 0, 'type' => 'Module', 'title' => 'Disabled Module'
        ]);

        $this
            ->actingAs($admin)
            ->get(route('module', [$course->slug, $module->slug]))
            ->assertStatus(200)
            ->assertSee('Disabled Module');

    }

    /**
     * @test
     */
    public function module_not_found_not_loggedin_course_free_not_enrolled()
    {
        $course = factory(Course::class)->create(['status' => 1, 'free' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1, 'type' => 'Module']);

        $response = $this->get(route('module', [$course->slug, $module->slug]));

        $response->assertStatus(302);

        self::assertFalse(str_contains($response->getContent(), '<div class="progress-bar">'));
    }

    /**
     * @test
     */
    public function module_found_loggedin_enrolled()
    {
        $course = factory(Course::class)->create(['status' => 1, 'free' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1, 'type' => 'Module']);
        $user = factory(User::class)->create();
        $user->enroll($course);

        $response = $this
            ->actingAs($user)
            ->get(route('module', [$course->slug, $module->slug]));

        $response
            ->assertStatus(200)
            ->assertSeeText(htmlspecialchars($module->title, ENT_QUOTES))
            ->assertSeeText($module->description)
            ->assertSeeText('Choose a lesson');

        self::assertTrue(str_contains($response->getContent(), '<div class="progress-bar">'));
    }

    /**
     * @test
     */
    public function module_found_loggedin_course_not_free_not_enrolled()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1, 'type' => 'Module']);
        $user = factory(User::class)->create();

        $this
            ->actingAs($user)
            ->get(route('module', [$course->slug, $module->slug]))
            ->assertRedirect()
            ->assertSeeText(route('enroll', $course->slug));
    }

    /**
     * Lesson Tests.
     */

    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function lesson_not_found()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);

        $this->get(route('lesson', [$course->slug, $module->slug, '__non_existent__']))
            ->assertStatus(404);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function lesson_disabled()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 0]);

        $this->get(route('lesson', [$course->slug, $module->slug, $lesson->slug]))
            ->assertStatus(404);
    }



    /**
     * Notes Tests.
     */

    /**
     * @test
     */
    public function lists_notes()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $module = factory(Module::class)->create(['course_id' => $course->id, 'status' => 1]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id, 'status' => 1]);
        $user = factory(User::class)->create();
        $user->enroll($course);

        $notes = ['notes' => 'testing 1, 2, 3'];

        $this
            ->actingAs($user)
            ->post("/api/notes/$course->id", $notes + ['lesson' => $lesson->id])
            ->assertStatus(200);

        $this
            ->actingAs($user)
            ->get(route('notes', $course->slug))
            ->assertSeeText('testing 1, 2, 3');
    }

    /**
     * @test
     */
    public function no_notes_message()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        $user->enroll($course);

        $this
            ->actingAs($user)
            ->get(route('notes', $course->slug))
            ->assertSeeText("You haven't taken any notes yet.");
    }

    /**
     * @test
     */
    public function notes_can_be_printed()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();
        $user->enroll($course);

        $response = $this->actingAs($user)->get(route('print-notes', $course->slug));

        self::assertTrue(str_contains($response->getContent(), '<body onload="print()">'));
    }

    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function notes_arent_accessible_if_user_is_not_enrolled()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        $user = factory(User::class)->create();

        $this
            ->actingAs($user)
            ->get(route('notes', $course->slug))
            ->assertStatus(404);
    }

    /**
     * @test
     */
    public function lesson_marked_with_correct_class_when_course_is_M()
    {
        $course = factory(Course::class)->create([
            'status' => 1,
            'confirm_after' => 'M'
        ]);

        $user = factory(User::class)->create();
        $user->enroll($course);

        $module = factory(Module::class)->create(['course_id' =>  $course->id, 'type' => 'Module', 'status' => 1]);

        $lesson = factory(Lesson::class)->create([
            'module_id' =>  $module->id,
            'status' => 1,
            'order' => 1,
            'type' => 'Lesson',
        ]);
        $lesson2 = factory(Lesson::class)->create([
            'module_id' =>  $module->id,
            'status' => 1,
            'order' => 2,
            'type' => 'Lesson',
        ]);


        $response = $this
                ->actingAs($user)
                ->get(route('lesson', [$course->slug, $module->slug, $lesson->slug]));

        $this->assertNotContains('ask-if-complete', $response->getContent());
        $this->assertContains('silent-complete', $response->getContent());
    }

    /**
     * @test
     */
    public function lesson_marked_with_correct_class_when_course_is_L()
    {
        $course = factory(Course::class)->create([
            'status' => 1,
            'confirm_after' => 'L'
        ]);

        $user = factory(User::class)->create();
        $user->enroll($course);

        $module = factory(Module::class)->create(['course_id' =>  $course->id, 'type' => 'Module', 'status' => 1]);

        $lesson = factory(Lesson::class)->create([
            'module_id' =>  $module->id,
            'status' => 1,
            'order' => 1,
            'type' => 'Lesson',
        ]);
        $lesson2 = factory(Lesson::class)->create([
            'module_id' =>  $module->id,
            'status' => 1,
            'order' => 2,
            'type' => 'Lesson',
        ]);

        $response = $this->actingAs($user)
            ->get(route('lesson', [$course->slug, $module->slug, $lesson->slug]));

        $this->assertContains('ask-if-complete', $response->getContent());
        $this->assertNotContains('silent-complete', $response->getContent());
    }

    /**
     * @test
     */
    public function lesson_is_marked_if_last_in_module_of_M_course()
    {
        $course = factory(Course::class)->create([
            'status' => 1,
            'confirm_after' => 'M',
        ]);

        $user = factory(User::class)->create();
        $user->enroll($course);

        $module = factory(Module::class)->create(['course_id' =>  $course->id, 'status' => 1, 'type' => 'Module', 'order' => 1]);
        $module2 = factory(Module::class)->create(['course_id' =>  $course->id, 'status' => 1, 'type' => 'Module', 'order' => 2]);

        $lesson = factory(Lesson::class)->create([
            'module_id' =>  $module->id,
            'status' => 1,
            'order' => 1,
            'type' => 'Lesson',
        ]);

        $lesson2 = factory(Lesson::class)->create([
            'module_id' =>  $module->id,
            'status' => 1,
            'order' => 2,
            'type' => 'Lesson',
        ]);

        $lesson3 = factory(Lesson::class)->create([
            'module_id' =>  $module2->id,
            'status' => 1,
            'order' => 1,
            'type' => 'Lesson',
        ]);


        $response = $this->actingAs($user)
            ->get(route('lesson', [$course->slug, $module->slug, $lesson2->slug]));

        $this->assertContains('ask-if-complete', $response->getContent());
        $this->assertContains('mark-module', $response->getContent());
    }

    /**
     * @test
     */
    public function onboarding_not_boarded_redirects()
    {
        $course = factory(Course::class)->create();
        $onboardingModule = factory(Module::class)->create([
            'status' => 1,
            'title' => 'On-Boarding',
            'course_id' => $course->id,
            'type' => 'Module',
            'order' => 1,
        ]);
        $onboardingLesson = factory(Lesson::class)->create([
            'status' => 1,
            'title' => 'Welcome to On-Boarding!',
            'module_id' => $onboardingModule->id,
            'type' => 'Lesson',
        ]);
        $module = factory(Module::class)->create([
            'status' => 1,
            'title' => 'Introduction',
            'course_id' => $course->id,
            'type' => 'Module',
            'order' => 2,
        ]);
        $lesson = factory(Lesson::class)->create([
            'status' => 1,
            'title' => 'You are in!',
            'module_id' => $module->id,
            'type' => 'Lesson',
        ]);
        $user = factory(User::class)->create();

        $user->enroll($course);

        $this->actingAs($user)
            ->get(route('module', [$course->slug, $module->slug]))
            ->assertRedirect(route('lesson', [$course->slug, $onboardingModule->slug, $onboardingLesson->slug]));
    }

    /**
     * @test
     */
    public function onboarding_boarded_doesnt_redirect()
    {
        $course = factory(Course::class)->create();
        $onboardingModule = factory(Module::class)->create([
            'status' => 1,
            'title' => 'On-Boarding',
            'course_id' => $course->id,
            'type' => 'Module',
            'order' => 1,
        ]);
        $onboardingLesson = factory(Lesson::class)->create([
            'status' => 1,
            'title' => 'Welcome to On-Boarding!',
            'module_id' => $onboardingModule->id,
            'type' => 'Lesson',
        ]);
        $module = factory(Module::class)->create([
            'status' => 1,
            'title' => 'Introduction',
            'course_id' => $course->id,
            'type' => 'Module',
            'order' => 2,
        ]);
        $user = factory(User::class)->create();

        $user->enroll($course);

        $onboardingLesson->subscriptions()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('module', [$course->slug, $module->slug]))
            ->assertStatus(200);
    }
}
