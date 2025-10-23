<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategorieResource;
use App\Models\Categorie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class CategorieController extends Controller
{
    /**
     * Lister toutes les catégories
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(CategorieResource::collection(Categorie::all()), 200);
    }

    /**
     * Créer une nouvelle catégorie
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


   /* public function store(Request $request): RedirectResponse
    {
        $name = $request->input('name');
 
        // Store the user...
 
        return redirect('/users');
    }*/

    /**
     * Afficher une catégorie spécifique
     * @param \App\Models\Categorie $categorie
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
     * Update Category table
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Categorie $categorie
     * @return \Illuminate\Http\JsonResponse
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
     * @param \App\Models\Categorie $categorie
     * @return JsonResponse
     */
    public function destroy(Categorie $categorie)
    {
        $categorie->delete();
        return response()->json(['message' => 'Catégorie supprimée avec succès']);
    }
}




