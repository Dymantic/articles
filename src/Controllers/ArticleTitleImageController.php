<?php


namespace Dymantic\Articles\Controllers;


use Dymantic\Articles\Article;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class ArticleTitleImageController extends Controller
{
    use ValidatesRequests, DispatchesJobs, AuthorizesRequests;

    public function store($article)
    {
        request()->validate(['image' => 'required|image']);
        $image = Article::findOrFail($article)->setTitleImage(request('image'));

        return ['image_src' => $image->getUrl()];
    }

    public function delete($article)
    {
        Article::findOrFail($article)->clearTitleImage();
    }
}