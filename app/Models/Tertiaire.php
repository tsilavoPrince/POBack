<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tertiaire extends Model
{
    use HasFactory;
    protected $fillable = [
        'loisir', 'vaccance', 'nom', 'autres','fety'
    ];
}
