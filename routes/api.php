<?php

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/{version}/posts',
    function (Request $request, $version){
        return app()->make("App\Http\Controllers\API\\$version\\PostController")->index($request);
    }
)->middleware('apiVersionControl:PostController,index');

Route::get('/{version}/post/{post:id}',
    function ($version, Post $post){
        return app()->make("App\Http\Controllers\API\\$version\\PostController")->show($post);
    }
)->middleware('apiVersionControl:PostController,show');
