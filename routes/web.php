<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArticleController;

use App\Http\Controllers\CategorieController;

Route::get('/', function () {
    return view('welcome');
});


