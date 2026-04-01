<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Verificar cuenta bloqueada
        if ($request->user() && $request->user()->locked_at) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Esta cuenta ha sido bloqueada por seguridad. Contacte al administrador.',
            ]);
        }

        $request->session()->regenerate();

        // Registrar último acceso
        $request->user()->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        AuditoriaService::login();

        $user = Auth::user();
        $rol  = $user->getRoleNames()->first();

        $destino = match($rol) {
            'doctora'       => route('dashboard'),
            'administrador' => route('dashboard'),
            'asistente'     => route('pacientes.index'),
            default         => route('dashboard'),
        };

        return redirect()->intended($destino);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        AuditoriaService::logout();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
