<?php


namespace Dymantic\Articles;


trait AuthorsArticles
{
    public function articles()
    {
        return $this->morphMany(Article::class, 'author');
    }

    public function postArticle($article_attributes)
    {
        return $this->articles()->create($article_attributes);
    }

    public function getAuthorName()
    {
        return $this->name ?? 'Anonymous';
    }
}