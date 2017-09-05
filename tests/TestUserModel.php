<?php

namespace Dymantic\Articles\Test;

use Dymantic\Articles\AuthorsArticles;
use Illuminate\Database\Eloquent\Model;

class TestUserModel extends Model
{
    use AuthorsArticles;

    protected $table = 'test_users';
    protected $guarded = [];
    public $timestamps = false;



}