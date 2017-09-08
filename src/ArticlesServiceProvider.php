<?php


namespace Dymantic\Articles;


use Cviebrock\EloquentSluggable\ServiceProvider as SluggableServiceProvider;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

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
        $this->app->register(MediaLibraryServiceProvider::class);
        $this->app->register(SluggableServiceProvider::class);
    }
}