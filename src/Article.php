<?php


namespace Dymantic\Articles;


use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Article extends Model implements HasMedia
{
    use HasMediaTrait, Publishable, Sluggable;

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

    public function clearTitleImage()
    {
        $this->clearMediaCollection(static::TITLE_IMAGE_COLLECTION);
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
        return !$this->is_draft && $this->published_on->startOfDay()->lte(Carbon::today());
    }

    public function publishedStatus()
    {
        if ($this->is_draft) {
            return 'Draft';
        }

        if ($this->isLive()) {
            return 'Published on ' . $this->published_on->toFormattedDateString();
        }

        return 'Will be published on ' . $this->published_on->toFormattedDateString();
    }

    public function toJsonableArray()
    {
        return [
            'id'                     => $this->id,
            'title'                  => $this->title,
            'slug'                   => $this->slug,
            'description'            => $this->description,
            'intro'                  => $this->intro,
            'body'                   => $this->body,
            'is_draft'               => $this->is_draft,
            'published_on'           => $this->published_on ? $this->published_on->format('Y-m-d') : null,
            'published_status'       => $this->publishedStatus(),
            'has_author'             => !!$this->author,
            'author_id'              => $this->author->id ?? null,
            'author_name'            => $this->author->name ?? null,
            'title_image'            => $this->titleImage(),
            'title_image_thumb'      => $this->titleImage('thumb'),
            'title_image_large_tile' => $this->titleImage('large_tile'),
            'title_image_banner'     => $this->titleImage('banner'),
            'created_at'             => $this->created_at->format('Y-m-d'),
            'updated_at'             => $this->updated_at->format('Y-m-d')
        ];
    }


    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'title']
        ];
    }

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
             ->fit(Manipulations::FIT_CROP, 400, 300)
             ->keepOriginalImageFormat()
             ->optimize();

        $this->addMediaConversion('web')
             ->fit(Manipulations::FIT_MAX, 800, 500)
             ->keepOriginalImageFormat()
             ->performOnCollections(static::ARTICLE_IMAGES_COLLECTION)
             ->optimize();

        $this->addMediaConversion('large_tile')
             ->fit(Manipulations::FIT_CROP, 800, 400)
             ->keepOriginalImageFormat()
             ->performOnCollections(static::TITLE_IMAGE_COLLECTION)
             ->optimize();

        $this->addMediaConversion('banner')
             ->fit(Manipulations::FIT_CROP, 1200, 400)
             ->keepOriginalImageFormat()
             ->performOnCollections(static::TITLE_IMAGE_COLLECTION)
             ->optimize();
    }
}