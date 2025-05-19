<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Researcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:manage users']);

        // Si vous aviez besoin d'une granularité plus fine par méthode plus tard:
        // $this->middleware(['permission:view users'])->only('index', 'show');
        // $this->middleware(['permission:create users'])->only('create', 'store');
        // $this->middleware(['permission:edit users'])->only('edit', 'update');
        // $this->middleware(['permission:delete users'])->only('destroy');
    }

    private function validationRules(User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', $user ? Rule::unique('users')->ignore($user->id) : Rule::unique('users')],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name'],
            'researcher_id' => ['nullable', 'exists:researchers,id', Rule::unique('researchers', 'user_id')->ignore($user ? $user->researcher?->id : null, 'id')]
        ];

        if (!$user || ($user && ($rules['password_confirmation'] ?? false))) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        } else {
            $rules['password'] = ['nullable', 'confirmed', Rules\Password::defaults()];
        }

        return $rules;
    }

    public function index()
    {
        $users = User::with('roles')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        $researchers = Researcher::whereNull('user_id')->orWhere('user_id', '')->pluck('first_name', 'id');
        return view('admin.users.create', compact('roles', 'researchers'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            $user->assignRole($validatedData['roles']);

            if (in_array('Chercheur', $validatedData['roles']) && !empty($request->researcher_id)) {
                $researcher = Researcher::find($request->researcher_id);
                if ($researcher && is_null($researcher->user_id)) {
                    $researcher->user_id = $user->id;
                    $researcher->save();
                }
            }

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'Utilisateur "' . $user->name . '" créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage())->withInput();
        }
    }

    public function show(User $user)
    {
        $user->load('roles', 'researcher');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name')->all();
        $currentResearcherId = $user->researcher?->id;
        $researchers = Researcher::whereNull('user_id')
                                ->orWhere('user_id', $user->id)
                                ->pluck('first_name', 'id')
                                ->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'researchers', 'currentResearcherId'));
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate($this->validationRules($user));

        DB::beginTransaction();
        try {
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];

            if (!empty($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }

            $user->save();
            $user->syncRoles($validatedData['roles']);

            if (in_array('Chercheur', $validatedData['roles'])) {
                if (!empty($request->researcher_id)) {
                    $newResearcher = Researcher::find($request->researcher_id);
                    if ($newResearcher) {
                        if ($user->researcher && $user->researcher->id !== $newResearcher->id) {
                            $user->researcher->user_id = null;
                            $user->researcher->save();
                        }
                        $newResearcher->user_id = $user->id;
                        $newResearcher->save();
                    }
                } else {
                    if ($user->researcher) {
                        $user->researcher->user_id = null;
                        $user->researcher->save();
                    }
                }
            } else {
                if ($user->researcher) {
                    $user->researcher->user_id = null;
                    $user->researcher->save();
                }
            }

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'Utilisateur "' . $user->name . '" mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour de l\'utilisateur: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($user->researcher) {
            $user->researcher->user_id = null;
            $user->researcher->save();
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur "' . $userName . '" supprimé avec succès.');
    }
}
