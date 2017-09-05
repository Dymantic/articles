<?php

namespace Dymantic\Articles\Events;

use Dymantic\Articles\Article;
use Illuminate\Queue\SerializesModels;

class ArticleFirstPublished
{
    use SerializesModels;

    public $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }
}