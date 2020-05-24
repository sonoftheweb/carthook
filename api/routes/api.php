<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::resource('users', 'API\UserController')->only(['index', 'show']);
Route::resource('posts', 'API\PostController')->only(['index', 'show']);

Route::resource('users.posts', 'API\PostController')->shallow();
Route::resource('posts.comments', 'API\CommentController')->shallow();

Route::fallback(function(){
    return response()->json([
        'message' => 'No such resource...'], 404);
});
