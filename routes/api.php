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

    Route::group([
        'prefix'    => 'categories',
        'as'        => 'categories.',
    ], function () {
        Route::post('/delete', [
            'middleware' => 'auth:api',
            'as'   => 'delete',
            'uses' => 'CategoriesController@delete',
        ]);
        Route::post('/create', [
            'middleware' => 'auth:api',
            'as'   => 'create',
            'uses' => 'CategoriesController@store',
        ]);
        Route::post('/update', [
            'middleware' => 'auth:api',
            'as'   => 'update',
            'uses' => 'CategoriesController@update',
        ]);
        Route::get('/index', [
            'as'   => 'index',
            'uses' => 'CategoriesController@index',
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
            'middleware' => 'auth:api',
            'as'   => 'delete',
            'uses' => 'ArticlesController@delete',
        ]);
        Route::post('/create', [
            'middleware' => 'auth:api',
            'as'   => 'create',
            'uses' => 'ArticlesController@store',
        ]);
        Route::post('/update', [
            'middleware' => 'auth:api',
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
            'middleware' => 'auth:api',
            'as'   => 'delete',
            'uses' => 'NewspaperController@delete',
        ]);
        Route::post('/create', [
            'middleware' => 'auth:api',
            'as'   => 'create',
            'uses' => 'NewspaperController@store',
        ]);
        Route::post('/update', [
            'middleware' => 'auth:api',
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

    //tags
    Route::group([
        'prefix'    => 'tags',
        'as'        => 'tags.',
    ], function () {
        Route::post('/delete', [
            'middleware' => 'auth:api',
            'as'   => 'delete',
            'uses' => 'TagsController@delete',
        ]);
        Route::post('/create', [
            'middleware' => 'auth:api',
            'as'   => 'create',
            'uses' => 'TagsController@store',
        ]);
        Route::post('/update', [
            'middleware' => 'auth:api',
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

    //languages
    Route::group([
        'middleware' => 'auth:api',
        'prefix'    => 'languages',
        'as'        => 'languages.',
    ], function () {
        Route::get('/', [
            'as'   => 'index',
            'uses' => 'LanguagesController@index',
        ]);
    });
});
