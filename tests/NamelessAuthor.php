<?php


namespace Dymantic\Articles\Test;


use Dymantic\Articles\AuthorsArticles;
use Illuminate\Database\Eloquent\Model;

class NamelessAuthor extends Model
{
    use AuthorsArticles;

    protected $table = 'nameless_authors';
    public $timestamps = false;

}