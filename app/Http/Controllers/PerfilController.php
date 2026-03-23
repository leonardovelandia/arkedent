<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();
        return view('perfil.index', compact('usuario'));
    }

    public function update(Request $request)
    {
        $usuario = auth()->user();

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'password_actual'  => 'nullable|string',
            'password'         => ['nullable', Password::min(8)->letters()->numbers(), 'confirmed'],
        ]);

        if ($request->filled('password')) {
            if (!$request->filled('password_actual') || !Hash::check($request->password_actual, $usuario->password)) {
                return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.'])->withInput();
            }
        }

        $data = [
            'name'  => trim($request->name),
            'email' => strtolower(trim($request->email)),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return back()->with('exito', 'Perfil actualizado correctamente.');
    }
}
