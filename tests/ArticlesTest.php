<?php


namespace Dymantic\Articles\Test;


use Carbon\Carbon;
use Dymantic\Articles\Article;

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

        $expected = [
            'id'           => $article->id,
            'title'        => 'TEST TITLE',
            'description'  => 'TEST DESCRIPTION',
            'intro'        => 'TEST INTRO',
            'body'         => 'TEST BODY',
            'is_draft'     => false,
            'published_on' => Carbon::today()->format('Y-m-d'),
            'has_author'   => true,
            'author_id'    => $article->author->id,
            'author_name'  => 'TEST USER'
        ];

        $this->assertEquals($expected, $article->toJsonableArray());
    }
}