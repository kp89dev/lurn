<?php

namespace Tests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoriesTest extends \AdminLoggedInTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function categories_page_available()
    {
        $this->get(route('categories.index'))
            ->assertSeeText('Categories')
            ->assertSeeText('Add New Category');
    }

    /**
     * @test
     */
    public function categories_get_listed()
    {
        $categories = factory(Category::class, 5)->create();
        $response = $this->get(route('categories.index'));

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }

    /**
     * @test
     */
    public function categories_create_page_available()
    {
        $this->get(route('categories.create'))
            ->assertSeeText('Categories')
            ->assertSeeText('Category Details');
    }

    /**
     * @test
     */
    public function category_gets_created()
    {
        $randomName = ['name' => 'testing 1, 2, 3' . microtime()];

        $this->post(route('categories.store'), $randomName);
        $this->assertDatabaseHas('categories', $randomName);
    }

    /**
     * @test
     */
    public function categories_edit_page_available_and_prefilled()
    {
        $category = factory(Category::class)->create();

        $this->get(route('categories.edit', $category->id))
            ->assertSee(sprintf('value="%s"', $category->name));
    }

    /**
     * @test
     */
    public function category_gets_edited()
    {
        $randomName = ['name' => 'testing 1, 2, 3' . microtime()];
        $category = factory(Category::class)->create();

        $this->put(route('categories.update', $category->id), $randomName);

        $this->assertDatabaseHas('categories', $randomName + ['id' => $category->id]);
    }

    /**
     * @test
     */
    public function category_gets_deleted()
    {
        $category = factory(Category::class)->create();

        $this->delete(route('categories.destroy', $category->id));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
