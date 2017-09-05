<?php


namespace Dymantic\Articles;


use Carbon\Carbon;
use Dymantic\Articles\Events\ArticleFirstPublished;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Spatie\MediaLibrary\Media;

class Article extends Model implements HasMediaConversions
{
    use HasMediaTrait, ConvertsImages, Publishable;
    
    const ARTICLE_IMAGES_COLLECTION = 'article_images';
    const TITLE_IMAGE_COLLECTION = 'title_images';

    protected $table = 'articles';

    protected $fillable = ['title', 'description', 'intro', 'body'];

    protected $casts = ['is_draft' => 'boolean'];

    protected $dates = ['published_on'];

    public function author()
    {
        return $this->morphTo();
    }

    public function setBody($body)
    {
        $this->update(['body' => $body]);
    }

    public function addImage($file)
    {
        return $this->addMedia($file)->toMediaCollection(static::ARTICLE_IMAGES_COLLECTION);
    }

    public function getArticleImages()
    {
        return $this->getMedia(static::ARTICLE_IMAGES_COLLECTION);
    }

    public function setTitleImage($file)
    {
        $this->clearMediaCollection(static::TITLE_IMAGE_COLLECTION);
        return $this->addMedia($file)->toMediaCollection(static::TITLE_IMAGE_COLLECTION);
    }

    public function getTitleImage()
    {
        return $this->getMedia(static::TITLE_IMAGE_COLLECTION)->first();
    }

}