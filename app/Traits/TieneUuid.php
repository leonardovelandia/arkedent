<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait TieneUuid
{
    protected static function bootTieneUuid(): void
    {
        static::creating(function ($modelo) {
            if (empty($modelo->uuid)) {
                $modelo->uuid = (string) Str::uuid();
            }
        });
    }

    public static function porUuid(string $uuid): ?static
    {
        return static::where('uuid', $uuid)->first();
    }

    public static function porUuidOrFail(string $uuid): static
    {
        return static::where('uuid', $uuid)->firstOrFail();
    }

    public function scopePorUuid($query, string $uuid)
    {
        return $query->where('uuid', $uuid);
    }

    public function scopePorUuidOrFail($query, string $uuid)
    {
        return $query->where('uuid', $uuid)->firstOrFail();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
