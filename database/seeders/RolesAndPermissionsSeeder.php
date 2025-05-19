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

        $this->command->info('Creating permissions...');

        // Permissions pour les utilisateurs
        Permission::firstOrCreate(['name' => 'manage users']);

        // Permissions pour les rôles
        Permission::firstOrCreate(['name' => 'manage roles']); // DÉCOMMENTÉ !

        // Permissions pour les contenus
        Permission::firstOrCreate(['name' => 'manage static pages']);
        Permission::firstOrCreate(['name' => 'manage researchers']);
        Permission::firstOrCreate(['name' => 'manage publications']);
        Permission::firstOrCreate(['name' => 'manage own publications']);
        Permission::firstOrCreate(['name' => 'manage news']);
        Permission::firstOrCreate(['name' => 'manage events']);
        Permission::firstOrCreate(['name' => 'manage partners']);
        Permission::firstOrCreate(['name' => 'manage research axes']); // Ajout potentiel si nécessaire

        // Permissions pour les paramètres
        Permission::firstOrCreate(['name' => 'manage site settings']);

        // Permissions d'accès général au panneau d'administration
        Permission::firstOrCreate(['name' => 'access admin panel']);

        $this->command->info('Permissions created successfully.');
        $this->command->info('Creating roles and assigning permissions...');

        // Rôle: Super Administrateur
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Administrateur']);
        // Assigner toutes les permissions créées explicitement
        // (Permission::all() peut être problématique si des permissions "fantômes" existent)
        $allDefinedPermissions = [
            'manage users', 'manage roles', 'manage static pages', 'manage researchers',
            'manage publications', 'manage own publications', 'manage news', 'manage events',
            'manage partners', 'manage research axes', 'manage site settings', 'access admin panel'
        ];
        // Filtrer pour s'assurer que seules les permissions réellement créées sont assignées
        $existingPermissions = Permission::whereIn('name', $allDefinedPermissions)->pluck('name')->toArray();
        $superAdminRole->syncPermissions($existingPermissions); // Utiliser syncPermissions est plus sûr
        $this->command->info('Role "Super Administrateur" created and permissions synced.');


        // Rôle: Administrateur (si différent du Super Administrateur)
        // Si vous avez besoin d'un rôle 'Administrateur' distinct avec moins de droits que le Super Admin,
        // vous pouvez le définir ici. Sinon, le Super Administrateur couvre tout.
        // Exemple :
        /*
        $adminRole = Role::firstOrCreate(['name' => 'Administrateur']);
        $adminRole->syncPermissions([
            'access admin panel',
            'manage users', // Peut-être pas 'manage roles' pour un admin standard
            'manage static pages',
            'manage researchers',
            'manage publications',
            'manage news',
            'manage events',
            'manage partners',
            'manage site settings',
        ]);
        $this->command->info('Role "Administrateur" created and permissions synced.');
        */


        // Rôle: Éditeur
        $editorRole = Role::firstOrCreate(['name' => 'Éditeur']);
        $editorRole->syncPermissions([ // Utiliser syncPermissions
            'access admin panel',
            'manage static pages',
            'manage news',
            'manage events',
            'manage partners',
            'manage publications', // Un éditeur peut gérer toutes les publications
            // 'manage researchers', // Peut-être pas les profils chercheurs eux-mêmes
        ]);
        $this->command->info('Role "Éditeur" created and permissions synced.');

        // Rôle: Chercheur
        $researcherRole = Role::firstOrCreate(['name' => 'Chercheur']);
        $researcherRole->syncPermissions([ // Utiliser syncPermissions
            'access admin panel',
            'manage own publications',
        ]);
        $this->command->info('Role "Chercheur" created and permissions synced.');

        $this->command->info('Roles and permissions seeding completed.');
        $this->command->info('Creating default users...');

        // Création d'un utilisateur Super Administrateur par défaut
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@crpqa.ml'],
            [
                'name' => 'Super Admin CRPQA',
                'password' => bcrypt('password123'),
            ]
        );
        $superAdminUser->assignRole('Super Administrateur'); // Assigner par nom de rôle est simple
        $this->command->info("User 'Super Admin CRPQA' created and assigned 'Super Administrateur' role.");

        // Vous pouvez ajouter la création d'autres utilisateurs ici si vous le souhaitez

        $this->command->info('Default users creation completed.');
    }
}