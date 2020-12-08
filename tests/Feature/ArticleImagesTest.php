<?php


namespace Dymantic\Articles\Test\Feature;


use Dymantic\Articles\Article;
use Dymantic\Articles\Test\MakesModels;
use Dymantic\Articles\Test\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleImagesTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function an_image_can_be_posted_to_an_article()
    {
        Storage::fake('media');
        $this->disableExceptionHandling();
        $article = $this->createArticle();

        $response = $this->asLoggedInUser()->post("/admin/articles/{$article->id}/images", [
            'image' => UploadedFile::fake()->image('testpic.png')
        ]);
        $response->assertStatus(200);

        $this->assertTrue($article->fresh()->hasMedia(Article::ARTICLE_IMAGES_COLLECTION));

        $this->assertArrayHasKey('location', $response->json());
        $this->assertArrayHasKey('url', $response->json());
        $this->assertArrayHasKey('href', $response->json());

        Storage::disk('media')->assertExists(Str::after($response->json('location'), "storage"));
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

        $this->assertArrayHasKey('image', $response->json()['errors']);
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

        $this->assertArrayHasKey('image', $response->json()['errors']);
    }
}