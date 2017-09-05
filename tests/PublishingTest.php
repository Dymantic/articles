<?php


namespace Dymantic\Articles\Test;


use Carbon\Carbon;
use Dymantic\Articles\Events\ArticleFirstPublished;
use Illuminate\Support\Facades\Event;

class PublishingTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function an_article_can_be_published()
    {
        $article = $this->createArticle();
        $this->assertNull($article->published_on);
        $this->assertTrue($article->is_draft);

        $article->publish();

        $this->assertTrue(Carbon::today()->isSameDay($article->fresh()->published_on));
        $this->assertFalse($article->fresh()->is_draft);
    }

    /**
     *@test
     */
    public function publishing_an_article_with_an_existing_published_on_date_does_not_change_the_published_on_date()
    {
        $article = $this->createArticle(['published_on' => Carbon::parse('-7 days')]);
        $this->assertTrue($article->is_draft);
        $this->assertNotNull($article->published_on);
        $original_publish_date = $article->published_on;

        $article->publish();

        $this->assertEquals($original_publish_date, $article->fresh()->published_on);
        $this->assertFalse($article->fresh()->is_draft);
    }

    /**
     *@test
     */
    public function passing_a_date_string_to_the_publish_method_will_publish_on_that_date()
    {
        $article = $this->createArticle();
        $this->assertNull($article->published_on);
        $this->assertTrue($article->is_draft);

        $article->publish(Carbon::parse('+10 days')->format('Y-m-d'));

        $this->assertTrue(Carbon::parse('+10 days')->isSameDay($article->fresh()->published_on));
        $this->assertFalse($article->fresh()->is_draft);
    }

    /**
     *@test
     */
    public function an_article_can_be_retracted()
    {
        $article = $this->createArticle(['published_on' => Carbon::parse('-4 days'), 'is_draft' => false]);
        $this->assertNotNull($article->published_on);
        $this->assertFalse($article->is_draft);

        $article->retract();

        $this->assertTrue(Carbon::parse('-4 days')->isSameDay($article->fresh()->published_on));
        $this->assertTrue($article->fresh()->is_draft);
    }

    /**
     *@test
     */
    public function an_event_is_fired_when_an_article_is_first_published()
    {
        Event::fake();

        $article = $this->createArticle(['published_on' => null, 'is_draft' => true]);
        $article->publish();

        Event::assertDispatched(ArticleFirstPublished::class, function($event) use ($article) {
            return $event->article->id === $article->id;
        });

    }

    /**
     *@test
     */
    public function republishing_a_previously_published_article_does_not_dispatch_the_first_published_event()
    {
        Event::fake();

        $article = $this->createArticle(['published_on' => Carbon::parse('-5 days'), 'is_draft' => true]);
        $article->publish();

        Event::assertNotDispatched(ArticleFirstPublished::class);
    }

    /**
     *@test
     */
    public function the_first_published_event_will_not_be_fired_if_the_article_is_being_published_in_the_future()
    {
        Event::fake();

        $article = $this->createArticle(['published_on' => null, 'is_draft' => true]);
        $article->publish(Carbon::parse('+3 days')->format('Y-m-d'));

        Event::assertNotDispatched(ArticleFirstPublished::class);
    }
}