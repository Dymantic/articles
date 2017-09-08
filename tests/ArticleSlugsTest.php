<?php


namespace Dymantic\Articles\Test;


class ArticleSlugsTest extends TestCase
{
    use MakesModels;
    /**
     *@test
     */
    public function a_created_article_has_a_slug()
    {
        $article = $this->createArticle(['title' => 'A Test Article']);

        $this->assertEquals('a-test-article', $article->slug);
    }
}