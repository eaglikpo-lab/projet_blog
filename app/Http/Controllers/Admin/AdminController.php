<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategorieController;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Categorie;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Actions sur Categorie
    public function dashboard()
    {
        return response()->json(['message' => 'Bienvenue sur le tableau de bord admin ✅']);
    }

    public function addCategorie(Request $request)
    {
        $categorieController = new CategorieController();
        return $categorieController->store($request);
    }

    public function deleteCategorie($id)
    {
        $categorieController = new CategorieController();
        return $categorieController->destroy($id);
    }

    public function updateCategorie(Request $request, $id)
    {
        $categorieController = new CategorieController();
        return $categorieController->update($request, $id);
    }
    


    // Actions sur Article
    public function addArticle(Request $request)
    {
        $articleController = new ArticleController();
        return $articleController->store($request);
    }

    public function deleteArticle(Article $article) {
        $articleController = new ArticleController();
        return $articleController->destroy($article);
    }

    public function updateArticle(Request $request, $id)
    {
        $articleController = new ArticleController();
        return $articleController->update( $request, $id);    
    }


    /**
     * Liste de tous les utilisateurs
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role')->get();
        return response()->json($users);
    }


    /**
     * Promouvoir ou rétrograder un utilisateur
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:user,admin',
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        // Optionnel : empêcher le dernier admin d’être rétrogradé
        if ($user->role === 'admin' && $request->role === 'user') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json(['message' => 'Impossible de rétrograder le dernier administrateur.'], 403);
            }
        }

        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => "Le rôle de l'utilisateur a été mis à jour avec succès.",
            'user' => $user,
        ]);
    }


    /**
     * Supprimer un utilisateur
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        // On empêche un admin de se supprimer lui-même
        if (Auth::id() === $user->id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
            ], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }
}
