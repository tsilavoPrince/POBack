<?php

namespace App\Http\Controllers;
    use Illuminate\Http\Request;
    use App\Models\Interview;
    use App\Models\Primaire;
    use App\Models\Notification;
    use App\Models\Secondaire;
    use App\Models\Tertiaire;
    use App\Models\Depense;
    use Carbon\Carbon;
use App\Models\Historique;

use DB;
    
    class InterviewController extends Controller
    {
        public function store(Request $request)
        {
            DB::beginTransaction();
    
            try {
                // Validation
                $data = $request->validate([
                    'nom' => 'required|string|max:255',
                    'nbrPersonne' => 'nullable|integer|min:0',
                    'nbrFemme' => 'nullable|integer|min:0',
                    'age02' => 'nullable|integer|min:0',
                    'age310' => 'nullable|integer|min:0',
                    'age10plus' => 'nullable|integer|min:0',
                    
                    'loyer' => 'nullable|string',
                    'montantLoyer' => 'nullable|numeric|min:0',
                    'ecolage' => 'nullable|string',
                    'nbrEcolage' => 'nullable|integer|min:0',
                ]);
    
                // Récupérer les valeurs
                    $nom = $data['nom'];
                    $nbrPersonne = $data['nbrPersonne'] ?? 0;
                    $nbrFemme = 0;
                    $age02 = $data['age02'] ?? 0;
                    $age310 = $data['age310'] ?? 0;
                    $age10plus = $data['age10plus'] ?? 0;
                    $depense = 0;
                    $budget = 0; // revenu mis par défaut à 2 Ar
                    $loyer = $data['loyer'];
                    $ecolage = $data['ecolage'];

                $montantLoyer = ($loyer == "oui") ? ($data['montantLoyer'] ?? 0) : 0;
                $ecolageTotal = ($ecolage == "oui") ? (($data['nbrEcolage'] ?? 0) * 25000) : 0;
    
                // Calculs
                $loka = 7000 * 30;
                $jiroRano = 25000;
                $sante = 200000;
    
                if ($nbrPersonne == 1) {
                    $sakafoTotal = 30 * 2800;
                    $vetementotal = 50000;
                } elseif ($nbrPersonne >= 2 && $nbrPersonne <= 3) {
                    $enfant = $age310;
                    $vetementotal = 70000;
                    if ($enfant <= 3) {
                        $sakaenfant = 5 * 2800;
                    } elseif ($enfant >= 4 && $enfant <= 6) {
                        $sakaenfant = 8 * 2800;
                    } else {
                        $sakaenfant = 15 * 2800;
                    }
                    $sakafoTotal = $sakaenfant + (30 * 2800);
                } elseif ($nbrPersonne >= 5 && $nbrPersonne <= 8) {
                    $sakafoTotal = 50 * 2800;
                    $vetementotal = 80000;
                } else {
                    $sakafoTotal = 60 * 2800;
                    $vetementotal = 85000;
                }
    
                $sakafozaza = $age02 * 150000;
                $nouriture = $sakafozaza + $sakafoTotal + $loka;
    
                $depPrimaire = $nouriture + $montantLoyer + $jiroRano + $sante + $ecolageTotal + $vetementotal;
    
                // Secondaire
                $agereste = 0;
                if($age10plus!=0 && $nbrPersonne>1){
                    $credit = $age10plus * 1500;
                    $creditotal = $credit * 30;
                  
                }
                elseif($nbrPersonne==1){
                      //$credit = $age10plus * 1500;
                      $creditotal = 2000 * 30;
                }
                else{
                    $creditotal =0;
                }
                
                $entretien = 50000;

                $aide = 150000;

                $depSecondaire = $creditotal + $entretien + $aide;
    
                // Tertiaire
                $loisir = 100000 * 4;
                $vaccance = 100000;
                $autres = 100000;
    
                $now = Carbon::now();
                $mois = $now->month;
                $volafety = in_array($mois, [1, 4, 6, 12]) ? 200000 : 0;
    
                $depTertaire = $loisir + $vaccance + $autres + $volafety;
    
                // Dépense totale du mois
                $depMoistotal = $depPrimaire + $depSecondaire +  $depTertaire;
    
                // Enregistrement dans interviews
                $interview = Interview::create([
                    'nom' => $nom,
                    'nbrPersonne' => $nbrPersonne,
                    'nbrFemme' => $nbrFemme,
                    'age02' => $age02,
                    'age310' => $age310,
                    'age10plus' => $age10plus,
                    'depense' => $depense,
                    'budget' => $budget,
                    'loyer' => $loyer,
                    'depPrimaire' => $depPrimaire,
                    'depSecondaire' => $depSecondaire,
                    'depTertaire' => $depTertaire,
                    'montantLoyer' => $montantLoyer,
                    'ecolage' => $ecolage,
                    'nbrEcolage' => $data['nbrEcolage'] ?? 0,
                ]);
    
                // Enregistrement dans primaire
                Primaire::create([
                    'nom' => $nom,
                    'nouriture' => $nouriture,
                    'loyer' => $montantLoyer,
                    'energie' => $jiroRano,
                    'sante' => $sante,
                    'ecolage' => $ecolageTotal,
                    'vetement' => $vetementotal,
                ]);
    
                // Enregistrement dans secondaire
                Secondaire::create([
                    'nom' => $nom,
                    'credit' => $creditotal,
                    'entretien' => $entretien,
                    'aide' => $aide,
                ]);
    
                // Enregistrement dans tertiaire
                Tertiaire::create([
                    'nom' => $nom,
                    'loisir' => $loisir,
                    'vaccance' => $vaccance,
                    'autres' => $autres,
                    'fety' => $volafety,
                ]);
    
                // Enregistrement dans depense (nouveau)
                Depense::create([
                    'nom' => $nom,
                    'total' => $depMoistotal,
                ]);

                //
                

                // Insertion dans la table historique
                $moisLettre = $now->locale('fr')->translatedFormat('F');
                $moisLettre = ucfirst($moisLettre);

                // Vérifie si une ligne existe déjà
                $existe = Historique::where('nom', $nom)
                                    ->where('mois', $moisLettre)
                                    ->exists();

                if (!$existe) {
                    Historique::create([
                        'nom' => $nom,
                        'mois' => $moisLettre,
                        'totalDep' => $depMoistotal,
                    ]);
                }

                DB::commit();
    
                return response()->json([
                    'message' => 'Insertion réussie avec succès !',
                    'depense_mensuelle_totale' => $depMoistotal
                ]);
    
            } catch (\Exception $e) {
                DB::rollBack();
    
                return response()->json([
                    'message' => 'Erreur : ' . $e->getMessage()
                ], 500);
            }
        }
    

     ///affichage budegt
     public function getBudget(Request $request)
{
    $nom = $request->user()->nom;

    // Récupération du budget pour l'utilisateur connecté
    $budget = Interview::where('nom', $nom)->value('budget');  // On suppose que la colonne 'budget' existe

    return response()->json([
        'budget' => $budget  // Retourne le budget sous forme de nombre
    ]);
}

//insertion dudget
public function updateBudget(Request $request)
{
    $request->validate([
        'budget' => 'required|numeric|min:0',
    ]);

    $user = $request->user();
    $nom = $user->nom;

    // Recherche de l'enregistrement par nom de l'utilisateur connecté
    $interview = Interview::where('nom', $nom)->first();

    if (!$interview) {
        return response()->json([
            'message' => 'Interview non trouvée pour ce nom.'
        ], 404);
    }

    // Récupération de l'ancien budget
    $ancienBudget = $interview->budget;

    // Calcul du nouveau budget (ancien + nouveau)
    $nouveauBudget = $ancienBudget + $request->budget;

    // Mise à jour du budget dans la base de données
    $interview->budget = $nouveauBudget;
    $interview->save();

    // Création de la notification avec le budget mis à jour
    $texte = "Vous avez ajouté " . $request->budget . " Ar à votre budget. Nouveau total : " . $nouveauBudget . " Ar.";
    $this->creerNotification($nom, $texte);

    return response()->json([
        'message' => 'Budget mis à jour avec succès.',
        'budget_total' => $nouveauBudget,
        'interview' => $interview,
    ]);
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
