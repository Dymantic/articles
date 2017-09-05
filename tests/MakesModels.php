<?php


namespace Dymantic\Articles\Test;


use Dymantic\Articles\Article;

trait MakesModels
{
    public function createArticle($attributes = []) : Article
    {
        $defaults = [
            'title'       => 'TEST TITLE',
            'intro'       => 'TEST INTRO',
            'description' => 'TEST DESCRIPTION',
            'body'        => 'TEST BODY'
        ];

        return Article::forceCreate(array_merge($defaults, $attributes))->fresh();
    }

    public function createTestUser($attributes = []) : TestUserModel
    {
        $defaults = ['name' => 'TEST USER'];

        return TestUserModel::forceCreate(array_merge($defaults, $attributes))->fresh();
    }

    public function createUnnamedAuthor()
    {
        return NamelessAuthor::forceCreate([]);
    }
}