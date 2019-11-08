<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\News;

class NewsTest extends \TestCase
{
    /**
     * @test
     */
    public function news_page_available()
    {
        $response = $this->get(route('news'));
        $response->assertStatus(200)
            ->assertSee('News');
    }
    
    /**
     * @test
     */
    public function news_is_listed_on_news_page()
    {
        $newsItems = factory(News::class, 5)->create();
    
        $response = $this->get(route('news'));
        
        $response->assertStatus(200);
        foreach($newsItems as $news) {
            $response->assertSee($news->title)
                ->assertSee($news->excerpt);
        }
    }
    
    /**
     * @test
     */
    public function news_detail_page_available()
    {
        $newsItem = factory(News::class)->create();
        
        $response = $this->get(route('news-article', ['news' => $newsItem->slug]));
        
        $response->assertStatus(200)
            ->assertSee($newsItem->title)
            ->assertSee($newsItem->content);
    }
    
    /**
     * @test
     */
    public function news_detail_show_other_news()
    {
        $newsItem = factory(News::class)->create();
        
        $otherNewsItems = factory(News::class, 3)->create();
        
        $response = $this->get(route('news-article', ['news' => $newsItem->slug]));
        
        $response->assertStatus(200);
        
        foreach($otherNewsItems as $news) {
            $response->assertSee($news->title);
        }
    }
}   
