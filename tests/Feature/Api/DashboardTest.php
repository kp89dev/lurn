<?php

namespace Feature\Classroom;

use App\Models\News;
use App\Models\UserSetting;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DashboardTest extends \LoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function message_gets_hidden()
    {
        $this->post(url('api/hide-message', 'test123'));

        self::assertTrue(
            (bool) UserSetting::whereUserId($this->user->id)->whereMessages('{"show-test123":false}')->first()
        );
    }

    /**
     * @test
     */
    public function news_get_displayed()
    {
        $news = factory(News::class, 5)->create();

        $this->get(url('api/unread-news'))
            ->assertExactJson($news->toArray());
    }

    /**
     * @test
     */
    public function news_get_marked_unread()
    {
        $news = factory(News::class, 5)->create();

        $this->post(url('api/mark-news-read'), ['ids' => ''])
            ->assertExactJson(['success' => false]);

        $this->post(url('api/mark-news-read'), ['ids' => $news->pluck('id')->toArray()])
            ->assertExactJson(['success' => null]);
    }
}