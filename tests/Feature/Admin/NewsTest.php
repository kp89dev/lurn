<?php

namespace Tests\Admin;

use App\Models\News;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NewsTest extends \SuperAdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function news_page_available()
    {
        $this->get(route('news.index'))
            ->assertSeeText('News');
    }

    /**
     * @test
     */
    public function news_get_listed()
    {
        $news = factory(News::class, 5)->create();
        $response = $this->get(route('news.index'));

        foreach ($news as $article) {
            $response->assertSee($article->title);
        }
    }

    /**
     * @test
     */
    public function news_create_page_available()
    {
        $this->get(route('news.create'))
            ->assertSeeText('News')
            ->assertSeeText('News Details');
    }

    /**
     * @test
     */
    public function news_gets_created()
    {
        $news = factory(News::class)->create();

        $this->post(route('news.store'), $news = ['title' => $news->title, 'content' => $news->content]);
        $this->assertDatabaseHas('news', $news);
    }

    /**
     * @test
     */
    public function news_edit_page_available_and_prefilled()
    {
        $news = factory(News::class)->create();

        $this->get(route('news.edit', $news->id))
            ->assertSee(sprintf('value="%s"', $news->title));
    }

    /**
     * @test
     */
    public function news_gets_edited()
    {
        $news = factory(News::class)->create();
        $randomNews = ['title' => $news->title, 'content' => $news->content];
        $news = factory(News::class)->create();

        $this->put(route('news.update', $news->id), $randomNews);

        $this->assertDatabaseHas('news', $randomNews + ['id' => $news->id]);
    }

    /**
     * @test
     */
    public function news_gets_deleted()
    {
        $news = factory(News::class)->create();

        $this->delete(route('news.destroy', $news->id));
        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }
}
