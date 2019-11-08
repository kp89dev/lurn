<?php

namespace Feature;

use App\Models\User;
use App\Models\Faq;

class SupportTest extends \TestCase
{
    /**
     * @test
     */
    public function support_page_is_visible_to_everybody()
    {
        $this->get(route('support'))
            ->assertSeeText('Send us your issue using the form below.');
    }

    /**
     * @test
     */
    public function support_page_links_to_faq()
    {
        $this->get(route('support'))
            ->assertSee('Find quick answers to your most common questions here')
            ->assertSee(route('faq'));
    }
    
    /**
     * @test
     */
    public function support_page_links_to_account_merge()
    {
        $this->get(route('support'))
        ->assertSee('missing a course')
        ->assertSee(route('account-merge.index'));
    }
    
    /**
     * @test
     */
    public function user_details_are_present_to_be_used_to_prefill_the_form()
    {
        $user = factory(User::class)->create(['name' => 'Cosmin Gheorghita', 'email' => 'gecko.alpad@gmail.com']);

        $this
            ->actingAs($user)
            ->get(route('support'))
            ->assertSee('Cosmin Gheorghita')
            ->assertSee('gecko.alpad@gmail.com');
    }
    
    /**
     * @test
     */
    public function faq_page_is_available_to_everyone()
    {
        $this->get(route('faq'))
            ->assertStatus(200)
            ->assertSee('Frequently Asked Questions');
    }
    
    /**
     * @test
     */
    public function faq_page_lists_faqs()
    {
        $faq = factory(Faq::class)->create([
            'question' => 'A test question?',
            'answer' => 'The test answer.'
        ]);
        
        $this->get(route('faq'))
            ->assertStatus(200)
            ->assertSee('A test question?')
            ->assertSee('The test answer.');
    }
}