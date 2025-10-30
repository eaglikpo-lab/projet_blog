<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * Envoi du lien de réinitialisationDR  
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            // Génération d’un code à 6 chiffres
            $code = rand(100000, 999999);

            // Stocker le code temporairement (valable 5 minutes)
            Cache::put('reset_code_' . $notifiable->email, $code, now()->addMinutes(5));

            $frontendUrl = 'http://localhost:5173/reset-password?token=' . $token . '&email=' . urlencode($notifiable->email);

            return (new MailMessage)
                ->subject('Réinitialisation de votre mot de passe')
                ->greeting('Bonjour,')
                ->line('Vous pouvez réinitialiser votre mot de passe de deux manières :')
                ->line(' 1️⃣ Cliquez sur ce lien :')
                ->action('Réinitialiser mon mot de passe', $frontendUrl)
                ->line(' 2️⃣ Ou entrez ce code : **' . $code . '**')
                ->line('Ce code est valable 5 minutes.');
        });

        Password::sendResetLink($request->only('email'));

        return response()->json(['message' => 'Lien et code envoyés'], 200);
    }
  
    
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'nullable|string',
            'code' => 'nullable|digits:6',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        //  Vérification du code à 6 chiffres (OTP)
        if ($request->filled('code')) {
            $code = Cache::get('reset_code_' . $request->email);

            if (!$code || $code != $request->code) {
                return response()->json(['message' => 'Code invalide ou expiré'], 400);
            }

            // Si code valide → réinitialise directement le mot de passe
            $user = \App\Models\User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'Utilisateur introuvable'], 404);
            }

            $user->forceFill([
                'password' => Hash::make($request->password),
            ])->save();

            Cache::forget('reset_code_' . $request->email);

            \DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return response()->json(['message' => 'Mot de passe réinitialisé avec succès (via code).'], 200);
        }

        // Sinon, vérification standard via token Laravel
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                ])->save();
                Cache::forget('reset_code_' . $request->email);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }

}

