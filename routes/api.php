<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategorieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('categories', CategorieController::class);
Route::apiResource('articles', ArticleController::class);
