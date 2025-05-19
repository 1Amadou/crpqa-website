<?php

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicationPolicy
{
    use HandlesAuthorization;

    /**
     * Autorisation "globale" pour les super admins (peuvent tout faire).
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Administrateur')) {
            return true;
        }
        return null; // Laisse les autres méthodes de la policy décider
    }

    /**
     * Determine whether the user can view any models.
     * (Utilisé pour la page d'index)
     */
    public function viewAny(User $user): bool
    {
        // Les Éditeurs et les Chercheurs peuvent voir la liste (filtrée pour les chercheurs)
        return $user->hasPermissionTo('manage publications') || $user->hasPermissionTo('manage own publications');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Publication $publication): bool
    {
        if ($user->hasPermissionTo('manage publications')) {
            return true;
        }

        if ($user->hasPermissionTo('manage own publications') && $user->researcher) {
            // Vérifie si le chercheur est l'un des auteurs de la publication
            return $publication->researchers()->where('researcher_id', $user->researcher->id)->exists();
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage publications') || ($user->hasPermissionTo('manage own publications') && $user->hasRole('Chercheur'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Publication $publication): bool
    {
        if ($user->hasPermissionTo('manage publications')) {
            return true;
        }

        if ($user->hasPermissionTo('manage own publications') && $user->researcher) {
            return $publication->researchers()->where('researcher_id', $user->researcher->id)->exists();
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Publication $publication): bool
    {
        if ($user->hasPermissionTo('manage publications')) {
            return true;
        }

        if ($user->hasPermissionTo('manage own publications') && $user->researcher) {
            return $publication->researchers()->where('researcher_id', $user->researcher->id)->exists();
        }
        return false;
    }

    // Les méthodes restore et forceDelete peuvent être ajoutées si vous utilisez le Soft Deleting
}