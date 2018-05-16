<?php


namespace Dymantic\Articles\Test;


use Carbon\Carbon;
use Dymantic\Articles\Article;
use Illuminate\Http\UploadedFile;

class ArticlesTest extends TestCase
{
    use MakesModels;

    /**
     * @test
     */
    public function an_article_can_be_created_with_only_a_title()
    {
        $article = Article::create([
            'title' => 'TEST TITLE',
        ]);

        $this->assertNotNull($article->id);
        $this->assertEquals('TEST TITLE', $article->title);
        $this->assertNull($article->fresh()->description);
        $this->assertNull($article->fresh()->intro);
        $this->assertNull($article->fresh()->body);
        $this->assertTrue($article->fresh()->is_draft);
    }

    /**
     * @test
     */
    public function a_new_article_defaults_to_be_a_draft()
    {
        $article = Article::create([
            'title' => 'TEST TITLE',
        ]);

        $this->assertTrue($article->fresh()->is_draft);
    }

    /**
     * @test
     */
    public function an_article_can_be_created_with_all_attributes()
    {
        $article = Article::create([
            'title'       => 'TEST TITLE',
            'description' => 'TEST DESCRIPTION',
            'intro'       => 'TEST INTRO',
            'body'        => 'TEST BODY'
        ]);

        $this->assertEquals('TEST TITLE', $article->title);
        $this->assertEquals('TEST DESCRIPTION', $article->description);
        $this->assertEquals('TEST INTRO', $article->intro);
        $this->assertEquals('TEST BODY', $article->body);
    }

    /**
     * @test
     */
    public function the_body_of_an_article_can_be_set()
    {
        $article = $this->createArticle();

        $article->setBody('A new and updated body');

        $this->assertEquals('A new and updated body', $article->fresh()->body);
    }

    /**
     * @test
     */
    public function an_article_that_is_published_and_has_a_published_on_date_in_the_past_is_live()
    {
        $article = $this->createArticle(['is_draft' => false, 'published_on' => Carbon::parse('-3 days')]);

        $this->assertTrue($article->isLive());
    }

    /**
     * @test
     */
    public function an_article_that_is_a_draft_is_never_live()
    {
        $past = $this->createArticle(['is_draft' => true, 'published_on' => Carbon::parse('-5 days')]);
        $future = $this->createArticle(['is_draft' => true, 'published_on' => Carbon::parse('+5 days')]);
        $fresh = $this->createArticle(['is_draft' => true, 'published_on' => null]);

        $this->assertFalse($past->isLive());
        $this->assertFalse($future->isLive());
        $this->assertFalse($fresh->isLive());
    }

    /**
     * @test
     */
    public function an_article_that_is_not_a_draft_but_the_published_on_date_is_in_the_future_is_not_live()
    {
        $article = $this->createArticle(['is_draft' => false, 'published_on' => Carbon::parse('+3 days')]);

        $this->assertFalse($article->isLive());
    }

    /**
     * @test
     */
    public function an_article_may_be_presented_as_a_jsonable_array()
    {
        $user = $this->createTestUser(['name' => 'TEST USER']);
        $article = $user->postArticle([
            'title'       => 'TEST TITLE',
            'description' => 'TEST DESCRIPTION',
            'intro'       => 'TEST INTRO',
            'body'        => 'TEST BODY'
        ]);
        $article->publish();
        $image = $article->setTitleImage(UploadedFile::fake()->image('testpic.jpg'));

        $expected = [
            'id'                     => $article->id,
            'title'                  => 'TEST TITLE',
            'slug'                   => 'test-title',
            'description'            => 'TEST DESCRIPTION',
            'intro'                  => 'TEST INTRO',
            'body'                   => 'TEST BODY',
            'is_draft'               => false,
            'published_on'           => Carbon::today()->format('Y-m-d'),
            'published_status'       => 'Published on ' . Carbon::today()->toFormattedDateString(),
            'has_author'             => true,
            'author_id'              => $article->author->id,
            'author_name'            => 'TEST USER',
            'title_image_banner'     => $image->getUrl('banner'),
            'title_image_thumb'      => $image->getUrl('thumb'),
            'title_image_large_tile' => $image->getUrl('large_tile'),
            'title_image'            => $image->getUrl(),
            'created_at'             => Carbon::today()->format('Y-m-d'),
            'updated_at'             => Carbon::today()->format('Y-m-d')
        ];

        $this->assertEquals($expected, $article->toJsonableArray());
    }

    /**
     *@test
     */
    public function an_article_without_an_author_can_still_be_converted_to_a_jsonable_array()
    {
        $article = $this->createArticle([
            'title'       => 'TEST TITLE',
            'description' => 'TEST DESCRIPTION',
            'intro'       => 'TEST INTRO',
            'body'        => 'TEST BODY'
        ]);
        $article->publish();
        $image = $article->setTitleImage(UploadedFile::fake()->image('testpic.jpg'));

        $expected = [
            'id'                     => $article->id,
            'title'                  => 'TEST TITLE',
            'slug'                   => 'test-title',
            'description'            => 'TEST DESCRIPTION',
            'intro'                  => 'TEST INTRO',
            'body'                   => 'TEST BODY',
            'is_draft'               => false,
            'published_on'           => Carbon::today()->format('Y-m-d'),
            'published_status'       => 'Published on ' . Carbon::today()->toFormattedDateString(),
            'has_author'             => false,
            'author_id'              => null,
            'author_name'            => null,
            'title_image_banner'     => $image->getUrl('banner'),
            'title_image_thumb'      => $image->getUrl('thumb'),
            'title_image_large_tile' => $image->getUrl('large_tile'),
            'title_image'            => $image->getUrl(),
            'created_at'             => Carbon::today()->format('Y-m-d'),
            'updated_at'             => Carbon::today()->format('Y-m-d')
        ];

        $this->assertEquals($expected, $article->toJsonableArray());
    }

    /**
     * @test
     */
    public function a_draft_article_has_a_published_state_of_draft()
    {
        $article = $this->createArticle(['is_draft' => true, 'published_on' => null]);

        $this->assertEquals('Draft', $article->publishedStatus());
    }

    /**
     * @test
     */
    public function a_published_articles_with_a_past_published_on_date_has_a_published_state()
    {
        $article = $this->createArticle(['is_draft' => false, 'published_on' => Carbon::parse('-3 days')]);

        $expected = 'Published on ' . Carbon::parse('-3 days')->toFormattedDateString();

        $this->assertEquals($expected, $article->publishedStatus());
    }

    /**
     * @test
     */
    public function a_published_article_with_a_future_published_on_date_has_published_state()
    {
        $article = $this->createArticle(['is_draft' => false, 'published_on' => Carbon::parse('+3 days')]);

        $expected = 'Will be published on ' . Carbon::parse('+3 days')->toFormattedDateString();

        $this->assertEquals($expected, $article->publishedStatus());
    }
}