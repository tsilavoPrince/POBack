<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validation des données envoyées
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Recherche de l'utilisateur par email
        $utilisateur = Utilisateur::where('email', $request->email)->first();

        // Vérification de l'utilisateur et du mot de passe
        if (!$utilisateur || !Hash::check($request->password, $utilisateur->password)) {

            return response()->json([
                'message' => 'Email ou mot de passe incorrect.'
            ], 401);
        }

        // Génération d'un token d'authentification
        $token = $utilisateur->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie !',
            'utilisateur' => $utilisateur,
            'token' => $token
        ]);
    }
    public function logout(Request $request) {
        $request->utilisateur()->tokens()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }
}
