<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Assurez-vous que le modèle User est importé

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Réinitialiser les rôles et permissions mis en cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Définition des Permissions
        // (Vous pouvez être aussi granulaire que nécessaire)

        // Permissions pour les utilisateurs
        Permission::firstOrCreate(['name' => 'manage users']); // Créer, voir, modifier, supprimer des utilisateurs

        // Permissions pour les rôles (si vous voulez gérer les rôles eux-mêmes depuis l'interface plus tard)
        // Permission::firstOrCreate(['name' => 'manage roles']);

        // Permissions pour les contenus
        Permission::firstOrCreate(['name' => 'manage static pages']);
        Permission::firstOrCreate(['name' => 'manage researchers']); // Gérer les profils chercheurs (pas les comptes utilisateurs chercheurs)
        Permission::firstOrCreate(['name' => 'manage publications']); // Gérer TOUTES les publications
        Permission::firstOrCreate(['name' => 'manage own publications']); // Gérer SES PROPRES publications
        Permission::firstOrCreate(['name' => 'manage news']);
        Permission::firstOrCreate(['name' => 'manage events']);
        Permission::firstOrCreate(['name' => 'manage partners']);

        // Permissions pour les paramètres
        Permission::firstOrCreate(['name' => 'manage site settings']);

        // Permissions d'accès général au panneau d'administration
        Permission::firstOrCreate(['name' => 'access admin panel']);


        // Définition des Rôles et assignation des permissions

        // Rôle: Super Administrateur - a toutes les permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Administrateur']);
        // $superAdminRole->givePermissionTo(Permission::all()); // Donne toutes les permissions existantes
        // Ou, de manière plus explicite (recommandé si vous voulez un contrôle plus fin à l'avenir)
        $superAdminRole->givePermissionTo([
            'access admin panel',
            'manage users',
            'manage roles',
            'manage static pages',
            'manage researchers',
            'manage publications', // Le Super Admin peut gérer toutes les publications
            'manage news',
            'manage events',
            'manage partners',
            'manage site settings',
        ]);


        // Rôle: Éditeur - gère les contenus généraux
        $editorRole = Role::firstOrCreate(['name' => 'Éditeur']);
        $editorRole->givePermissionTo([
            'access admin panel',
            'manage static pages',
            'manage news',
            'manage events',
            'manage partners', // Un éditeur pourrait aussi gérer les partenaires
            'manage publications',
        ]);

        // Rôle: Chercheur - gère ses propres publications
        $researcherRole = Role::firstOrCreate(['name' => 'Chercheur']);
        $researcherRole->givePermissionTo([
            'access admin panel', // Pour accéder à son interface de gestion de publications
            'manage own publications', // Permission clé
            // Optionnel: 'view own profile' ou 'edit own profile' si on développe cette fonctionnalité
        ]);


        // Création d'un utilisateur Super Administrateur par défaut (si aucun n'existe)
        // Assurez-vous de changer l'email et le mot de passe pour la production
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@crpqa.ml'], // Email unique pour la recherche
            [
                'name' => 'Super Admin CRPQA',
                'password' => bcrypt('password123'), // Changez ce mot de passe !
            ]
        );
        $superAdminUser->assignRole($superAdminRole);

        // Vous pouvez créer d'autres utilisateurs par défaut ici si nécessaire
        // Par exemple, un Éditeur de test
        /*
        $editorUser = User::firstOrCreate(
            ['email' => 'editor@crpqa.ml'],
            [
                'name' => 'Éditeur Contenu',
                'password' => bcrypt('password123'),
            ]
        );
        $editorUser->assignRole($editorRole);
        */
    }
}