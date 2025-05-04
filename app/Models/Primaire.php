<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Primaire extends Model
{
    use HasFactory;
    protected $fillable = [
        'nouriture', 'loyer', 'nom', 'energie','sante' ,'ecolage' , 'vetement'
    ];
}
