<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    use HasApiTokens;
    //use HasApiTokens ,Notifiable;
    use HasFactory;

    //protected $table = 'utilisateurs'; // Spécifie le nom de la table

    protected $fillable = ['nom', 'email', 'password'];
    // Colonnes autorisées pour l'insertion

    protected $hidden = ['password'];
}
