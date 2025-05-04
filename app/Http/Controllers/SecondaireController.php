<?php

namespace App\Http\Controllers;
use App\Models\Interview;
use Illuminate\Support\Str;
use App\Models\Secondaire;
use App\Models\Depense;

use App\Models\Historique;
use Carbon\Carbon;
use App\Models\Notification;
use Illuminate\Http\Request;

class SecondaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function modifierTotalDepPourMois($nom, $total)
{

// Exemple : obtenir le mois en lettres (avril, mai, etc.)
    $mois = Carbon::now()->locale('fr')->translatedFormat('F');

    $historique = Historique::where('nom', $nom)
                            ->where('mois', $mois)
                            ->first();

    if (!$historique) {
        return response()->json(['message' => 'Aucun historique trouvé pour ce nom et ce mois.'], 404);
    }

    // Mise à jour de la colonne totalDep
    $historique->totalDep = $total;
    $historique->save();

    return response()->json([
        'message' => 'Total des dépenses mis à jour avec succès.',
        'historique' => $historique,
    ]);
}
    public function secondaire(Request $request)
    {
    
            $user = $request->user();

        // Récupérer les données basées sur le nom de l'utilisateur
        $secondaire = Secondaire::where('nom', $user->nom)->latest()->first();

        if (!$secondaire) {
            return response()->json([], 404);
        }

        return response()->json([
            'credit'   => $secondaire->credit,
            'entretien'=> $secondaire->entretien,
            'aide'     => $secondaire->aide,
        ]);

    }

    //modification

public function update(Request $request)
{
    $user = $request->user();
    $nom = $user->nom;

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé'], 404);
    }

    $secondaire = Secondaire::where('nom', $nom)->first();

    if (!$secondaire) {
        return response()->json(['message' => 'Dépenses secondaires non trouvées'], 404);
    }

    $sommePourInterview = 0;

    foreach ($request->depenses as $depense) {
        $categorie = Str::ascii(strtolower($depense['categorie']));
        $montant = $depense['montant'];

        switch ($categorie) {
            case 'credit':
                $secondaire->credit = $montant;
                $sommePourInterview += $montant;
                break;

            case 'entretien des machines':
            case 'entretiendesmachines':
            case 'entretien':
                $secondaire->entretien = $montant;
                $sommePourInterview += $montant;
                break;

            case 'aide personne':
            case 'aidepersonne':
            case 'aide':
                $secondaire->aide = $montant;
                $sommePourInterview += $montant;
                break;
        }
    }

    $secondaire->save();

    // Mise à jour dans la table interviews → colonne depSecondaire
    $interview = Interview::where('nom', $nom)->first();
    if ($interview) {
        $interview->depSecondaire += $sommePourInterview;
        $interview->save();

        // ✅ Récupérer les 3 types de dépenses
        $depPrimaire = $interview->depPrimaire ?? 0;
        $depSecondaire = $interview->depSecondaire ?? 0;
        $depTertaire = $interview->depTertaire ?? 0;

        $totalDepenses = $depPrimaire + $depSecondaire + $depTertaire;

        // ✅ Mise à jour dans la table depenses → colonne total
        $depense = Depense::where('nom', $nom)->first();
        if ($depense) {
            $depense->total = $totalDepenses;
            $depense->save();
        }

        // ✅ Notification
        $texte = "Vous avez modifié votre dépense secondaire avec " . $totalDepenses . " Ar pour ce mois.";
        $this->creerNotification($nom, $texte);

        // ✅ Appel de la fonction de mise à jour dans 'historiques'
        $mois = Carbon::now()->format('Y-m'); // ou en lettres : ->translatedFormat('F')
        $this->modifierTotalDepPourMois($nom, $totalDepenses);
    }

    return response()->json(['message' => 'Dépenses secondaires et total mises à jour avec succès']);
}

    

    //notification
    public function creerNotification($nom, $texte)
    {
        Notification::create([
            'nom' => $nom,
            'notification' => $texte,
        ]);

        return response()->json(['message' => 'Notification enregistrée avec succès']);
    }
    }
