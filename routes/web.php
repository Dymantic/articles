<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('front.home.page');
});

Route::get('about', function () {
    return view('front.about.page');
});

Route::get('testimonials', function () {
    return view('front.testimonials.page');
});

Route::get('testimonials', function () {
    return view('front.testimonials.page');
});

Route::get('thanks', function () {
    return view('front.thanks');
});

Route::get('contact', 'ContactController@show');
Route::post('contact', 'ContactController@store');

Route::get('admin/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('admin/login', 'Auth\LoginController@login');
Route::post('admin/logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
//$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
$this->post('admin/users', 'Auth\RegisterController@register');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset');

Route::group(['prefix' => 'admin/services', 'namespace' => 'Admin\Services'], function() {


    Route::group(['middleware' => 'auth'], function() {
        Route::get('users', 'UsersServiceController@index');

        Route::get('articles', 'ArticlesListServiceController@index');

        Route::get('testimonials', 'TestimonialsController@index');
    });
});

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function() {


    Route::group(['middleware' => 'auth'], function() {
        Route::get('/', 'DashboardController@dashboard');

        Route::get('users', 'UsersController@index');
        Route::delete('users/{user}', 'UsersController@delete')->middleware('superadmin');

        Route::get('users/passwords', 'UsersPasswordController@edit');
        Route::post('users/passwords', 'UsersPasswordController@update');

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

        Route::get('testimonials', 'TestimonialsController@index');
        Route::post('testimonials', 'TestimonialsController@store');
        Route::post('testimonials/{testimonial}', 'TestimonialsController@update');
        Route::delete('testimonials/{testimonial}', 'TestimonialsController@delete');

        Route::post('published-testimonials', 'PublishedTestimonialsController@store');
        Route::delete('published-testimonials/{testimonial}', 'PublishedTestimonialsController@delete');

        Route::post('testimonials/{testimonial}/image', 'TestimonialsImageController@store');
        Route::delete('testimonials/{testimonial}/image', 'TestimonialsImageController@delete');
    });
});

