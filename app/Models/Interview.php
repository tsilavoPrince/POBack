<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;
  protected $fillable = [
        'nom',
        'nbrPersonne',
        'nbrFemme',
        'age02',
        'depPrimaire',
        'depSecondaire',
        'depTertaire',
        'age310',
        'age10plus',
        'depense',
        'budget',
        'loyer',
        'montantLoyer',
        'ecolage',
        'nbrEcolage',
    ];
}
