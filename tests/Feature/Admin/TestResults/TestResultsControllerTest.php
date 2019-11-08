<?php
namespace Feature\Admin\TestResults;

use App\Models\Course;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\User;
use niklasravnsborg\LaravelPdf\Pdf;
use niklasravnsborg\LaravelPdf\PdfWrapper;

class TestResultsControllerTest extends \SuperAdminLoggedInTestCase
{
    /**
     * @test
     */
    public function results_test_page_displays_correctly()
    {
        $user   = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $test   = factory(Test::class)->create(['course_id' => $course->id]);

        factory(TestResult::class, 3)->create([
            'user_id' => $user->id,
            'test_id' => $test->id
        ]);

        $response = $this->get(route('test-results.index'));
        $response->assertSee('Test Results')
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function one_result_page_shows_correctly()
    {
        $user   = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $test   = factory(Test::class)->create(['course_id' => $course->id]);

        $tR = factory(TestResult::class)->create([
            'user_id' => $user->id,
            'test_id' => $test->id
        ]);

        $response = $this->get(route('test-results.show', ['testResult' => $tR]));
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function pdf_downloads_successfully()
    {
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $test = factory(Test::class)->create(['course_id' => $course->id]);

        $tR = factory(TestResult::class)->create([
            'user_id' => $user->id,
            'test_id' => $test->id
        ]);

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

        $response = $this->get(route('test-results.download-pdf', ['testResult' => $tR]));

        self::assertEquals('pdf-file', $response->getContent());
    }
}
