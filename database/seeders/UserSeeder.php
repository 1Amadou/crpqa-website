<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Importer le modèle User
use Illuminate\Support\Facades\Hash; // Importer la façade Hash pour crypter le mot de passe

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Vérifie si l'utilisateur admin existe déjà par son email avant de le créer
        // pour éviter les doublons si on lance le seeder plusieurs fois.
        User::firstOrCreate(
            ['email' => 'admin@crpqa.test'], // Critère de recherche unique
            [
                'name' => 'Admin CRPQA',
                'password' => Hash::make('password'), // Mot de passe crypté. Changez 'password' pour quelque chose de plus sûr en production !
                // 'email_verified_at' => now(), // Décommentez si vous voulez que l'email soit marqué comme vérifié
            ]
        );

        // Vous pourriez ajouter d'autres utilisateurs ici si nécessaire
        // Par exemple, un utilisateur avec un rôle d'éditeur :
        // User::firstOrCreate(
        //     ['email' => 'editor@crpqa.test'],
        //     [
        //         'name' => 'Editeur CRPQA',
        //         'password' => Hash::make('password'),
        //     ]
        // );
    }
}