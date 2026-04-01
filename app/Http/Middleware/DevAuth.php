<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DevAuth
{
    private string $cookieName = 'dev_panel_auth';

    public function handle(Request $request, Closure $next): mixed
    {
        $rateLimitKey = 'dev_panel_intentos_' . $request->ip();

        if (Cache::get($rateLimitKey, 0) >= 10) {
            abort(429, 'Demasiados intentos. Espera 30 minutos.');
        }

        $password      = env('DEV_PASSWORD', '********');
        $tokenEsperado = hash_hmac('sha256', $password, config('app.key'));
        $tokenCookie   = $request->cookie($this->cookieName);

        if ($tokenCookie && hash_equals($tokenEsperado, $tokenCookie)) {
            Cache::forget($rateLimitKey);
            $response = $next($request);
            return $response->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'        => 'no-cache',
                'Expires'       => 'Sat, 01 Jan 2000 00:00:00 GMT',
            ]);
        }

        Cache::put($rateLimitKey, Cache::get($rateLimitKey, 0) + 1, now()->addMinutes(30));

        return redirect()->route('dev.auth.form', ['redirigir' => url('/dev')]);
    }
}
