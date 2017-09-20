<?php


namespace Dymantic\Articles;


use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\Media;

trait ConvertsImages
{
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