<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Vous n'utilisez pas MustVerifyEmail
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // Importé

class User extends Authenticatable // Vous n'implémentez pas MustVerifyEmail ici
{
    use HasFactory, Notifiable, HasRoles; // HasRoles est déjà là, c'est bien

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'researcher_id', // Ce champ existe dans votre migration users, mais la relation est mieux gérée via Researcher
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array // Nouvelle syntaxe pour casts dans Laravel 10+
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ----- AJOUTEZ CETTE MÉTHODE POUR LA RELATION -----
    /**
     * Get the researcher profile associated with the user.
     * Un utilisateur (User) a un seul profil chercheur (Researcher).
     */
    public function researcherProfile() // Le nom doit correspondre à celui utilisé dans whereDoesntHave
    {
        // La clé étrangère 'user_id' est sur la table 'researchers'
        return $this->hasOne(Researcher::class, 'user_id');
    }
    // ----------------------------------------------------
}