<?php


namespace Dymantic\Articles\Test;


class AuthoringTest extends TestCase
{
    use MakesModels;
    /**
     *@test
     */
    public function an_model_with_an_authors_articles_trait_can_create_posts_belonging_to_them()
    {
        $user = $this->createTestUser();

        $article_attributes = [
            'title' => 'THE USERS TITLE',
            'description' => 'AN ARTICLE BY A USER'
        ];

        $article = $user->postArticle($article_attributes);

        $this->assertTrue($user->articles->first()->is($article));
    }

    /**
     *@test
     */
    public function an_article_posted_by_an_author_knows_of_its_author()
    {
        $user = $this->createTestUser();

        $article_attributes = [
            'title' => 'THE USERS TITLE',
            'description' => 'AN ARTICLE BY A USER'
        ];

        $article = $user->postArticle($article_attributes);

        $this->assertTrue($article->author->is($user));
    }

    /**
     *@test
     */
    public function an_article_can_always_get_its_authors_name()
    {
        $user = $this->createTestUser(['name' => 'TEST AUTHOR']);

        $article_attributes = [
            'title' => 'THE USERS TITLE',
            'description' => 'AN ARTICLE BY A USER'
        ];

        $article = $user->postArticle($article_attributes);

        $this->assertEquals('TEST AUTHOR', $article->author->getAuthorName());
    }

    /**
     *@test
     */
    public function the_author_can_get_a_default_name_if_it_is_unnamed()
    {
        $author = $this->createUnnamedAuthor();

        $article_attributes = [
            'title' => 'THE USERS TITLE',
            'description' => 'AN ARTICLE BY A USER'
        ];

        $article = $author->postArticle($article_attributes);

        $this->assertEquals('Anonymous', $article->author->getAuthorName());
    }
}