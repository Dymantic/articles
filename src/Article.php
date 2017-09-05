<?php


namespace Dymantic\Articles;


use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

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

    public function toJsonableArray()
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'intro'        => $this->intro,
            'body'         => $this->body,
            'is_draft'     => $this->is_draft,
            'published_on' => $this->published_on ? $this->published_on->format('Y-m-d') : null,
            'has_author'   => !! $this->author,
            'author_id'    => $this->author->id,
            'author_name'  => $this->author->name
        ];
    }

}