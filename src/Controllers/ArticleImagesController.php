<?php


namespace Dymantic\Articles\Controllers;


use Dymantic\Articles\Article;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class ArticleImagesController extends Controller
{
    use ValidatesRequests, AuthorizesRequests, DispatchesJobs;

    public function store($article)
    {
        request()->validate(['image' => 'required|image']);

        $image = Article::findOrFail($article)->addImage(request('image'));

        return ['location' => $image->getUrl('web')];
    }
}