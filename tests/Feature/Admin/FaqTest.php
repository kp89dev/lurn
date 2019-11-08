<?php

namespace Tests\Admin;

use App\Models\Faq;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FaqTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function faq_page_available()
    {
        $this->get(route('faq.index'))
            ->assertSeeText('FAQ')
            ->assertSeeText('frequently asked questions');
    }

    /**
     * @test
     */
    public function faq_get_listed()
    {
        $faqs = factory(Faq::class, 5)->create();
        $response = $this->get(route('faq.index'));

        foreach ($faqs as $faq) {
            $response->assertSee($faq->question)
                ->assertSee($faq->answer);
        }
    }

    /**
     * @test
     */
    public function faq_create_page_available()
    {
        $this->get(route('faq.create'))
            ->assertSeeText('FAQ')
            ->assertSeeText('FAQ Details');
    }

    /**
     * @test
     */
    public function faq_gets_created()
    {
        $randomFaq = ['question' => microtime(), 'answer' => uniqid()];

        $this->post(route('faq.store'), $randomFaq);
        $this->assertDatabaseHas('faq', $randomFaq);
    }

    /**
     * @test
     */
    public function faq_edit_page_available_and_prefilled()
    {
        $faq = factory(Faq::class)->create();

        $this->get(route('faq.edit', $faq->id))
            ->assertSee(sprintf('value="%s"', $faq->question));
    }

    /**
     * @test
     */
    public function faq_gets_edited()
    {
        $randomFaq = ['question' => microtime(), 'answer' => uniqid()];
        $faq = factory(Faq::class)->create();

        $this->put(route('faq.update', $faq->id), $randomFaq);

        $this->assertDatabaseHas('faq', $randomFaq + ['id' => $faq->id]);
    }

    /**
     * @test
     */
    public function faq_gets_deleted()
    {
        $faq = factory(Faq::class)->create();

        $this->delete(route('faq.destroy', $faq->id));
        $this->assertDatabaseMissing('faq', ['id' => $faq->id]);
    }
}
