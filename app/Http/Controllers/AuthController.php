<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validation des données envoyées
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Recherche de l'utilisateur par email
            $utilisateur = Utilisateur::where('email', $validated['email'])->first();

            // Vérification du mot de passe
            if (!$utilisateur || !Hash::check($validated['password'], $utilisateur->password)) {
                return response()->json([
                    'message' => 'Email ou mot de passe incorrect.'
                ], 401);
            }

            // Génération du token
            $token = $utilisateur->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Connexion réussie !',
                'utilisateur' => $utilisateur,
                'token' => $token
            ]);
        } catch (ValidationException $e) {
            // Gestion des erreurs de validation
            return response()->json([
                'message' => 'Les données fournies sont invalides.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            // Gestion des autres erreurs inattendues
            return response()->json([
                'message' => 'Une erreur s’est produite lors de la connexion.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Suppression des tokens de l'utilisateur connecté
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Déconnexion réussie'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la déconnexion.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function me(Request $request)
    {
        return response()->json([
            'utilisateur' => $request->user()
        ]);
    }
    //
    public function getNomUtilisateur(Request $request)
{
    $nom = $request->user()->nom; // Ou auth()->user()->nom

    return response()->json([
        'nom_utilisateur' => $nom
    ]);

}
public function profile(Request $request)
{
    try {
        $utilisateur = $request->user();

        // Debug rapide pour voir si l'utilisateur est bien trouvé
        if (!$utilisateur) {
            return response()->json([
                'message' => 'Utilisateur non trouvé via le token.'
            ], 401);
        }

        $interview = \App\Models\Interview::where('nom', $utilisateur->nom)->first();

        if (!$interview) {
            return response()->json([
                'message' => 'Aucune interview trouvée pour cet utilisateur.'
            ], 404);
        }

        return response()->json([
            'nom' => $utilisateur->nom,
            'email' => $utilisateur->email,
            'nbrpersonne' => $interview->nbrPersonne,
            'budget' => $interview->budget,
            'depense' => $interview->depense
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur serveur',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
}


public function notifications()
{
    return $this->hasMany(Notification::class);
}
}
