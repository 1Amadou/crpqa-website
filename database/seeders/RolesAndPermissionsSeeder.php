<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Réinitialiser les rôles et permissions en cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Création des permissions
        $permissions = [
            // Accès général
            'access admin panel',

            // Utilisateurs et rôles
            'manage users',
            'manage roles',

            // Pages statiques
            'view static pages',
            'create static pages',
            'edit static pages',
            'delete static pages',

            // Chercheurs
            'view researchers',
            'create researchers',
            'edit researchers',
            'delete researchers',

            // Publications
            'view publications',
            'create publications',
            'edit publications',
            'delete publications',
            'manage own publications',

            // Actualités
            'view news',
            'create news',
            'edit news',
            'delete news',
            'publish news',

            // Événements
            'view events',
            'create events',
            'edit events',
            'delete events',
            'publish events',

            // Partenaires
            'view partners',
            'create partners',
            'edit partners',
            'delete partners',

            // Axes de recherche
            'view research axes',
            'create research axes',
            'edit research axes',
            'delete research axes',

            // Paramètres du site
            'manage site settings',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Création des rôles et assignation des permissions

        // Super Administrateur : toutes les permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Administrateur']);
        $superAdminRole->syncPermissions(Permission::all());

        // Éditeur : accès admin + gestion contenus (sans gestion utilisateurs/roles)
        $editorPermissions = [
            'access admin panel',
            'view static pages', 'create static pages', 'edit static pages', 'delete static pages',
            'view researchers',   'create researchers',   'edit researchers',   'delete researchers',
            'view publications',  'create publications',  'edit publications',  'delete publications',
            'view news',          'create news',          'edit news',          'delete news',          'publish news',
            'view events',        'create events',        'edit events',        'delete events',        'publish events',
            'view partners',      'create partners',      'edit partners',      'delete partners',
            'view research axes', 'create research axes', 'edit research axes', 'delete research axes',
            'manage site settings',
        ];

        $editorRole = Role::firstOrCreate(['name' => 'Éditeur']);
        $editorRole->syncPermissions($editorPermissions);

        // Chercheur : accès admin + ne peut gérer que ses propres publications
        $researcherPermissions = [
            'access admin panel',
            'view publications',
            'manage own publications',
        ];

        $researcherRole = Role::firstOrCreate(['name' => 'Chercheur']);
        $researcherRole->syncPermissions($researcherPermissions);

        // Création des utilisateurs par défaut

        // Super Admin CRPQA
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@crpqa.ml'],
            [
                'name'     => 'Super Admin CRPQA',
                'password' => bcrypt('password123'),
            ]
        );
        $superAdminUser->syncRoles(['Super Administrateur']);

        // (Optionnel) Exemple de création d'un éditeur par défaut
        // $editorUser = User::firstOrCreate(
        //     ['email' => 'editor@crpqa.ml'],
        //     [
        //         'name'     => 'Éditeur CRPQA',
        //         'password' => bcrypt('password123'),
        //     ]
        // );
        // $editorUser->syncRoles(['Éditeur']);

        // (Optionnel) Exemple de création d'un chercheur par défaut
        // $researcherUser = User::firstOrCreate(
        //     ['email' => 'researcher@crpqa.ml'],
        //     [
        //         'name'     => 'Chercheur CRPQA',
        //         'password' => bcrypt('password123'),
        //     ]
        // );
        // $researcherUser->syncRoles(['Chercheur']);
    }
}
