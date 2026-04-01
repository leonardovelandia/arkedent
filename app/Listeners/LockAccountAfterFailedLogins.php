<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LockAccountAfterFailedLogins
{
    public function handle(Failed $event): void
    {
        if (!$event->user) {
            return;
        }

        $key   = 'failed_logins_' . $event->user->id;
        $count = Cache::increment($key);
        Cache::put($key, $count, now()->addMinutes(30));

        if ($count >= 5) {
            $event->user->update(['locked_at' => now()]);
            Log::channel('firmas')->warning('Cuenta bloqueada por intentos fallidos', [
                'user_id' => $event->user->id,
                'email'   => $event->user->email,
                'ip'      => request()->ip(),
            ]);
        }
    }
}
