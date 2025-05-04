<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depense;
use App\Models\Models;
use App\Models\Interview; // Assurez-vous que le chemin d'importation est correct
use Illuminate\Support\Facades\Auth;
use App\Models\Historique;

class DepenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    try {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $nom = $user->nom; // ou $user->name si la colonne s'appelle comme ça
        $interviews = Interview::where('nom', $nom)->get(); // Recherche dans la table "interviews"

        if ($interviews->isEmpty()) {
            return response()->json(['message' => 'Aucune interview trouvée pour cet utilisateur'], 404);
        }

        // Si des interviews sont trouvées, nous récupérons les valeurs de depPrimaire, depSecondaire, depTertiaire
        $resultats = $interviews->map(function ($interview) {
            return [
                'nom' => $interview->nom,
                'depPrimaire' => $interview->depPrimaire,
                'depSecondaire' => $interview->depSecondaire,
                'depTertaire' => $interview->depTertaire,
            ];
        });

        return response()->json($resultats);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erreur serveur',
            'message' => $e->getMessage()
        ], 500);
    }
}

//

public function graphe(Request $request)
{
    try {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        $nom = $user->nom; // ou 'name' selon ta base

        // On récupère les mois et les totaux depuis la table historiques
        $historiques = Historique::where('nom', $nom)
            ->select('mois', 'totalDep')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json($historiques);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erreur serveur',
            'message' => $e->getMessage()
        ], 500);
    }
}


//total 

public function getTotal(Request $request)
{
    $user = $request->user();

    // Calcul du total des dépenses pour l'utilisateur connecté
    $total = Depense::where('nom', $user->nom)->sum('total');  // Assure-toi d'utiliser 'sum' pour récupérer le total

    return response()->json([
        'total' => $total  // Retourne le total des dépenses sous forme de nombre
    ]);
}



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Depense  $depense
     * @return \Illuminate\Http\Response
     */
    public function show(Depense $depense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Depense  $depense
     * @return \Illuminate\Http\Response
     */
    public function edit(Depense $depense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Depense  $depense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Depense $depense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Depense  $depense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Depense $depense)
    {
        //
    }
}
