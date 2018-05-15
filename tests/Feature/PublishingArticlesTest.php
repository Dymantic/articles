<?php


namespace Dymantic\Articles\Test\Feature;


use Carbon\Carbon;
use Dymantic\Articles\Test\MakesModels;
use Dymantic\Articles\Test\TestCase;

class PublishingArticlesTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function an_article_can_be_published()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle(['is_draft' => true, 'published_on' => null]);

        $response = $this->asLoggedInUser()->json('POST', "/admin/published-articles", [
            'article_id' => $article->id
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'is_draft' => false
        ]);
        $this->assertNotNull($article->fresh()->published_on);
    }

    /**
     *@test
     */
    public function a_valid_article_id_is_required_to_publish()
    {
        $response = $this->asLoggedInUser()->json('POST', "/admin/published-articles", [
            'article_id' => 7
        ]);
        $response->assertStatus(422);

        $this->assertArrayHasKey('article_id', $response->decodeResponseJson()['errors']);
    }

    /**
     *@test
     */
    public function an_article_can_be_published_with_a_specific_date()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle(['is_draft' => true, 'published_on' => null]);

        $response = $this->asLoggedInUser()->json('POST', "/admin/published-articles", [
            'article_id' => $article->id,
            'publish_date' => Carbon::parse('+5 days')->format('Y-m-d\Th:i:s') . '.000Z'
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'is_draft' => false
        ]);

        $this->assertTrue($article->fresh()->published_on->isSameDay(Carbon::parse('+5 days')));
    }

    /**
     *@test
     */
    public function publishing_an_article_responds_with_the_fresh_article_data()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle(['is_draft' => true, 'published_on' => null]);

        $response = $this->asLoggedInUser()->json('POST', "/admin/published-articles", [
            'article_id' => $article->id,
            'publish_date' => Carbon::parse('+5 days')->format('Y-m-d\Th:i:s') . '.000Z'
        ]);
        $response->assertStatus(200);

        $this->assertEquals($article->fresh()->toJsonableArray(), $response->decodeResponseJson());
    }

    /**
     *@test
     */
    public function a_valid_date_string_is_required_if_publishing_for_a_specific_date()
    {
        $article = $this->createArticle(['is_draft' => true, 'published_on' => null]);

        $response = $this->asLoggedInUser()->json('POST', "/admin/published-articles", [
            'article_id' => $article->id,
            'publish_date' => 'INVALID-DATE-STRING'
        ]);
        $response->assertStatus(422);

        $this->assertArrayHasKey('publish_date', $response->decodeResponseJson()['errors']);
    }

    /**
     *@test
     */
    public function an_article_can_be_retracted()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle(['is_draft' => false, 'published_on' => Carbon::parse('-5 days')]);

        $response = $this->asLoggedInUser()->json('DELETE', "/admin/published-articles/{$article->id}");
        $response->assertStatus(200);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'is_draft' => true
        ]);
        $this->assertNotNull($article->fresh()->published_on);
    }
}