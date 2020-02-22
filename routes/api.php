<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([
    'middleware' => 'cors',
    'prefix'    => 'v1',
    'as'        => 'api.',
    'namespace' => 'Api\\v1',
], function () {

        //languages
        Route::group([
            'prefix'    => 'languages',
            'as'        => 'languages.',
        ], function () {
            Route::get('/', [
                'as'   => 'index',
                'uses' => 'LanguagesController@index',
            ]);
        });

        // Laravel passport
        Route::group([
            'prefix' => 'auth',
            'as'        => 'auth.',
        ], function () {
            Route::post('login', 'AuthController@login');
            Route::post('signup', 'AuthController@signUp');

            Route::group([
                'middleware' => 'auth:api'
            ], function() {
                Route::get('logout', 'AuthController@logout');
                Route::get('details', 'AuthController@details');
            });
        });

        //admin
        Route::group([
            'middleware' => 'auth:api',
            'prefix'    => 'admin',
            'as'        => 'admin.',
            'namespace'        => 'Admin',
        ], function () {
            // images
            Route::group([
                'prefix'    => 'images',
                'as'        => 'images.',
            ], function () {
                Route::post('/upload', [
                    'as'   => 'upload',
                    'uses' => 'ImagesController@upload',
                ]);
                Route::get('/list', [
                    'as'   => 'list',
                    'uses' => 'ImagesController@list',
                ]);
                Route::get('/', [
                    'as'   => 'list',
                    'uses' => 'ImagesController@list',
                ]);
            });
            // categories
            Route::group([
                'prefix'    => 'categories',
                'as'        => 'categories.',
            ], function () {
                Route::post('/delete', [
                    'as'   => 'delete',
                    'uses' => 'CategoriesController@delete',
                ]);
                Route::post('/create', [
                    'as'   => 'create',
                    'uses' => 'CategoriesController@store',
                ]);
                Route::post('/update', [
                    'as'   => 'update',
                    'uses' => 'CategoriesController@update',
                ]);
                Route::get('/index', [
                    'as'   => 'index',
                    'uses' => 'CategoriesController@index',
                ]);
                Route::get('/menu', [
                    'as'   => 'index',
                    'uses' => 'CategoriesController@menu',
                ]);
                Route::get('/', [
                    'as'   => 'index',
                    'uses' => 'CategoriesController@index',
                ]);
                Route::get('/{id}', [
                    'as'   => 'show',
                    'uses' => 'CategoriesController@show',
                ]);
            });

            //articles
            Route::group([
                'prefix'    => 'articles',
                'as'        => 'articles.',
            ], function () {
                Route::post('/delete', [
                    'as'   => 'delete',
                    'uses' => 'ArticlesController@delete',
                ]);
                Route::post('/create', [
                    'as'   => 'create',
                    'uses' => 'ArticlesController@store',
                ]);
                Route::post('/update', [
                    'as'   => 'update',
                    'uses' => 'ArticlesController@update',
                ]);
                Route::get('/index', [
                    'as'   => 'index',
                    'uses' => 'ArticlesController@index',
                ]);
                Route::get('/', [
                    'as'   => 'index',
                    'uses' => 'ArticlesController@index',
                ]);
                Route::get('/{id}', [
                    'as'   => 'show',
                    'uses' => 'ArticlesController@show',
                ]);
            });

            //newspaper
            Route::group([
                'prefix'    => 'newspaper',
                'as'        => 'newspaper.',
            ], function () {
                Route::post('/delete', [
                    'as'   => 'delete',
                    'uses' => 'NewspaperController@delete',
                ]);
                Route::post('/create', [
                    'as'   => 'create',
                    'uses' => 'NewspaperController@store',
                ]);
                Route::post('/update', [
                    'as'   => 'update',
                    'uses' => 'NewspaperController@update',
                ]);
                Route::get('/index', [
                    'as'   => 'index',
                    'uses' => 'NewspaperController@index',
                ]);
                Route::get('/', [
                    'as'   => 'index',
                    'uses' => 'NewspaperController@index',
                ]);
                Route::get('/{id}', [
                    'as'   => 'show',
                    'uses' => 'NewspaperController@show',
                ]);
            });

            //video
            Route::group([
                'prefix'    => 'videos',
                'as'        => 'videos.',
            ], function () {
                Route::post('/delete', [
                    'as'   => 'delete',
                    'uses' => 'VideoController@delete',
                ]);
                Route::post('/create', [
                    'as'   => 'create',
                    'uses' => 'VideoController@store',
                ]);
                Route::post('/update', [
                    'as'   => 'update',
                    'uses' => 'VideoController@update',
                ]);
                Route::get('/index', [
                    'as'   => 'index',
                    'uses' => 'VideoController@index',
                ]);
                Route::get('/', [
                    'as'   => 'index',
                    'uses' => 'VideoController@index',
                ]);
                Route::get('/{id}', [
                    'as'   => 'show',
                    'uses' => 'VideoController@show',
                ]);
            });

            //tags
            Route::group([
                'prefix'    => 'tags',
                'as'        => 'tags.',
            ], function () {
                Route::post('/delete', [
                    'as'   => 'delete',
                    'uses' => 'TagsController@delete',
                ]);
                Route::post('/create', [
                    'as'   => 'create',
                    'uses' => 'TagsController@store',
                ]);
                Route::post('/update', [
                    'as'   => 'update',
                    'uses' => 'TagsController@update',
                ]);
                Route::get('/index', [
                    'as'   => 'index',
                    'uses' => 'TagsController@index',
                ]);
                Route::get('/', [
                    'as'   => 'index',
                    'uses' => 'TagsController@index',
                ]);
                Route::get('/{id}', [
                    'as'   => 'show',
                    'uses' => 'TagsController@show',
                ]);
            });
        });

        // end admin


        // lang
        Route::prefix('{locale}')->group(function () {
            // categories
            Route::group([
                'prefix'    => 'categories',
                'as'        => 'categories.',
            ], function () {
                Route::get('/index', [
                    'as'   => 'index',
                    'uses' => 'CategoriesController@index',
                ]);
                Route::get('/menu', [
                    'as'   => 'index',
                    'uses' => 'CategoriesController@menu',
                ]);
                Route::get('/', [
                    'as'   => 'index',
                    'uses' => 'CategoriesController@index',
                ]);
                Route::get('/{id}', [
                    'as'   => 'show',
                    'uses' => 'CategoriesController@show',
                ]);
            });

            //articles
            Route::group([
                'prefix'    => 'articles',
                'as'        => 'articles.',
            ], function () {
                Route::get('/index', [
                    'as'   => 'index',
                    'uses' => 'ArticlesController@index',
                ]);
                Route::get('/latest', [
                    'as'   => 'index',
                    'uses' => 'ArticlesController@latest',
                ]);
                Route::get('/search', [
                    'as'   => 'index',
                    'uses' => 'ArticlesController@search',
                ]);
                Route::get('/', [
                    'as'   => 'index',
                    'uses' => 'ArticlesController@index',
                ]);
                Route::get('/{id}', [
                    'as'   => 'show',
                    'uses' => 'ArticlesController@show',
                ]);
                Route::get('/alike/{id}', [
                    'as'   => 'alike',
                    'uses' => 'ArticlesController@alike',
                ]);
            });

            //newspaper
            Route::group([
                'prefix'    => 'newspaper',
                'as'        => 'newspaper.',
            ], function () {
                Route::get('/index', [
                    'as'   => 'index',
                    'uses' => 'NewspaperController@index',
                ]);
                Route::get('/', [
                    'as'   => 'index',
                    'uses' => 'NewspaperController@index',
                ]);
                Route::get('/{id}', [
                    'as'   => 'show',
                    'uses' => 'NewspaperController@show',
                ]);
            });

            //tags
            Route::group([
                'prefix'    => 'tags',
                'as'        => 'tags.',
            ], function () {
                Route::get('/index', [
                    'as'   => 'index',
                    'uses' => 'TagsController@index',
                ]);
                Route::get('/', [
                    'as'   => 'index',
                    'uses' => 'TagsController@index',
                ]);
                Route::get('/{id}', [
                    'as'   => 'show',
                    'uses' => 'TagsController@show',
                ]);
            });

            //video with lang
            Route::group([
                'prefix'    => 'videos',
                'as'        => 'videos.',
            ], function () {
                Route::get('/index', [
                    'as'   => 'index',
                    'uses' => 'VideoController@index',
                ]);
                Route::get('/', [
                    'as'   => 'index',
                    'uses' => 'VideoController@index',
                ]);
                Route::get('/{id}', [
                    'as'   => 'show',
                    'uses' => 'VideoController@show',
                ]);
            });
        });

        // categories
        Route::group([
            'prefix'    => 'categories',
            'as'        => 'categories.',
        ], function () {
            Route::get('/index', [
                'as'   => 'index',
                'uses' => 'CategoriesController@index',
            ]);
            Route::get('/menu', [
                'as'   => 'index',
                'uses' => 'CategoriesController@menu',
            ]);
            Route::get('/', [
                'as'   => 'index',
                'uses' => 'CategoriesController@index',
            ]);
            Route::get('/{id}', [
                'as'   => 'show',
                'uses' => 'CategoriesController@show',
            ]);
        });

        //articles
        Route::group([
            'prefix'    => 'articles',
            'as'        => 'articles.',
        ], function () {
            Route::get('/index', [
                'as'   => 'index',
                'uses' => 'ArticlesController@index',
            ]);
            Route::get('/latest', [
                'as'   => 'index',
                'uses' => 'ArticlesController@latest',
            ]);
            Route::get('/search', [
                'as'   => 'index',
                'uses' => 'ArticlesController@search',
            ]);
            Route::get('/', [
                'as'   => 'index',
                'uses' => 'ArticlesController@index',
            ]);
            Route::get('/{id}', [
                'as'   => 'show',
                'uses' => 'ArticlesController@show',
            ]);
        });

        //newspaper
        Route::group([
            'prefix'    => 'newspaper',
            'as'        => 'newspaper.',
        ], function () {
            Route::get('/index', [
                'as'   => 'index',
                'uses' => 'NewspaperController@index',
            ]);
            Route::get('/', [
                'as'   => 'index',
                'uses' => 'NewspaperController@index',
            ]);
            Route::get('/{id}', [
                'as'   => 'show',
                'uses' => 'NewspaperController@show',
            ]);
        });

        //video without lang
        Route::group([
            'prefix'    => 'videos',
            'as'        => 'videos.',
        ], function () {
            Route::get('/index', [
                'as'   => 'index',
                'uses' => 'VideoController@index',
            ]);
            Route::get('/', [
                'as'   => 'index',
                'uses' => 'VideoController@index',
            ]);
            Route::get('/{id}', [
                'as'   => 'show',
                'uses' => 'VideoController@show',
            ]);
        });

        //tags
        Route::group([
            'prefix'    => 'tags',
            'as'        => 'tags.',
        ], function () {
            Route::get('/index', [
                'as'   => 'index',
                'uses' => 'TagsController@index',
            ]);
            Route::get('/', [
                'as'   => 'index',
                'uses' => 'TagsController@index',
            ]);
            Route::get('/{id}', [
                'as'   => 'show',
                'uses' => 'TagsController@show',
            ]);
        });

});
