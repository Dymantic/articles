<?php


namespace Dymantic\Articles\Test\Feature;


use Dymantic\Articles\Test\MakesModels;
use Dymantic\Articles\Test\TestCase;

class DeletingArticlesTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function an_article_can_be_deleted()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle();

        $response = $this->asLoggedInUser()->delete("/admin/articles/{$article->id}");
        $response->assertStatus(302);
        $response->assertRedirect('/admin/articles');

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }
}