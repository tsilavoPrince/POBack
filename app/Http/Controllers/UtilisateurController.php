<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class UtilisateurController extends Controller
{
    public function store(Request $request)
    {
        // Validation des données envoyées
        $validatedData =$request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs',
            'password' => 'required|min:6'
        ]);

        // Création et insertion de l'utilisateur dans la base de données
        $utilisateur = Utilisateur::create([
            'nom' => $validatedData['nom'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // crypter le mot de passe
        ]);

        // Retourner une réponse JSON
        return response()->json([
            'message' => 'Utilisateur créé avec succès !',
            'utilisateur' => $utilisateur
        ], 201);
    }
}

