<?php


namespace Dymantic\Articles;


use Illuminate\Support\ServiceProvider;

class ArticlesServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if (! class_exists('CreateArticlesTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_articles_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_articles_table.php'),
            ], 'migrations');
        }
    }

    public function register()
    {

    }
}