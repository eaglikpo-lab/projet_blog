<?php   

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Créer un commentaire ou une réponse
     * @param \Illuminate\Http\Request $request
     * @param mixed $articleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeComment(Request $request, $articleId)
    {
        $validated = $request->validate([
            'contenu' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id', // facultatif → réponse à un commentaire
        ]);

     
        $comment = Comment::create([
            'user_id' => Auth::id(),
            'article_id' => $articleId,
            'contenu' => $validated['contenu'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);
        // return response()->json([
        //     'message' => 'Commentaire ajouté avec succès.',
        //     'comment' => $comment->load('user'),
        // ], 201);
        return response()->json([
            'message' => 'Commentaire ajouté avec succès.',
            'comment' => $comment], 201);
    }

    /**
     * Récupérer les commentaires d’un article (avec les réponses)
     * @param mixed $articleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexComment($articleId)
    {
        $comments = Comment::with(['user', 'replies.user'])
            ->where('article_id', $articleId)
            ->whereNull('parent_id') // uniquement les principaux
            ->orderByDesc('created_at')
            ->get();

        return response()->json($comments);
    }

    /**
     * Supprimer un commentaire (et ses réponses)
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyComment(Comment $comment)
    {
        
        if (Auth::id() !== $comment->user_id && Auth::user()->role!=="admin") {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $comment->delete();

        return response()->json(['message' => ' Commentaire supprimé avec succès.']);
    }

    /**
     * Mettre à jour un commentaire
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateComment(Request $request, Comment $comment)
    {
        // Vérifier que l’utilisateur est bien l’auteur du commentaire
        if (auth()->id() !== $comment->user_id) {
            return response()->json(['message' => "Vous n'êtes pas autorisé à modifier ce commentaires."], 403);
        }

        // Validation du contenu
        $validated = $request->validate([
            'contenu' => 'required|string|max:1000',
        ]);

        // Mise à jour du commentaire
        $comment->update([
            'contenu' => $validated['contenu'],
        ]);

        return response()->json([
            'message' => 'Commentaire mis à jour avec succès.',
            'comment' => $comment,
        ], 200);
    }
}
