<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArticleController;


Route::get('/', [ArticleController::class, 'index'])->name('articles.index');

Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create'); // create the new article 

Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store'); // saves the new article in the database

// Route de suppression
Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

Route::get('/articles/back', [ArticleController::class, 'back'])->name('articles.back');
Route::get('/articles/search', [ArticleController::class, 'search'])->name('articles.search');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');

Route::get('/articles/category/{category}', [ArticleController::class, 'byCategory'])->name('articles.byCategory');

Route::resource('articles', ArticleController::class);

