<?php

namespace App\Http\Controllers;
use App\Models\Interview;
use Illuminate\Support\Str;
use App\Models\Primaire;
use App\Models\Notification;
use App\Models\Depense;
use App\Models\Historique;
use Carbon\Carbon;
use Illuminate\Http\Request;
class PrimaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Récupérer les données basées sur le nom de l'utilisateur
        $primaire = Primaire::where('nom', $user->nom)->latest()->first();

        if (!$primaire) {
            return response()->json([], 404);
        }

        return response()->json([
            'nouriture' => $primaire->nouriture,
            'loyer'     => $primaire->loyer,
            'energie'   => $primaire->energie,
            'sante'     => $primaire->sante,
            'ecolage'   => $primaire->ecolage,
            'vetement'  => $primaire->vetement,
        ]);
    }

//modification du total dep


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

    //modifaction

 

    public function update(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'depenses' => 'required|array',
            'depenses.*.categorie' => 'required|string',
            'depenses.*.montant' => 'required|numeric',
        ]);
    
        $user = $request->user();
        $nom = $user->nom;
    
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }
    
        $primaire = Primaire::where('nom', $nom)->first();
    
        if (!$primaire) {
            return response()->json(['message' => 'Dépenses primaires non trouvées'], 404);
        }
    
        $sommePourInterview = 0;
    
        foreach ($request->depenses as $depense) {
            $categorie = strtolower($depense['categorie']);
            $montant = $depense['montant'];
    
            switch ($categorie) {
                case 'nourriture':
                    $primaire->nouriture = $montant;
                    $sommePourInterview += $montant;
                    break;
                case 'loyer':
                    $primaire->loyer = $montant;
                    $sommePourInterview += $montant;
                    break;
                case 'énergie':
                case 'energie':
                    $primaire->energie = $montant;
                    $sommePourInterview += $montant;
                    break;
                case 'santé':
                case 'sante':
                    $primaire->sante = $montant;
                    $sommePourInterview += $montant;
                    break;
                case 'écolage':
                case 'ecolage':
                    $primaire->ecolage = $montant;
                    $sommePourInterview += $montant;
                    break;
                case 'vêtements':
                case 'vetement':
                    $primaire->vetement = $montant;
                    $sommePourInterview += $montant;
                    break;
            }
        }
    
        $primaire->save();
    
        // Mise à jour de depPrimaire dans interviews
        $interview = Interview::where('nom', $nom)->first();
        if ($interview) {
            $interview->depPrimaire += $sommePourInterview;
            $interview->save();
    
            // ✅ Calcul du total
            $depPrimaire = $interview->depPrimaire ?? 0;
            $depSecondaire = $interview->depSecondaire ?? 0;
            $depTertaire = $interview->depTertaire ?? 0;
    
            $totalDepenses = $depPrimaire + $depSecondaire + $depTertaire;
    
            // ✅ Mise à jour dans la table depenses
            $depense = Depense::where('nom', $nom)->first();
            if ($depense) {
                $depense->total = $totalDepenses;
                $depense->save();
            }
    
            // ✅ Notification
            $texte = "Vous avez modifié votre dépense primaire avec " . $totalDepenses . " Ar pour ce mois.";
            $this->creerNotification($nom, $texte);
    
            // ✅ Appel de la fonction pour mettre à jour la table historiques
            
            $this->modifierTotalDepPourMois($nom, $totalDepenses);
        }
    
        return response()->json(['message' => 'Dépenses primaires et total mises à jour avec succès']);
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
