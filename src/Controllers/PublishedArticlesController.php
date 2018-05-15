<?php


namespace Dymantic\Articles\Controllers;


use Dymantic\Articles\Article;
use Dymantic\Articles\ParsableDate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class PublishedArticlesController extends Controller
{
    use ValidatesRequests, AuthorizesRequests, DispatchesJobs;

    public function store()
    {
        request()->validate([
            'article_id' => 'required|exists:articles,id',
            'publish_date' => 'date'
        ]);
        $article = Article::find(request('article_id'));
        $article->publish(request('publish_date', null));

        return $article->fresh()->toJsonableArray();
    }

    public function delete($article)
    {
        Article::findOrFail($article)->retract();
    }
}