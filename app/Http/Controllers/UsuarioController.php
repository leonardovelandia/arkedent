<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $rol    = $request->input('rol');

        $query = User::with('roles')->orderBy('name');

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('name', 'like', "%$buscar%")
                  ->orWhere('email', 'like', "%$buscar%");
            });
        }

        if ($rol) {
            $query->whereHas('roles', fn($q) => $q->where('name', $rol));
        }

        $usuarios = $query->paginate(15)->withQueryString();
        $roles    = Role::orderBy('name')->pluck('name');

        return view('usuarios.index', compact('usuarios', 'roles', 'buscar', 'rol'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('usuarios.create', compact('roles'));
    }

    private function mapRolColumna(string $spatieRol): string
    {
        return match($spatieRol) {
            'doctora'        => 'doctor',
            'asistente'      => 'asistente',
            'administrador'  => 'administrador',
            default          => 'doctor',
        };
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'rol'      => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name'              => trim($request->name),
            'email'             => strtolower(trim($request->email)),
            'password'          => Hash::make($request->password),
            'email_verified_at' => now(),
            'rol'               => $this->mapRolColumna($request->rol),
        ]);

        $user->assignRole($request->rol);

        return redirect()->route('usuarios.index')
            ->with('exito', 'Usuario ' . $user->name . ' creado correctamente.');
    }

    public function show(User $usuario)
    {
        $usuario->load('roles');
        return view('usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $roles = Role::orderBy('name')->get();
        $usuario->load('roles');
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'password' => ['nullable', Password::min(8)->letters()->numbers()],
            'rol'      => 'required|string|exists:roles,name',
        ]);

        $data = [
            'name'  => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'rol'   => $this->mapRolColumna($request->rol),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);
        $usuario->syncRoles([$request->rol]);

        return redirect()->route('usuarios.show', $usuario)
            ->with('exito', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $nombre = $usuario->name;
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('exito', 'Usuario ' . $nombre . ' eliminado.');
    }
}
