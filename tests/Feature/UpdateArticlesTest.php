<?php


namespace Dymantic\Articles\Test\Feature;


use Dymantic\Articles\Test\MakesModels;
use Dymantic\Articles\Test\TestCase;

class UpdateArticlesTest extends TestCase
{
    use MakesModels;

    /**
     * @test
     */
    public function an_existing_article_may_be_updated()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle([
            'title'       => 'OLD TITLE',
            'intro'       => 'OLD INTRO',
            'description' => 'OLD DESCRIPTION'
        ]);
        $new_attributes = [
            'title'       => 'NEW TITLE',
            'intro'       => 'NEW INTRO',
            'description' => 'NEW DESCRIPTION'
        ];

        $response = $this->asLoggedInUser()->json('POST', "/admin/articles/{$article->id}", $new_attributes);
        $response->assertStatus(200);

        $this->assertDatabaseHas('articles', array_merge(['id' => $article->id], $new_attributes));
    }

    /**
     *@test
     */
    public function updating_an_article_requires_at_least_a_title()
    {
        $old_attributes = [
            'title'       => 'OLD TITLE',
            'intro'       => 'OLD INTRO',
            'description' => 'OLD DESCRIPTION'
        ];
        $article = $this->createArticle($old_attributes);
        $new_attributes = [
            'title'       => '',
            'intro'       => 'NEW INTRO',
            'description' => 'NEW DESCRIPTION'
        ];

        $response = $this->asLoggedInUser()->json('POST', "/admin/articles/{$article->id}", $new_attributes);
        $response->assertStatus(422);
        $this->assertArrayHasKey('title', $response->decodeResponseJson()['errors']);

        $this->assertDatabaseHas('articles', array_merge(['id' => $article->id], $old_attributes));
    }

    /**
     *@test
     */
    public function successfully_updating_an_article_responds_with_the_updated_data()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle([
            'title'       => 'OLD TITLE',
            'intro'       => 'OLD INTRO',
            'description' => 'OLD DESCRIPTION'
        ]);
        $new_attributes = [
            'title'       => 'NEW TITLE',
            'intro'       => 'NEW INTRO',
            'description' => 'NEW DESCRIPTION'
        ];

        $response = $this->asLoggedInUser()->json('POST', "/admin/articles/{$article->id}", $new_attributes);
        $response->assertStatus(200);

        $this->assertEquals($article->fresh()->toJsonableArray(), $response->decodeResponseJson());
    }

    /**
     *@test
     */
    public function an_articles_body_may_be_updated()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle(['body' => 'OLD BODY']);
        $response = $this->asLoggedInUser()->json('POST', "/admin/articles/{$article->id}/body", [
            'body' => 'NEW BODY'
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'body' => 'NEW BODY'
        ]);
    }

    /**
     *@test
     */
    public function updating_an_article_successfully_responds_with_the_updated_body()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle(['body' => 'OLD BODY']);
        $response = $this->asLoggedInUser()->json('POST', "/admin/articles/{$article->id}/body", [
            'body' => 'NEW BODY'
        ]);
        $response->assertStatus(200);
        $this->assertEquals(['body' => 'NEW BODY'], $response->decodeResponseJson());
    }
}