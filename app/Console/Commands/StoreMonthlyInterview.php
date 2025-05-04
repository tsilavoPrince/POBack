<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Interview;
use App\Models\Depense;
use Carbon\Carbon;

class StoreMonthlyInterview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interview:monthly-store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enregistre automatiquement les interviews et les dépenses chaque mois';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Exemple de données (à remplacer par des vraies données d’utilisateurs si disponibles)
        $data = [
            [
                'nom' => 'Jean',
                'nbrPersonne' => 4,
                'budget' => 1000000,
                'depense' => 900000,
            ],
            // Tu peux ajouter d'autres personnes ici...
        ];

        $now = Carbon::now();
        $anneeActuelle = $now->year;
        $moisNombre = $now->month;
        $bissextile = ($anneeActuelle % 4 == 0 && ($anneeActuelle % 100 != 0 || $anneeActuelle % 400 == 0));
        $jour = ($moisNombre == 2) ? ($bissextile ? 29 : 28) : (in_array($moisNombre, [4, 6, 9, 11]) ? 30 : 31);

        foreach ($data as $item) {
            $nom = $item['nom'];
            $nbrPersonne = $item['nbrPersonne'];
            $budget = $item['budget'];
            $userDepense = $item['depense'];

            // Vérifie si l'interview existe déjà ce mois
            $dejaExiste = Interview::where('nom', $nom)
                ->whereMonth('created_at', $moisNombre)
                ->whereYear('created_at', $anneeActuelle)
                ->exists();

            if ($dejaExiste) {
                $this->info("Interview déjà enregistrée pour $nom ce mois-ci.");
                continue;
            }

            // Calcul Nourriture + Loka
            $nouriture1 = $nbrPersonne * 0.6;
            $kg = ($nouriture1 * 1) / 3.5;
            $parkg = $kg * 3;
            $nouriture = $parkg * $jour + (5000 * $jour);

            $reste = $budget - $nouriture;
            if ($reste < 0) {
                $this->warn("Budget insuffisant pour $nom, pas d'insertion.");
                continue;
            }

            $economie = $reste * 0.20;
            $restePourDepenses = $reste * 0.80;

            $logements = $restePourDepenses / 3;
            $jirama = $restePourDepenses / 3;
            $autres = $restePourDepenses / 3;

            $depenseTotal = $nouriture + $logements + $jirama + $autres;

            // Ajustement si différence avec dépense entrée
            $delta = $userDepense - $depenseTotal;
            if ($delta != 0) {
                $autres += $delta;
                $depenseTotal = $nouriture + $logements + $jirama + $autres;
            }

            // Création Interview
            Interview::create([
                'nbrPersonne' => $nbrPersonne,
                'depense' => $userDepense,
                'budget' => $budget,
                'nom' => $nom,
            ]);

            // Création Depense
            Depense::create([
                'nom' => $nom,
                'nouriture' => $nouriture,
                'logements' => $logements,
                'jirama' => $jirama,
                'autres' => $autres,
                'total' => $depenseTotal,
                'economie' => $economie
            ]);

            $this->info("Insertion mensuelle réussie pour $nom.");
        }

        return Command::SUCCESS;
    }
}
