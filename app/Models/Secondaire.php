<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secondaire extends Model
{
    use HasFactory;
    protected $fillable = [
        'credit', 'entretien', 'nom', 'aide',
    ];
}
