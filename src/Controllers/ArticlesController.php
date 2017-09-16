<?php

namespace Dymantic\Articles\Controllers;

use Dymantic\Articles\Article;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class ArticlesController extends Controller
{
    use AuthorizesRequests, ValidatesRequests, DispatchesJobs;

    public function store()
    {
        request()->validate(['title' => 'required']);

        request()->user()->postArticle(request()->all());

    }

    public function update($article)
    {
        request()->validate(['title' => 'required']);

        $article = Article::findOrFail($article);

        $article->update(request()->all());

        return $article->fresh()->toJsonableArray();
    }

    public function delete($article)
    {
        $article = Article::findOrFail($article);

        $article->delete();

        return redirect('/admin/articles');
    }


}