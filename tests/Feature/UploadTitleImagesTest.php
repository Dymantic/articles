<?php


namespace Dymantic\Articles\Test\Feature;


use Dymantic\Articles\Test\MakesModels;
use Dymantic\Articles\Test\TestCase;
use Illuminate\Http\UploadedFile;

class UploadTitleImagesTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function a_title_image_can_be_posted_to_an_article()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle();

        $response = $this->asLoggedInUser()->post("/admin/articles/{$article->id}/title-images", [
            'image' => UploadedFile::fake()->image('testpic.jpg')
        ]);
        $response->assertStatus(200);

        $this->assertTrue($article->fresh()->hasTitleImage());
    }

    /**
     *@test
     */
    public function an_image_is_required_to_set_the_title_image()
    {
        $article = $this->createArticle();

        $response = $this->asLoggedInUser()->json('post', "/admin/articles/{$article->id}/title-images", [
            'image' => ''
        ]);
        $response->assertStatus(422);
        $this->assertArrayHasKey('image', $response->decodeResponseJson()['errors']);

        $this->assertFalse($article->fresh()->hasTitleImage());
    }

    /**
     *@test
     */
    public function the_image_must_be_an_actual_image_to_be_valid()
    {
        $article = $this->createArticle();

        $response = $this->asLoggedInUser()->json('post', "/admin/articles/{$article->id}/title-images", [
            'image' => 'NOT-AN-IMAGE'
        ]);
        $response->assertStatus(422);
        $this->assertArrayHasKey('image', $response->decodeResponseJson()['errors']);

        $this->assertFalse($article->fresh()->hasTitleImage());
    }

    /**
     *@test
     */
    public function an_articles_title_image_can_be_cleared()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle();
        $article->setTitleImage(UploadedFile::fake()->image('testpic.png'));
        $this->assertTrue($article->fresh()->hasTitleImage());

        $response = $this->asLoggedInUser()->json('DELETE', "/admin/articles/{$article->id}/title-images");
        $response->assertStatus(200);


        $this->assertFalse($article->fresh()->hasTitleImage());
    }
}