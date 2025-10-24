<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategorieResource;
use App\Models\Categorie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CategorieController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Lister toutes les catégories",
     *     tags={"Catégories"},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des catégories récupérée avec succès"
     *     )
     * )
     * 
     * Lister toutes les catégories
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(CategorieResource::collection(Categorie::all()), 200);
    }

    

    /** 
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Créer une catégorie",
     *     tags={"Catégories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom"},
     *             @OA\Property(property="nom", type="string", example="Technologie"),
     *             @OA\Property(property="description", type="string", example="Catégorie sur les articles tech")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Catégorie créée avec succès"),
     * )
     * 
     * Créer(stocker) une catégorie
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
        ]);

        $categorie = Categorie::create($validated);
        return response()->json(CategorieResource::make($categorie), 201);
    }


    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Afficher une catégorie spécifique",
     *     description="Récupère les détails d'une catégorie selon son ID",
     *     operationId="getCategorieById",
     *     tags={"Catégories"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Identifiant unique de la catégorie",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Catégorie trouvée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nom", type="string", example="Technologie"),
     *             @OA\Property(property="description", type="string", example="Articles sur les innovations tech"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-23T10:20:30"),
     *             @OA\Property(property="created_at_formatted", type="string", example="2025-10-23 10:20:30")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Catégorie introuvable",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Catégorie introuvable")
     *         )
     *     )
     * )
     *
     * Afficher une catégorie spécifique
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $categorie = Categorie::find($id);

        if (!$categorie) {
            return response()->json(['message' => 'Catégorie introuvable'], 404);
        }

        return response()->json(CategorieResource::make($categorie), 200);
    }



    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Mettre à jour une catégorie",
     *     description="Met à jour les informations d'une catégorie existante selon son ID.",
     *     operationId="updateCategorie",
     *     tags={"Catégories"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Identifiant unique de la catégorie à mettre à jour",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Les champs à mettre à jour pour la catégorie",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="nom", type="string", example="Technologie et Innovation"),
     *             @OA\Property(property="description", type="string", example="Catégorie mise à jour avec des articles sur la tech moderne")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Catégorie mise à jour avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nom", type="string", example="Technologie et Innovation"),
     *             @OA\Property(property="description", type="string", example="Catégorie mise à jour avec succès"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-23T09:12:30"),
     *             @OA\Property(property="created_at_formatted", type="string", example="2025-10-23 09:12:30")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Catégorie introuvable",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Catégorie introuvable")
     *         )
     *     ),
     *
     * )
     *
     * Update Category table
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $categorie = Categorie::find($id);

        if (!$categorie) {
            return response()->json(['message' => 'Catégorie introuvable'], 404);
        }

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255|unique:categories,nom,' . $categorie->id,
            'description' => 'nullable|string',
        ]);

        $categorie->update($validated);

        return response()->json(CategorieResource::make($categorie), 200);
    }


    /**
     * Supprimer une catégorie
     *
     * Supprime une catégorie spécifique de la base de données.
     *
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Supprimer une catégorie",
     *     description="Supprime une catégorie existante à partir de son ID.",
     *     operationId="deleteCategorie",
     *     tags={"Catégories"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie à supprimer",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Catégorie supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Catégorie supprimée avec succès")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Catégorie introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Catégorie introuvable")
     *         )
     *     )
     * )
     *
     * Supprimer une catégorie
     * @param \App\Models\Categorie $categorie
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $categorie = Categorie::find($id);
        if(!$categorie) {
            // dd($categorie); 
            return response()->json(['message' => 'Catégorie introuvable'], 400);
        } else {
            $categorie->delete();
            return response()->json(['message' => 'Catégorie supprimée avec succès'], 200);
        }
    }
}




