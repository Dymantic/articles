<?php


namespace Dymantic\Articles\Test;


use Carbon\Carbon;
use Dymantic\Articles\Article;

class ArticleScopesTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function it_has_a_published_scope_that_scopes_to_only_published_articles()
    {
        $draft = $this->createArticle(['is_draft' => true]);
        $published = $this->createArticle(['published_on' => Carbon::parse('-5 days'), 'is_draft' => false]);

        $scoped = Article::published()->get();

        $this->assertTrue($scoped->contains($published));
        $this->assertFalse($scoped->contains($draft));
    }

    /**
     *@test
     */
    public function articles_that_have_a_future_published_on_date_are_not_scoped_as_published_even_if_not_draft()
    {
        $past = $this->createArticle(['published_on' => Carbon::parse('-5 days'), 'is_draft' => false]);
        $future = $this->createArticle(['published_on' => Carbon::parse('+5 days'), 'is_draft' => false]);

        $scoped = Article::published()->get();

        $this->assertTrue($scoped->contains($past));
        $this->assertFalse($scoped->contains($future));
    }

    /**
     *@test
     */
    public function articles_can_be_scoped_to_drafts()
    {
        $draft = $this->createArticle(['is_draft' => true]);
        $published = $this->createArticle(['published_on' => Carbon::parse('-5 days'), 'is_draft' => false]);

        $scoped = Article::drafts()->get();

        $this->assertTrue($scoped->contains($draft));
        $this->assertFalse($scoped->contains($published));
    }
}