<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;



/**
 * Summary of index
 *      
 * Retourne la liste paginée des articles.  
 * Permet également de filtrer les articles par catégorie en utilisant le paramètre de requête `categorie_id`.
 *
 * @OA\Get(
 *     path="/api/articles",
 *     summary="Lister tous les articles",
 *     description="Récupère tous les articles ou ceux d'une catégorie spécifique si `categorie_id` est fourni.",
 *     operationId="getArticles",
 *     tags={"Articles"},
 *
 *     @OA\Parameter(
 *         name="categorie_id",
 *         in="query",
 *         required=false,
 *         description="Filtrer les articles par ID de catégorie",
 *         @OA\Schema(type="integer", example=2)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Liste des articles récupérée avec succès",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Article")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Aucun article trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Aucun article trouvé")
 *         )
 *     )
 * )
 * 
 * Lister les articles
 * 
 * @param \Illuminate\Http\Request $request
 * @return JsonResponse
 */
class ArticleController extends Controller
{
      public function index(Request $request): JsonResponse
    {
        $categorieId = $request->query('categorie_id');
       // dd($categorieId);
        if($categorieId) {
            $articles = Article::forCategory($categorieId)->latest()->paginate(10);
        } else {
            $articles = Article::with('categorie')->paginate(10);
        }
        return response()->json( ArticleResource::collection($articles)->resource,200);
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mots_cles' => 'nullable|string',
            'categorie_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // => ex: "images/uh3s8sd92.jpg"
            $path = $request->file('image')->store('images', 'public');
            $validated['image'] = $path;
        }


        $article = Article::create($validated);
        return response()->json(ArticleResource::make($article), 201);
    }


    /**
     * Afficher un article spécifique
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article introuvable'], 404);
        }
        return response()->json(ArticleResource::make($article->load('categorie')), 200);
    }

    /**
     *  Mettre à jour un article
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request,  $id): JsonResponse
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article introuvable'], 404);
        }
        $validated = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'contenu' => 'sometimes|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'mots_cles' => 'nullable|string',
            'categorie_id' => 'sometimes|exists:categories,id',
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $validated['image'] = $request->file('image')->store('images', 'public');
        }

        $article->update($validated);
        return response()->json(ArticleResource::make($article), 200);
    }


    /**
     * Supprime une article spécifique de la base de données.
     *
     * @OA\Delete(
     *     path="/api/articles/{id}",
     *     summary="Supprimer une article",
     *     description="Supprime une article existante à partir de son ID.",
     *     operationId="deleteArticle",
     *     tags={"Articles"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'article à supprimer",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Article supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Article supprimée avec succès")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Article introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Article introuvable")
     *         )
     *     )
     * )
     *
     *  Supprimer un article
     * @param \App\Models\Article $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Article $article)
    {
      //  $article = Article::find($id);
        if (!$article) {
            return response()->json(['message' => 'Article introuvable'], 404);
        }
        $article->delete();
        return response()->json(['message' => 'Article supprimé avec succès'], 204);
    }
}
