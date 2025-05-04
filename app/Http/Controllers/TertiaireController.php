<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use Illuminate\Support\Str;
use App\Models\Tertiaire;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Historique;
use Carbon\Carbon;
use App\Models\Depense;

class TertiaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tertiaire(Request $request)
    {
        $user = $request->user();

        // Récupérer les données basées sur le nom de l'utilisateur
        $tertaire = Tertiaire::where('nom', $user->nom)->latest()->first();

        if (!$tertaire) {
            return response()->json([], 404);
        }

        return response()->json([
            'loisir'   => $tertaire->loisir,
            'vaccance'  => $tertaire->vaccance,
            'autres'   => $tertaire->autres,
            'fety'     => $tertaire->fety,
        ]);
    }
    

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
    //modification 

   

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
    
        $tertiaire = Tertiaire::where('nom', $request->nom)->first();
    
        if (!$tertiaire) {
            return response()->json([
                'message' => 'Utilisateur tertiaire non trouvé.'
            ], 404);
        }
    
        $sommePourInterview = 0;
    
        foreach ($request->depenses as $depense) {
            $categorie = strtolower($depense['categorie']);
            $montant = $depense['montant'];
    
            switch ($categorie) {
                case 'budget de loisir':
                    $tertiaire->loisir = $montant;
                    $sommePourInterview += $montant;
                    break;
                case 'vacances':
                    $tertiaire->vaccance = $montant;
                    $sommePourInterview += $montant;
                    break;
                case 'autres':
                    $tertiaire->autres = $montant;
                    $sommePourInterview += $montant;
                    break;
                case 'fete (si le mois contient une fete)':
                    $tertiaire->fety = $montant;
                    $sommePourInterview += $montant;
                    break;
            }
        }
    
        $tertiaire->save();
    
        $utilisateurNom = auth()->user()->nom;
    
        $interview = Interview::where('nom', $utilisateurNom)->first();
    
        if ($interview) {
            $interview->depTertaire += $sommePourInterview;
            $interview->save();
    
            $depPrimaire = $interview->depPrimaire ?? 0;
            $depSecondaire = $interview->depSecondaire ?? 0;
            $depTertaire = $interview->depTertaire ?? 0;
    
            $total = $depPrimaire + $depSecondaire + $depTertaire;
    
            $depense = Depense::where('nom', $utilisateurNom)->first();
            if ($depense) {
                $depense->total = $total;
                $depense->save();
            }
    
            $texte = "Vous avez modifié votre dépense tertiaire avec " . $total . " Ar pour ce mois.";
            $this->creerNotification($nom, $texte);
    
            $this->modifierTotalDepPourMois($nom,  $total);
        }
    
        return response()->json([
            'message' => 'Dépenses tertiaires et total mises à jour avec succès.'
        ], 200);
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
