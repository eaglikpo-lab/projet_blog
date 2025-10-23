<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    
    /**
     * Lister tous les articles
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(ArticleResource::collection(Article::with('article')->get()), 200);
    }

    /**
     *  Créer (stocker) un nouvel article dans la table Article
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'image' => 'nullable|string',
            'mots_cles' => 'nullable|string',
            'article_id' => 'required|exists:articles,id',
        ]);

        $article = Article::create($validated);
        return response()->json(ArticleResource::make($article), 201);
    }


    /**
     * Afficher un article spécifique
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Article $article)
    {
        return response()->json(ArticleResource::make($article->load('article')), 200);
    }

    /**
     *  Mettre à jour un article
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Article $article)
    {
        $article = Article::find($article->id);

        if (!$article) {
            return response()->json(['message' => 'Article introuvable'], 404);
        }
        $validated = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'contenu' => 'sometimes|string',
            'image' => 'nullable|string',
            'mots_cles' => 'nullable|string',
            'categorie_id' => 'sometimes|exists:categories,id',
        ]);

        $article->update($validated);
        return response()->json(ArticleResource::make($article), 200);
    }

    /**
     *  Supprimer un article
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json(['message' => 'Article supprimé avec succès'], 204);
    }
}
