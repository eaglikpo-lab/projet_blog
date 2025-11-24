<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
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
     * @OA\Post(
     *     path="/api/articles",
     *     operationId="storeArticle",
     *     tags={"Articles"},
     *     summary="Créer un nouvel article",
     *     description="Crée un nouvel article avec un titre, contenu, catégorie et éventuellement une image et des mots-clés.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"titre", "contenu", "categorie_id"},
     *                 @OA\Property(property="titre", type="string", example="Les nouveautés de Laravel 12"),
     *                 @OA\Property(property="contenu", type="string", example="Laravel 12 introduit une nouvelle structure API..."),
     *                 @OA\Property(property="image", type="file", format="binary", description="Image associée à l'article"),
     *                 @OA\Property(property="mots_cles", type="string", example="Laravel, PHP, Backend"),
     *                 @OA\Property(property="categorie_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation des champs",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The titre field is required.")
     *         )
     *     )
     * )
     *
     * Créer (stocker) un nouvel article dans la table Article
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

        $validated['admin_id'] = auth()->id();

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // => ex: "images/uh3s8sd92.jpg"
            $path = $request->file('image')->store('images', 'public');
            $validated['image'] = $path;
        }

        $article = Article::create($validated);
        return response()->json(ArticleResource::make($article), 201);
    }



    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     operationId="getArticleById",
     *     tags={"Articles"},
     *     summary="Afficher un article spécifique",
     *     description="Récupère les informations détaillées d’un article à partir de son identifiant.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Identifiant de l'article à afficher",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article récupéré avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Article introuvable")
     *         )
     *     )
     * )
     * 
     *  Afficher un article spécifique
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
     * @OA\Put(
     *     path="/api/articles/{id}",
     *     operationId="updateArticle",
     *     tags={"Articles"},
     *     summary="Mettre à jour un article existant",
     *     description="Permet de modifier un article existant à partir de son identifiant.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Identifiant de l'article à modifier",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="titre", type="string", example="Titre mis à jour"),
     *                 @OA\Property(property="contenu", type="string", example="Nouveau contenu de l'article..."),
     *                 @OA\Property(property="image", type="string", format="binary", description="Image optionnelle (jpg, jpeg, png, gif)"),
     *                 @OA\Property(property="mots_cles", type="string", example="laravel, php, mise à jour"),
     *                 @OA\Property(property="categorie_id", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article mis à jour avec succès",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=7),
     *              @OA\Property(property="title", type="string", example="Titre mis à jour"),
     *              @OA\Property(property="contenu", type="string", example="Nouveau contenu de l'article..."),
     *              @OA\Property(property="image", type="string", nullable=true, example="http://127.0.0.1:8000/storage/images/abc.jpg"),
     *              @OA\Property(property="keywords", type="string", example="laravel, php, mise à jour"),
     *              @OA\Property(property="categorie_id", type="integer", example=2),
     *              @OA\Property(property="categorie", type="string", nullable=true, example="Tech"),
     *              @OA\Property(property="created_at", type="string", format="date-time"),
     *              @OA\Property(property="created_at_formatted", type="string", example="2025-10-23 17:46:47"),
     *              @OA\Property(property="updated_at", type="string", format="date-time"),
     *              @OA\Property(property="updated_at_formatted", type="string", example="2025-10-23 17:46:47")
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Article introuvable")
     *         )
     *     )
     * )
     * 
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

        $validated['admin_id'] = auth()->id();

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
