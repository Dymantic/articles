<?php

namespace Dymantic\Articles\Test;

use Dymantic\Articles\AuthorsArticles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class TestUserModel extends User
{
    use AuthorsArticles;

    protected $table = 'test_users';
    protected $guarded = [];
    public $timestamps = false;



}