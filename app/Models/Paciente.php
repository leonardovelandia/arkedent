<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Paciente extends Model
{
    protected $fillable = [
        'numero_historia',
        'nombre',
        'apellido',
        'tipo_documento',
        'numero_documento',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'telefono_emergencia',
        'email',
        'direccion',
        'ciudad',
        'ocupacion',
        'nombre_acudiente',
        'foto_path',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo'           => 'boolean',
    ];

    // ── Auto-generar numero_historia al crear ──────────────────
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Paciente $paciente) {
            if (empty($paciente->numero_historia)) {
                $ultimo = static::max('id') ?? 0;
                $paciente->numero_historia = 'PAC-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // ── Accessors ─────────────────────────────────────────────
    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function getEdadAttribute(): int
    {
        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto_path) {
            return asset('storage/' . $this->foto_path);
        }

        return '';
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // ── Relaciones (preparadas) ───────────────────────────────
    public function historiaClinica()
    {
        return $this->hasOne(HistoriaClinica::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class)->orderBy('fecha', 'desc');
    }

    public function proximasCitas()
    {
        return $this->hasMany(Cita::class)
            ->whereDate('fecha', '>=', today())
            ->where('activo', true)
            ->orderBy('fecha')
            ->orderBy('hora_inicio');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class)->orderBy('fecha_pago', 'desc');
    }

    public function tratamientos()
    {
        return $this->hasMany(Tratamiento::class)->orderBy('created_at', 'desc');
    }

    public function getTotalDeudaAttribute()
    {
        return $this->tratamientos()
            ->where('estado', 'activo')
            ->sum('saldo_pendiente');
    }

    public function getTotalPagadoAttribute()
    {
        return $this->pagos()
            ->where('anulado', false)
            ->sum('valor');
    }

    public function consentimientos()
    {
        return $this->hasMany(Consentimiento::class)->orderBy('created_at', 'desc');
    }

    public function evoluciones()
    {
        return $this->hasMany(Evolucion::class)->orderBy('fecha', 'desc');
    }

    public function presupuestos()
    {
        return $this->hasMany(Presupuesto::class)->orderBy('created_at', 'desc');
    }

    public function presupuestosVigentes()
    {
        return $this->hasMany(Presupuesto::class)
            ->whereIn('estado', ['borrador', 'enviado', 'aprobado'])
            ->where('activo', true);
    }

    public function imagenesClinicas()
    {
        return $this->hasMany(ImagenClinica::class)
            ->where('activo', true)
            ->orderBy('fecha_toma', 'desc');
    }

    public function valoraciones()
    {
        return $this->hasMany(Valoracion::class)
            ->orderBy('fecha', 'desc');
    }

    public function ultimaValoracion()
    {
        return $this->hasOne(Valoracion::class)
            ->orderBy('fecha', 'desc');
    }

    public function ordenesLaboratorio()
    {
        return $this->hasMany(OrdenLaboratorio::class)
            ->orderBy('created_at', 'desc');
    }
}
