<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Enregistrement d’un nouvel utilisateur
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // nécessite password_confirmation
            //'role'=>['nullable','string','in:user,admin'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
           // 'role'=>$validated['role'],
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user
        ], 201);
    }

    /**
     * Connexion et génération d’un token Sanctum
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        // Supprimer les anciens tokens si besoin
        $user->tokens()->delete();

        // Créer un nouveau token
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Déconnexion et suppression du token actif
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
            return response()->json(['message' => 'Déconnexion réussie']);
        }

        return response()->json(['message' => 'Aucun token actif trouvé'], 400);

    }

    /**
     * Récupérer l'utilisateur actuellement authentifié
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function dashboard()
    {
        return response()->json(['message' => 'Bienvenue sur le tableau de bord user ✅']);
    }
}
