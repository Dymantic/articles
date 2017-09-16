<?php


namespace Dymantic\Articles\Controllers;


use Dymantic\Articles\Article;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class ArticlesBodyController extends Controller
{
    use ValidatesRequests, DispatchesJobs, AuthorizesRequests;

    public function show($article)
    {
        $article = Article::findOrFail($article);

        return view('admin.articles.body.show', ['article' => $article]);
    }

    public function update($article)
    {
        $article = Article::findOrFail($article);

        $article->setBody(request('body'));

        return ['body' => $article->fresh()->body];
    }
}