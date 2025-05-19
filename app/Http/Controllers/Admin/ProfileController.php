<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule; // Pour Rule::unique

class ProfileController extends Controller
{
    /**
     * Affiche le formulaire d'édition du profil de l'utilisateur connecté.
     */
    public function edit(Request $request)
    {
        return view('admin.profile.edit', [
            'user' => $request->user(), // Passe l'utilisateur authentifié à la vue
        ]);
    }

    /**
     * Met à jour les informations du profil de l'utilisateur connecté.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        // Si l'email a été modifié et que vous utilisez la vérification d'email :
        // if ($user->isDirty('email')) {
        //     $user->email_verified_at = null;
        // }

        $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profil mis à jour avec succès.');
    }

    // Vous pourriez ajouter une méthode destroy(Request $request) ici si vous gérez la suppression de compte.
}