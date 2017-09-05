<?php


namespace Dymantic\Articles\Test;


use Illuminate\Http\UploadedFile;

class ArticleImagesTest extends TestCase
{
    use MakesModels;
    /**
     *@test
     */
    public function an_image_can_be_added_to_an_article()
    {
        $article = $this->createArticle();

        $image = $article->addImage(UploadedFile::fake()->image('testpic.jpg'));

        $this->assertTrue(file_exists($image->getPath()));

        $this->assertCount(1, $article->getArticleImages());
    }

    /**
     *@test
     */
    public function an_image_has_a_thumbnail_conversion()
    {
        $article = $this->createArticle();

        $article->addImage(UploadedFile::fake()->image('testpic.jpg'));

        $image = $article->getArticleImages()->first();

        $this->assertTrue(file_exists($image->getPath('thumb')));
        $this->assertNotEquals($image->getPath(), $image->getPath('thumb'));
    }

    /**
     *@test
     */
    public function a_thumbnail_conversion_keeps_the_same_extension()
    {
        $article = $this->createArticle();
        $article->addImage(UploadedFile::fake()->image('testpic.png'));

        $image = $article->getArticleImages()->first();

        $this->assertEquals('.png', substr($image->getPath('thumb'), -4));
    }

    /**
     *@test
     */
    public function an_image_has_a_web_conversion()
    {
        $article = $this->createArticle();

        $article->addImage(UploadedFile::fake()->image('testpic.jpg'));

        $image = $article->getArticleImages()->first();

        $this->assertTrue(file_exists($image->getPath('web')));
        $this->assertNotEquals($image->getPath(), $image->getPath('web'));
    }

    /**
     *@test
     */
    public function a_web_conversion_keeps_the_same_extension()
    {
        $article = $this->createArticle();
        $article->addImage(UploadedFile::fake()->image('testpic.png'));

        $image = $article->getArticleImages()->first();

        $this->assertEquals('.png', substr($image->getPath('web'), -4));
    }

}