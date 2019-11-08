<?php

namespace Tests\Admin;

use App\Models\Feedback;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FeedbackTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function feedback_page_available()
    {
        $this->get(route('feedback.index'))
            ->assertSeeText('Feedback messages');
    }

    /**
     * @test
     */
    public function feedback_lists_feedbacks()
    {
        $feedback = factory(Feedback::class, 5)->create();
        $response = $this->get(route('feedback.index'));

        foreach ($feedback as $message) {
            $response->assertSeeText($message->feedback);
        }
    }

    /**
     * @test
     */
    public function feedback_details_available()
    {
        $feedback = factory(Feedback::class)->create(['grade' => 5]);

        $response = $this->get(route('feedback.show', $feedback->id));
        $response->assertStatus(200)
            ->assertSee('5')
            ->assertSee('Viewing '.$feedback->id);
    }
    /**
     * @test
     */
    public function feedback_gets_deleted()
    {
        $feedback = factory(Feedback::class)->create();

        $this->delete(route('feedback.destroy', $feedback->id));
        $this->assertDatabaseMissing('feedback', ['id' => $feedback->id]);
    }

    public function csv_is_available()
    {
        factory(Feedback::class, 5)->create();

        $response = $this->get(route('feedback.download-csv'));

        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
    }
}
