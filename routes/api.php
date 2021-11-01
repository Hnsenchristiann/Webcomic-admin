<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\ComicController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TokenTransactionController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Laravue\Faker;
use \App\Laravue\JsonResponse;
use \App\Laravue\Acl;

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

Route::namespace('Api')->group(function() {
    Route::post('auth/login', 'AuthController@login');
    Route::group(['middleware' => 'auth:sanctum'], function () {
        // Auth routes
        Route::get('auth/user', 'AuthController@user');
        Route::post('auth/logout', 'AuthController@logout');

        Route::get('/user', function (Request $request) {
            return new UserResource($request->user());
        });

        // Api resource routes
        Route::apiResource('roles', 'RoleController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::apiResource('users', 'UserController')->middleware('permission:' . Acl::PERMISSION_USER_MANAGE);
        Route::apiResource('permissions', 'PermissionController')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);

        Route::get('settings', [SettingController::class, 'index']);
        Route::get('settings/{setting}', [SettingController::class, 'show']);
        Route::patch('setting/{setting}', [SettingController::class, 'update']);

        // Custom routes
        Route::put('users/{user}', 'UserController@update');
        Route::get('users/{user}/permissions', 'UserController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);
        Route::put('users/{user}/permissions', 'UserController@updatePermissions')->middleware('permission:' .Acl::PERMISSION_PERMISSION_MANAGE);
        Route::get('roles/{role}/permissions', 'RoleController@permissions')->middleware('permission:' . Acl::PERMISSION_PERMISSION_MANAGE);

        Route::get('users', [UserController::class, 'index']);//'UserController@index');
        Route::get('users/{user}', [UserController::class, 'show']);//'UserController@show');
        Route::post('user', [UserController::class, 'store']);//'UserController@store');
        Route::patch('user/{user}', [UserController::class, 'update']);//'UserController@update');
        Route::delete('user/{user}', [UserController::class, 'destroy']);//'UserController@destroy');
        Route::get('/users/{user}/checkbalance', [UserController::class, 'checkBalance']);
        Route::get('/users/{user}/rectifybalance', [UserController::class, 'rectifyBalance']);
        Route::post('/users/{user}/granttokens', [UserController::class, 'grantTokens']);

        Route::get('comics', [ComicController::class, 'index']);//'ComicController@index');
        Route::get('comics/{comic}', [ComicController::class, 'show']);//'ComicController@show');
        Route::get('comics/{comic}/transactions', [ComicController::class, 'showTransactions']);//'ComicController@show');
        Route::post('comic', [ComicController::class, 'store']);//'ComicController@store');
        Route::patch('comic/{comic}', [ComicController::class, 'update']);//'ComicController@update');
        Route::delete('comic/{comic}', [ComicController::class, 'destroy']);//'ComicController@destroy');

        Route::get('authors', [AuthorController::class, 'index']);//'ComicController@index');
        Route::get('authors/{author}', [AuthorController::class, 'show']);//'AuthorController@show');
        Route::post('author', [AuthorController::class, 'store']);//'AuthorController@store');
        Route::patch('author/{author}', [AuthorController::class, 'update']);//'AuthorController@update');
        Route::delete('author/{author}', [AuthorController::class, 'destroy']);//'AuthorController@destroy');

        Route::get('genres', [GenreController::class, 'index']);//'ComicController@index');
        Route::get('genres/{genre}', [GenreController::class, 'show']);//'GenreController@show');
        Route::post('genre', [GenreController::class, 'store']);//'GenreController@store');
        Route::patch('genre/{genre}', [GenreController::class, 'update']);//'GenreController@update');
        Route::delete('genre/{genre}', [GenreController::class, 'destroy']);//'GenreController@destroy');

        Route::get('tags', [TagController::class, 'index']);//'ComicController@index');
        Route::get('tags/{tag}', [TagController::class, 'show']);//'TagController@show');
        Route::post('tag', [TagController::class, 'store']);//'TagController@store');
        Route::patch('tag/{tag}', [TagController::class, 'update']);//'TagController@update');
        Route::delete('tag/{tag}', [TagController::class, 'destroy']);//'TagController@destroy');

        Route::get('chapters', [ChapterController::class, 'index']);//'ComicController@index');
        Route::get('chapters/{chapter}', [ChapterController::class, 'show']);//'ChapterController@show');
        Route::post('chapter', [ChapterController::class, 'store']);//'ChapterController@store');
        Route::patch('chapter/{chapter}', [ChapterController::class, 'update']);//'ChapterController@update');
        Route::delete('chapter/{chapter}', [ChapterController::class, 'destroy']);//'ChapterController@destroy');

        Route::get('pages', [PageController::class, 'index']);
        Route::get('pages/{page}', [PageController::class, 'show']);
        Route::post('page', [PageController::class, 'store']);
        Route::patch('page/{page}', [PageController::class, 'update']);
        Route::delete('page/{page}', [PageController::class, 'destroy']);

        Route::get('tokens', [TokenTransactionController::class, 'index']);
        Route::get('tokens/queriedtotal', [TokenTransactionController::class, 'getTotalTokens']);
        Route::get('tokens/{token}', [TokenTransactionController::class, 'show']);
        Route::post('token', [TokenTransactionController::class, 'store']);
        Route::patch('token/{token}', [TokenTransactionController::class, 'update']);
        Route::delete('token/{token}', [TokenTransactionController::class, 'destroy']);

        Route::get('data/transactions/raw', [DataController::class, 'getTotalTokensTransactionData']);
        Route::get('data/transactions/daily/{startDate?}/{endDate?}', [DataController::class, 'getDailyTransactionData']);
        Route::get('data/transactions/user/{userId}/{startDate?}/{endDate?}', [DataController::class, 'getUserTransactionData']);
        Route::get('data/transactions/comic/{comicId}/{startDate?}/{endDate?}', [DataController::class, 'getComicTransactionData']);
        Route::get('data/transactions/chapter/{chapterId}/{startDate?}/{endDate?}', [DataController::class, 'getChapterTransactionData']);

        Route::get('comments', [CommentController::class, 'index']);
        Route::get('comments/{comment}', [CommentController::class, 'show']);
        Route::delete('comment/{comment}', [CommentController::class, 'destroy']);
    });
});

// Fake APIs
Route::get('/table/list', function () {
    $rowsNumber = mt_rand(20, 30);
    $data = [];
    for ($rowIndex = 0; $rowIndex < $rowsNumber; $rowIndex++) {
        $row = [
            'author' => Faker::randomString(mt_rand(5, 10)),
            'display_time' => Faker::randomDateTime()->format('Y-m-d H:i:s'),
            'id' => mt_rand(100000, 100000000),
            'pageviews' => mt_rand(100, 10000),
            'status' => Faker::randomInArray(['deleted', 'published', 'draft']),
            'title' => Faker::randomString(mt_rand(20, 50)),
        ];

        $data[] = $row;
    }

    return response()->json(new JsonResponse(['items' => $data]));
});

Route::get('/orders', function () {
    $rowsNumber = 8;
    $data = [];
    for ($rowIndex = 0; $rowIndex < $rowsNumber; $rowIndex++) {
        $row = [
            'order_no' => 'LARAVUE' . mt_rand(1000000, 9999999),
            'price' => mt_rand(10000, 999999),
            'status' => Faker::randomInArray(['success', 'pending']),
        ];

        $data[] = $row;
    }

    return response()->json(new JsonResponse(['items' => $data]));
});

Route::get('/articles', function () {
    $rowsNumber = 10;
    $data = [];
    for ($rowIndex = 0; $rowIndex < $rowsNumber; $rowIndex++) {
        $row = [
            'id' => mt_rand(100, 10000),
            'display_time' => Faker::randomDateTime()->format('Y-m-d H:i:s'),
            'title' => Faker::randomString(mt_rand(20, 50)),
            'author' => Faker::randomString(mt_rand(5, 10)),
            'comment_disabled' => Faker::randomBoolean(),
            'content' => Faker::randomString(mt_rand(100, 300)),
            'content_short' => Faker::randomString(mt_rand(30, 50)),
            'status' => Faker::randomInArray(['deleted', 'published', 'draft']),
            'forecast' => mt_rand(100, 9999) / 100,
            'image_uri' => 'https://via.placeholder.com/400x300',
            'importance' => mt_rand(1, 3),
            'pageviews' => mt_rand(10000, 999999),
            'reviewer' => Faker::randomString(mt_rand(5, 10)),
            'timestamp' => Faker::randomDateTime()->getTimestamp(),
            'type' => Faker::randomInArray(['US', 'VI', 'JA']),

        ];

        $data[] = $row;
    }

    return response()->json(new JsonResponse(['items' => $data, 'total' => mt_rand(1000, 10000)]));
});

Route::get('articles/{id}', function ($id) {
    $article = [
        'id' => $id,
        'display_time' => Faker::randomDateTime()->format('Y-m-d H:i:s'),
        'title' => Faker::randomString(mt_rand(20, 50)),
        'author' => Faker::randomString(mt_rand(5, 10)),
        'comment_disabled' => Faker::randomBoolean(),
        'content' => Faker::randomString(mt_rand(100, 300)),
        'content_short' => Faker::randomString(mt_rand(30, 50)),
        'status' => Faker::randomInArray(['deleted', 'published', 'draft']),
        'forecast' => mt_rand(100, 9999) / 100,
        'image_uri' => 'https://via.placeholder.com/400x300',
        'importance' => mt_rand(1, 3),
        'pageviews' => mt_rand(10000, 999999),
        'reviewer' => Faker::randomString(mt_rand(5, 10)),
        'timestamp' => Faker::randomDateTime()->getTimestamp(),
        'type' => Faker::randomInArray(['US', 'VI', 'JA']),

    ];

    return response()->json(new JsonResponse($article));
});

Route::get('articles/{id}/pageviews', function ($id) {
    $pageviews = [
        'PC' => mt_rand(10000, 999999),
        'Mobile' => mt_rand(10000, 999999),
        'iOS' => mt_rand(10000, 999999),
        'android' => mt_rand(10000, 999999),
    ];
    $data = [];
    foreach ($pageviews as $device => $pageview) {
        $data[] = [
            'key' => $device,
            'pv' => $pageview,
        ];
    }

    return response()->json(new JsonResponse(['pvData' => $data]));
});

Route::post('testing', function(Request $request){
    return response()->json($request, 200);
});
Route::get('template/scene', function(){
    $template = file_get_contents(base_path('stubs/Scene.html.stub'));
    return response()->json($template, 200);
});
