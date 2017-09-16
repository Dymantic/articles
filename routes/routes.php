<?php

Route::group(['prefix' => 'admin/services', 'namespace' => '\Dymantic\Articles\Controllers\Services'], function() {
    Route::group(['middleware' => ['web', 'auth']], function() {
        Route::get('articles', 'ArticlesListServiceController@index');
    });
});

Route::group(['prefix' => 'admin', 'namespace' => '\Dymantic\Articles\Controllers'], function() {
    Route::group(['middleware' => ['web', 'auth']], function() {

        Route::get('articles', 'ArticlesController@index');
        Route::get('articles/{article}', 'ArticlesController@show');
        Route::post('articles', 'ArticlesController@store');
        Route::post('articles/{article}', 'ArticlesController@update');
        Route::delete('articles/{article}', 'ArticlesController@delete');

        Route::get('articles/{article}/body', 'ArticlesBodyController@show');
        Route::post('articles/{article}/body', 'ArticlesBodyController@update');

        Route::post('published-articles', 'PublishedArticlesController@store');
        Route::delete('published-articles/{article}', 'PublishedArticlesController@delete');

        Route::post('articles/{article}/title-images', 'ArticleTitleImageController@store');
        Route::delete('articles/{article}/title-images', 'ArticleTitleImageController@delete');

        Route::post('articles/{article}/images', 'ArticleImagesController@store');

    });
});