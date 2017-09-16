<?php

namespace Dymantic\Articles\Controllers\Services;

use Dymantic\Articles\Article;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class ArticlesListServiceController extends Controller
{
    use ValidatesRequests, DispatchesJobs, AuthorizesRequests;

    public function index()
    {
        $articles = Article::latest()->paginate(15);

        return [
            'articles' => collect($articles->items())->map->toJsonableArray(),
            'page_links' => $articles->getUrlRange(1, $articles->lastPage()),
            'current_page' => $articles->currentPage()
        ];
    }
}