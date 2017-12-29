<?php


namespace Dymantic\Articles\Test\Feature;


use Dymantic\Articles\Article;
use Dymantic\Articles\Test\MakesModels;
use Dymantic\Articles\Test\TestCase;
use Illuminate\Http\UploadedFile;

class ArticleImagesTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function an_image_can_be_posted_to_an_article()
    {
        $this->disableExceptionHandling();
        $article = $this->createArticle();

        $response = $this->asLoggedInUser()->post("/admin/articles/{$article->id}/images", [
            'image' => UploadedFile::fake()->image('testpic.png')
        ]);
        $response->assertStatus(200);

        $this->assertTrue($article->fresh()->hasMedia(Article::ARTICLE_IMAGES_COLLECTION));

        $this->assertArrayHasKey('location', $response->decodeResponseJson());
        $this->assertArrayHasKey('url', $response->decodeResponseJson());
        $this->assertArrayHasKey('href', $response->decodeResponseJson());

        $this->assertTrue(file_exists(__DIR__ . '/../temp' .$response->decodeResponseJson()['location']));
    }

    /**
     *@test
     */
    public function an_image_is_required()
    {
        $article = $this->createArticle();

        $response = $this->asLoggedInUser()->json('POST', "/admin/articles/{$article->id}/images", [
            'image' => ''
        ]);
        $response->assertStatus(422);

        $this->assertArrayHasKey('image', $response->decodeResponseJson()['errors']);
    }

    /**
     *@test
     */
    public function a_valid_image_file_is_required()
    {
        $article = $this->createArticle();

        $response = $this->asLoggedInUser()->json('POST', "/admin/articles/{$article->id}/images", [
            'image' => UploadedFile::fake()->create('image.pdf')
        ]);
        $response->assertStatus(422);

        $this->assertArrayHasKey('image', $response->decodeResponseJson()['errors']);
    }
}