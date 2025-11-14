<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CommentController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('articles', ArticleController::class);
Route::apiResource('categories', CategorieController::class);

Route::get('/articles/{articleId}/comments', [CommentController::class, 'indexComment']);   // Voir les commentaires d’un article
// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Route::post('/categories', [CategorieController::class, 'store']);
    // Route::post('/categories', [CategorieController::class, 'store']);
    // Route::post('/categories', [CategorieController::class, 'store']);


    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/dashboard', [AuthController::class, 'dashboard']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/articles/{articleId}/comments', [CommentController::class, 'storeComment']);   // Ajouter un commentaire
    Route::delete('/comments/{comment}', [CommentController::class, 'destroyComment']);  // Supprimer un commentaire
    Route::put('/comments/{comment}', [CommentController::class, 'updateComment']);
});



Route::post('/forgot-password', [PasswordResetController::class, 'forgot']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

    Route::post('/admin/categories', [AdminController::class, 'addCategorie']);
    Route::delete('/admin/categories/{category}', [AdminController::class, 'deleteCategorie']);
    Route::put('/admin/categories/{category}', [AdminController::class, 'updateCategorie']);
    
    Route::post('/admin/articles', [AdminController::class, 'addArticle']);
    Route::delete('/admin/articles/{article}', [AdminController::class,'deleteArticle']);
    Route::put('/admin/articles/{article}', [AdminController::class, 'updateArticle']);

    Route::get('admin/users', [AdminController::class, 'index']);
    Route::put('admin/users/{user}/role', [AdminController::class, 'updateRole']);
    Route::delete('admin/users/{user}', [AdminController::class, 'destroy']);
});

