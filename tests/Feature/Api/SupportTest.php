<?php

namespace Feature\Classroom;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupportTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @expectedException \Illuminate\Validation\ValidationException
     */
    public function support_message_is_validated()
    {
        $url = url('api/support-message');

        $this->post($url, [])->assertStatus(302);
        $this->post($url, ['message' => 'test'])->assertStatus(302);
        $this->post($url, ['user' => null, 'message' => 'test'])->assertStatus(302);
        $this->post($url, ['user' => ['email' => null], 'message' => 'test'])->assertStatus(302);
        $this->post($url, ['user' => ['email' => 'invalid'], 'message' => 'test'])->assertStatus(302);
    }

    /**
     * @test
     */
    public function support_message_is_sent()
    {
        $this->post(url('api/support-message'), ['user' => ['email' => 'test@test.com'], 'message' => 'test'])
            ->assertStatus(200)
            ->assertExactJson(['success' => true]);
    }
}
