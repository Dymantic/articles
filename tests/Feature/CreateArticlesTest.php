<?php


namespace Dymantic\Articles\Test\Feature;


use Dymantic\Articles\Article;
use Dymantic\Articles\Test\TestCase;
use Illuminate\Support\Facades\Auth;

class CreateArticlesTest extends TestCase
{
    /**
     *@test
     */
    public function an_article_can_be_created()
    {
        $this->disableExceptionHandling();
        $article_data = [
            'title' => 'TEST TITLE',
            'description' => 'TEST DESCRIPTION',
            'intro' => 'TEST INTRO',
        ];

        $response = $this->asLoggedInUser()->json('POST', '/admin/articles', $article_data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('articles', $article_data);
    }

    /**
     *@test
     */
    public function the_article_is_created_in_the_name_of_the_authenticated_user()
    {
        $this->disableExceptionHandling();
        $article_data = [
            'title' => 'TEST TITLE',
            'description' => 'TEST DESCRIPTION',
            'intro' => 'TEST INTRO',
        ];

        $response = $this->asLoggedInUser()->json('POST', '/admin/articles', $article_data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('articles', array_merge(['author_id' => Auth::user()->id], $article_data));
    }

    /**
     *@test
     */
    public function an_article_requires_at_least_a_title_to_be_created()
    {
        $article_data = [
            'description' => 'TEST DESCRIPTION',
            'intro' => 'TEST INTRO',
        ];

        $response = $this->asLoggedInUser()->json('POST', '/admin/articles', $article_data);

        $response->assertStatus(422);
        $this->assertArrayHasKey('title', $response->json()['errors']);

        $this->assertDatabaseMissing('articles', $article_data);
    }

    /**
     *@test
     */
    public function creating_a_new_article_responds_with_the_fresh_data()
    {
        $this->disableExceptionHandling();
        $article_data = [
            'title' => 'TEST TITLE',
            'description' => 'TEST DESCRIPTION',
            'intro' => 'TEST INTRO',
        ];

        $response = $this->asLoggedInUser()->json('POST', '/admin/articles', $article_data);
        $response->assertStatus(200);

        $this->assertCount(1, Article::all());
        $this->assertEquals(Article::first()->toJsonableArray(), $response->json());
    }
}