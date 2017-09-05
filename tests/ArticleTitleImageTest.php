<?php


namespace Dymantic\Articles\Test;


use Dymantic\Articles\Article;
use Illuminate\Http\UploadedFile;

class ArticleTitleImageTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function an_article_can_have_a_title_image_set()
    {
        $article = $this->createArticle();

        $article->setTitleImage(UploadedFile::fake()->image('testpic.jpg'));

        $this->assertContains('testpic.jpg', $article->getTitleImage()->getUrl());
    }

    /**
     *@test
     */
    public function adding_a_title_image_overwrites_the_previous_one()
    {
        $article = $this->createArticle();
        $first = $article->setTitleImage(UploadedFile::fake()->image('first_testpic.jpg'));
        $second = $article->setTitleImage(UploadedFile::fake()->image('second_testpic.jpg'));

        $this->assertFalse(file_exists($first->getPath()));
        $this->assertTrue(file_exists($second->getPath()));

        $this->assertContains('second_testpic', $article->fresh()->getTitleImage()->getPath());
        $this->assertCount(1, $article->getMedia(Article::TITLE_IMAGE_COLLECTION));
    }

    /**
     *@test
     */
    public function a_title_image_has_a_thumbnail_conversion()
    {
        $article = $this->createArticle();
        $article->setTitleImage(UploadedFile::fake()->image('testpic.jpg'));

        $image = $article->getTitleImage();
        $this->assertTrue(file_exists($image->getPath('thumb')));

        $this->assertNotEquals($image->getPath(), $image->getPath('thumb'));
    }

    /**
     *@test
     */
    public function a_title_image_has_a_large_tile_conversion()
    {
        $article = $this->createArticle();
        $article->setTitleImage(UploadedFile::fake()->image('testpic.jpg'));

        $image = $article->getTitleImage();
        $this->assertTrue(file_exists($image->getPath('large_tile')));

        $this->assertNotEquals($image->getPath(), $image->getPath('large_tile'));
    }

    /**
     *@test
     */
    public function a_title_image_has_a_banner_conversion()
    {
        $article = $this->createArticle();
        $article->setTitleImage(UploadedFile::fake()->image('testpic.jpg'));

        $image = $article->getTitleImage();
        $this->assertTrue(file_exists($image->getPath('banner')));

        $this->assertNotEquals($image->getPath(), $image->getPath('banner'));
    }
}