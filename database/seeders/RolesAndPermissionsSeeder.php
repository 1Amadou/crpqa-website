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
            'view static_pages',
            'create static_pages',
            'edit static_pages',
            'delete static_pages',
            // Si vous voulez une permission globale pour les pages statiques :
            'manage static_pages', 

            // Chercheurs
            'view researchers',
            'create researchers',
            'edit researchers',
            'delete researchers',
            // Si vous voulez une permission globale pour les chercheurs :
            'manage researchers',

            // Publications
            'view publications',
            'create publications',
            'edit publications',
            'delete publications',
            'manage own publications', // Pour un chercheur qui gère ses propres publications
            // Si vous voulez une permission globale pour les publications :
            'manage publications',

            // Actualités
            'view news',
            'create news',
            'edit news',
            'delete news',
            'publish news',
            // Si vous voulez une permission globale pour les actualités :
            'manage news',

            // Événements
            'view events',
            'create events',
            'edit events',
            'delete events',
            'publish events', // Si vous avez des fonctionnalités de publication pour les événements
            // Si vous voulez une permission globale pour les événements :
            'manage events',
            // Permissions spécifiques pour les inscriptions aux événements
            'view event_registrations',
            'manage event_registrations', // Pour créer, modifier, supprimer les inscriptions
            'export event_registrations', // Pour les exports PDF/Excel
            'import event_registrations', // Pour l'import de participants

            // Partenaires
            'view partners',
            'create partners',
            'edit partners',
            'delete partners',
            // Si vous voulez une permission globale pour les partenaires :
            'manage partners',

            // Axes de recherche
            'view research_axes',
            'create research_axes',
            'edit research_axes',
            'delete research_axes',
            // Si vous voulez une permission globale pour les axes de recherche :
            'manage research_axes',

            // Paramètres du site
            'manage site settings',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Création des rôles et assignation des permissions

        // Super Administrateur : toutes les permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Administrateur', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all()); // Donne toutes les permissions créées ci-dessus

        // Éditeur : accès admin + gestion contenus (sans gestion utilisateurs/roles)
        $editorPermissions = [
            'access admin panel',
            'view static_pages', 'create static_pages', 'edit static_pages', 'delete static_pages',
            'view researchers',  'create researchers',  'edit researchers',  'delete researchers',
            'view publications', 'create publications', 'edit publications', 'delete publications',
            'view news',         'create news',         'edit news',         'delete news',         'publish news',
            'view events',       'create events',       'edit events',       'delete events',       'publish events',
            'view event_registrations', 'manage event_registrations', 'export event_registrations', 'import event_registrations',
            'view partners',     'create partners',     'edit partners',     'delete partners',
            'view research_axes','create research_axes','edit research_axes','delete research_axes',
            'manage site settings',
        ];
        $editorRole = Role::firstOrCreate(['name' => 'Éditeur', 'guard_name' => 'web']);
        $editorRole->syncPermissions($editorPermissions);

        // Chercheur : accès admin + ne peut gérer que ses propres publications (exemple)
        $researcherPermissions = [
            'access admin panel',
            'view publications',        // Peut voir toutes les publications
            'manage own publications',  // Permission pour gérer ses propres publications
            // Un chercheur pourrait aussi avoir besoin de voir les événements, les axes, etc.
            'view events',
            'view research_axes',
        ];
        $researcherRole = Role::firstOrCreate(['name' => 'Chercheur', 'guard_name' => 'web']);
        $researcherRole->syncPermissions($researcherPermissions);
        
        // Membre (Participant d'événement par exemple, s'il a un compte) - peut-être pas d'accès admin
        $memberRole = Role::firstOrCreate(['name' => 'Membre', 'guard_name' => 'web']);
        // $memberRole->givePermissionTo(['view public content']); // Exemple de permission si besoin

        // Assignation du rôle Super Administrateur au premier utilisateur ou à un utilisateur spécifique
        // (La création des utilisateurs est dans UserSeeder maintenant)
        $user = User::where('email', 'superadmin@crpqa.ml')->first();
        if ($user) {
            $user->assignRole('Super Administrateur');
        } else {
            // Si l'utilisateur superadmin n'est pas créé par UserSeeder, vous pouvez le créer ici
            $superAdminUser = User::firstOrCreate(
                ['email' => 'superadmin@crpqa.ml'],
                [
                    'name'     => 'Super Admin CRPQA',
                    'password' => bcrypt('password123'), // Changez ce mot de passe !
                    'email_verified_at' => now(),
                ]
            );
            $superAdminUser->assignRole('Super Administrateur');
            $this->command->info('Default Super Admin user created and role assigned.');
        }
    }
}