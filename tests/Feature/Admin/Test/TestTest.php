<?php

namespace Tests\Admin\Test;

use App\Models\Test;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\TestQuestion;
use App\Models\TestQuestionAnswer;
use App\Models\User;
use niklasravnsborg\LaravelPdf\Pdf;
use niklasravnsborg\LaravelPdf\PdfWrapper;

class TestTest extends \AdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function test_list_page_is_available()
    {

        $course = factory(Course::class)->create();
        $response = $this->get(route('tests.index', ['course' => $course->id ]));

        $response->assertSee('Tests')
            ->assertSee('Add New Test')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function tests_get_listed()
    {
        $courses = factory(Course::class,2)->create();
        $module = factory(Module::class)->create([
            'course_id' => $courses[0]->id
        ]);
        
        $lessons = factory(Lesson::class,2)->create([
            'module_id' => $module->id
        ]);
        
        $tests = array();
        foreach($lessons as $lesson) {
            $tests[] = factory(Test::class)->create([
               'course_id' => $courses[0]->id,
               'after_lesson_id' => $lesson->id
            ]);
        }
        
        $otherModule = factory(Module::class)->create([
            'course_id' => $courses[1]
        ]);
        
        $otherLessons = factory(Lesson::class, 2)->create([
            'module_id' => $otherModule->id
        ]);

        $otherTests = array();
        foreach($otherLessons as $lesson) {
            $OtherTests[] = factory(Test::class)->create([
                'course_id' => $courses[1]->id,
                'after_lesson_id' => $lesson->id
            ]);
        }
        
        $response = $this->get(route('tests.index', ['course' => $courses[0]->id]));
        $response->assertStatus(200);

        foreach ($tests as $test) {
            $response->assertSee($test->title);
        }

        foreach ($otherTests as $test) {
            $response->assertDontSee($test->title);
        }
    }

    /**
     * @test
     */
    public function add_test_page_is_available()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create([
            'course_id' => $course->id
        ]);
        
        $lessons = factory(Lesson::class, 3)->create([
            'module_id' => $module->id
        ]);

        $response = $this->get(route('tests.create', ['course' => $course]));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function successfully_add_a_new_test()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create([
            'course_id' => $course->id
        ]);
        $lesson = factory(Lesson::class)->create([
            'module_id' => $module->id
        ]);

        $response = $this->post(
            route('tests.store', ['course' => $course->id]), [
                'course_id'         => $course->id,
                'after_lesson_id'   => $lesson->id,
                'title'             => 'Test Test',
                'status'            => 1
            ]
        );

        $this->assertDatabaseHas('tests', [
                'course_id'         => $course->id,
                'after_lesson_id'   => $lesson->id,
                'title'             => 'Test Test',
                'status'            => 1
            ]);

        $response->assertRedirect(route('tests.index', ['course' => $course->id]))
                 ->assertSessionMissing('errors');
    }

   
    /**
     * @test
     */
    public function edit_page_can_be_accessed()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' =>  $course->id]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);
        
        $test = factory(Test::class)->create(['course_id' => $course->id, 'after_lesson_id' => $lesson->id]);

        $response = $this->get(route('tests.edit', [
            'course' => $test->course->id,
            'test' => $test->id
        ]));

        $response->assertStatus(200)
            ->assertSee($test->title);
    }

    /**
     * @test
     */
    public function successfully_edit_a_test()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' =>  $course->id]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);
        
        $test = factory(Test::class)->create(['course_id' => $course->id, 'after_lesson_id' => $lesson->id]);

        $response = $this->put(
            route('tests.update', ['course_id' => $test->course_id, 'id' => $test->id]), [
                'title'             => 'new title',
                'course_id'         => $course->id,
                'after_lesson_id'   => $lesson->id,
                'status'            => ! $test->status
            
        ]);

        $this->assertDatabaseHas('tests', [
            'title'             => 'new title',
            'course_id'         => $course->id,
            'after_lesson_id'   => $lesson->id,
            'status'            => ! $test->status,
        ]);

        $response->assertRedirect(route('tests.index', ['course' => $course->id]))
                 ->assertSessionMissing('errors');
    }
    
    /**
     * @test
     */
    public function add_question_button_is_available()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create([
            'course_id' => $course->id
        ]);
        
        $lessons = factory(Lesson::class, 3)->create([
            'module_id' => $module->id
        ]);

        $response = $this->get(route('tests.create', ['course' => $course]));
        $response->assertStatus(200);
        
        $response->assertDontSee('Questions')
            ->assertDontSee('Add New Question');
    }
    
    /**
     * @test
     */
    public function test_questions_and_answers_are_deleted_with_test()
    {
        
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' =>  $course->id]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);
        
        $test = factory(Test::class)->create(['course_id' => $course->id, 'after_lesson_id' => $lesson->id]);

        $questions = factory(TestQuestion::class, 2)->create(['test_id' => $test->id]);
        
        $answersQ1 = factory(TestQuestionAnswer::class, 2)->create(['question_id' => $questions[0]->id]);
        $answersQ2 = factory(TestQuestionAnswer::class, 2)->create(['question_id' => $questions[1]->id]);
        
        $response = $this->delete(
                route('tests.destroy', ['test' => $test->id]),
                [],
                ['HTTP_REFERER' => route('tests.index', ['course' => $course->id])]
            );
        
        $response->assertRedirect(route('tests.index', ['course' => $course->id]))
            ->assertSessionHas(['alert-success']);
        
        $this->assertDatabaseMissing('test_question_answers', ['id' => $answersQ1[0]->id])
            ->assertDatabaseMissing('test_question_answers', ['id' => $answersQ1[1]->id])
            ->assertDatabaseMissing('test_question_answers', ['id' => $answersQ2[0]->id])
            ->assertDatabaseMissing('test_question_answers', ['id' => $answersQ2[1]->id])
            ->assertDatabaseMissing('test_questions', ['id' => $questions[0]->id])
            ->assertDatabaseMissing('test_questions', ['id' => $questions[1]->id])
            ->assertDatabaseMissing('tests', ['id' => $test->id]);
    }
    
    /**
     * @test
     */
    public function questions_are_listed()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' =>  $course->id]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);
        
        $test = factory(Test::class)->create(['course_id' => $course->id, 'after_lesson_id' => $lesson->id]);
        
        $questions = factory(TestQuestion::class, 5)->create(['test_id' => $test->id, 'status' => 1]);
        
        $response = $this->get(route('tests.edit', ['course' => $course->id, 'test' => $test->id]));
        $response->assertStatus(200)
            ->assertSee('Questions')
            ->assertSee($questions[0]->title)
            ->assertSee($questions[1]->title)
            ->assertSee($questions[2]->title);
            
    }
    
    /**
     * @test
     */
    public function successfully_add_a_question()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' =>  $course->id]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);
        
        $test = factory(Test::class)->create(['course_id' => $course->id, 'after_lesson_id' => $lesson->id]);
        
        //$org_questions = factory(TestQuestion::class, 5)->create(['test_id' => $test->id]);
        
        $response = $this->post(
            route('tests.create.question', [
                'course' => $course->id,
                'test' => $test->id
            ]), [
                'status'            => 1,
                'title'             => 'New Question',
                'question_type'     => 'Radio',
                'order'             => 0,
                'answers'           => [
                    0   =>  [
                        'title'     =>  'First wrong answer',
                        'is_answer' =>  0,
                    ],
                    1   =>  [
                        'title'     =>  'Second wrong answer',
                        'is_answer' =>  0,
                    ],
                    2   =>  [
                        'title'     =>  'First correct answer',
                        'is_answer' =>  1,
                    ],
                    3   =>  [
                        'title'     =>  'Third wrong answer',
                        'is_answer' =>  0,
                    ],
                ]
            ]
        );
        $this->assertDatabaseHas('test_questions', [
                'test_id'       => $test->id,
                'title'         => 'New Question',
                'question_type' => 'Radio'
            ]);
        $this->assertDatabaseHas('test_question_answers', [
            'title'     => 'First wrong answer',
            'is_answer' =>  0
        ]);
        $this->assertDatabaseHas('test_question_answers', [
            'title'     => 'Second wrong answer',
            'is_answer' =>  0
        ]);
        $this->assertDatabaseHas('test_question_answers', [
            'title'     => 'First correct answer',
            'is_answer' =>  1
        ]);
        $this->assertDatabaseHas('test_question_answers', [
            'title'     => 'Third wrong answer',
            'is_answer' =>  0
        ]);
        
    }
    
    /**
     * @test
     */
    public function successfully_edit_question_and_answers()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' =>  $course->id]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);
        
        $test = factory(Test::class)->create(['course_id' => $course->id, 'after_lesson_id' => $lesson->id]);
        $question = factory(TestQuestion::class)->create(['test_id' => $test->id, 'question_type' => 'Radio']);
        
        $answers = factory(TestQuestionAnswer::class, 3)->create(['question_id' => $question->id]);
        
        //$org_questions = factory(TestQuestion::class, 5)->create(['test_id' => $test->id]);
        
        $response = $this->post(
            route('tests.create.question', [
                'course' => $course->id,
                'test' => $test->id
            ]), [
                'status'            => 1,
                'title'             => $question->title . ' TEST EDIT',
                'question_type'     => $question->question_type,
                'question_id'       => $question->id,
                'order'             => 0,
                'answers'           => [
                    0   =>  [
                        'title'     =>  $answers[0]->title,
                        'is_answer' =>  0,
                        'id'        => $answers[0]->id,
                    ],
                    1   =>  [
                        'title'     =>  $answers[1]->title,
                        'id'        => $answers[1]->id,
                    ],
                    2   =>  [
                        'title'     =>  'First correct answer',
                        'is_answer' =>  1,
                    ]
                ]
            ]
            );
        //dd($response);
        $this->assertDatabaseHas('test_questions', [
            'test_id'       => $test->id,
            'title'         => $question->title . ' TEST EDIT',
            'question_type' => $question->question_type,
            'id'            => $question->id,
        ]);
        
        $this->assertDatabaseHas('test_question_answers', [
            'title'     => $answers[0]->title,
            'is_answer' => 0,
        ]);
        
        $this->assertDatabaseHas('test_question_answers', [
            'title'     => $answers[1]->title,
            'is_answer' => 0,
        ]);
        
        $this->assertDatabaseHas('test_question_answers', [
            'title'     => 'First correct answer',
            'is_answer' =>  1
        ]);
        
        $this->assertDatabaseMissing('test_question_answers', [
            'title'     => $answers[2]->title,
            'is_answer' =>  $answers[2]->is_answer
        ]);
    }

    /**
     * @test
     */
    public function successfully_delete_question()
    {
        $course = factory(Course::class)->create();
        $module = factory(Module::class)->create(['course_id' =>  $course->id]);
        $lesson = factory(Lesson::class)->create(['module_id' => $module->id]);
        
        $test = factory(Test::class)->create(['course_id' => $course->id, 'after_lesson_id' => $lesson->id]);
        $question = factory(TestQuestion::class)->create(['test_id' => $test->id, 'question_type' => 'Radio']);
        
        factory(TestQuestionAnswer::class, 5)->create(['question_id' => $question->id]);
        
        $this->delete(
            route('tests.delete.question', [
                'test' => $test->id,
                'questionId' => $question->id,
            ]));
        
        $this->assertDatabaseMissing('test_questions', [
            'id'    => $question->id
        ]);
        
    }

    /**
     * @test
     */
    public function test_answers_view_displays_correctly()
    {
        $course = factory(Course::class)->create();
        $test   = factory(Test::class)->create(['course_id' => $course->id]);

        $response = $this->get(route('tests.show', compact('course', 'test')));
        $response->assertSee($test->title)
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function test_page_shows_correctly()
    {
        $course = factory(Course::class)->create();
        $test   = factory(Test::class)->create(['course_id' => $course->id]);
        $tq     = factory(TestQuestion::class)->create(['test_id' => $test->id]);
        $tac    = factory(TestQuestionAnswer::class)->create(['question_id' => $tq->id, 'is_answer' => 1]);
        $tai    = factory(TestQuestionAnswer::class, 2)->create(['question_id' => $tq->id, 'is_answer' => 0]);

        $response = $this->get(route('tests.show', compact('course', 'test')));

        $response->assertStatus(200);
        $response->assertSee($tac->title);

        foreach ($tai as $t) {
            $response->assertSee($t->title);
        }

        $response->assertSee('#7BB661'); //bg color for correct answer - meaning correct answer is marked correctly
    }

    /**
     * @test
     */
    public function pdf_downloads_successfully()
    {
        $course = factory(Course::class)->create();
        $test = factory(Test::class)->create(['course_id' => $course->id]);

        $pdfMock = self::createMock(Pdf::class);
        $pdfMock->expects($this->once())->method('stream')->willReturn(new class() {
            public function __toString() {
                return 'pdf-file';
            }
        });

        $this->app->bind(PdfWrapper::class, function($app) use ($pdfMock){
            $pdfService = self::createMock(PdfWrapper::class);
            $pdfService->expects($this->once())
                ->method('loadView')
                ->willReturn($pdfMock);

            return $pdfService;
        });

        $response = $this->get(route('tests.download-pdf', compact('course', 'test')));

        self::assertEquals('pdf-file', $response->getContent());
    }
}
