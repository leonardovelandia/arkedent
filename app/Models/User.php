<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'activo',
        'locked_at',
        'last_login_at',
        'last_login_ip',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'activo'             => 'boolean',
            'locked_at'          => 'datetime',
            'last_login_at'      => 'datetime',
            'password_changed_at'=> 'datetime',
        ];
    }

    /**
     * Envía la notificación de recuperación de contraseña en español.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function esDoctor(): bool        { return $this->rol === 'doctor'; }
    public function esAsistente(): bool     { return $this->rol === 'asistente'; }
    public function esAdministrador(): bool { return $this->rol === 'administrador'; }

    public function getRolLabelAttribute(): string
    {
        return match($this->rol) {
            'doctor'         => 'Doctor(a)',
            'asistente'      => 'Asistente',
            'administrador'  => 'Administrador',
            default          => ucfirst($this->rol),
        };
    }
}
