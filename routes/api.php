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
        'middleware' => 'auth:api',
        'prefix'    => 'categories',
        'as'        => 'categories.',
    ], function () {
        Route::post('/delete', [
            'as'   => 'delete',
            'uses' => 'CategoryController@delete',
        ]);
        Route::post('/create', [
            'as'   => 'create',
            'uses' => 'CategoryController@store',
        ]);
        Route::post('/update', [
            'as'   => 'update',
            'uses' => 'CategoryController@update',
        ]);
        Route::get('/index', [
            'as'   => 'index',
            'uses' => 'CategoryController@index',
        ]);
        Route::get('/', [
            'as'   => 'index',
            'uses' => 'CategoryController@index',
        ]);
        Route::get('/{id}', [
            'as'   => 'show',
            'uses' => 'CategoryController@show',
        ]);
    });

    //articles
    Route::group([
        'middleware' => 'auth:api',
        'prefix'    => 'articles',
        'as'        => 'articles.',
    ], function () {
        Route::post('/delete', [
            'as'   => 'delete',
            'uses' => 'ArticleController@delete',
        ]);
        Route::post('/create', [
            'as'   => 'create',
            'uses' => 'ArticleController@store',
        ]);
        Route::post('/update', [
            'as'   => 'update',
            'uses' => 'ArticleController@update',
        ]);
        Route::get('/index', [
            'as'   => 'index',
            'uses' => 'ArticleController@index',
        ]);
        Route::get('/', [
            'as'   => 'index',
            'uses' => 'ArticleController@index',
        ]);
        Route::get('/{id}', [
            'as'   => 'show',
            'uses' => 'ArticleController@show',
        ]);
    });

    //newspaper
    Route::group([
        'middleware' => 'auth:api',
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

    //tags
    Route::group([
        'middleware' => 'auth:api',
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
