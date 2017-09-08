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
            'description'            => 'TEST DESCRIPTION',
            'intro'                  => 'TEST INTRO',
            'body'                   => 'TEST BODY',
            'is_draft'               => false,
            'published_on'           => Carbon::today()->format('Y-m-d'),
            'has_author'             => true,
            'author_id'              => $article->author->id,
            'author_name'            => 'TEST USER',
            'title_image_banner'     => $image->getUrl('banner'),
            'title_image_thumb'      => $image->getUrl('thumb'),
            'title_image_large_tile' => $image->getUrl('large_tile'),
            'title_image'            => $image->getUrl()
        ];

        $this->assertEquals($expected, $article->toJsonableArray());
    }
}