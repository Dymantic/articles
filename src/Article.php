<?php


namespace Dymantic\Articles;


use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

class Article extends Model implements HasMediaConversions
{
    use HasMediaTrait, ConvertsImages, Publishable, Sluggable;

    const ARTICLE_IMAGES_COLLECTION = 'article_images';
    const TITLE_IMAGE_COLLECTION = 'title_images';
    const DEFAULT_TITLE_IMG = '/images/defaults/article.jpg';

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

    public function titleImage($conversion = '')
    {
        return $this->hasTitleImage() ? $this->getTitleImage()->getUrl($conversion) : static::DEFAULT_TITLE_IMG;
    }

    public function getTitleImage()
    {
        return $this->getMedia(static::TITLE_IMAGE_COLLECTION)->first();
    }

    public function hasTitleImage()
    {
        return $this->hasMedia(static::TITLE_IMAGE_COLLECTION);
    }

    public function isLive()
    {
        return !$this->is_draft && $this->published_on->lte(Carbon::today());
    }

    public function toJsonableArray()
    {
        return [
            'id'                     => $this->id,
            'title'                  => $this->title,
            'description'            => $this->description,
            'intro'                  => $this->intro,
            'body'                   => $this->body,
            'is_draft'               => $this->is_draft,
            'published_on'           => $this->published_on ? $this->published_on->format('Y-m-d') : null,
            'has_author'             => !!$this->author,
            'author_id'              => $this->author->id,
            'author_name'            => $this->author->name,
            'title_image'            => $this->titleImage(),
            'title_image_thumb'      => $this->titleImage('thumb'),
            'title_image_large_tile' => $this->titleImage('large_tile'),
            'title_image_banner'     => $this->titleImage('banner'),
        ];
    }


    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'title']
        ];
    }
}