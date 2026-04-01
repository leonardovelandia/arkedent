<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait ValidaArchivos
{
    private array $extensionesPeligrosas = [
        'php', 'php3', 'php4', 'php5', 'phtml',
        'exe', 'bat', 'cmd', 'sh', 'bash',
        'py', 'rb', 'pl', 'cgi',
        'js', 'vbs', 'ps1',
        'htaccess', 'htpasswd',
        'sql', 'db',
    ];

    private array $mimeImagenes = [
        'image/jpeg', 'image/jpg', 'image/png',
        'image/gif', 'image/webp', 'image/bmp',
        'application/pdf',
    ];

    private array $mimeImportacion = [
        'text/csv', 'text/plain',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    /**
     * Valida que el archivo no sea peligroso.
     */
    protected function validarArchivoSeguro(UploadedFile $archivo): bool
    {
        $extension = strtolower($archivo->getClientOriginalExtension());

        if (in_array($extension, $this->extensionesPeligrosas)) {
            abort(422, "Tipo de archivo no permitido: .{$extension}");
        }

        $mimeReal = $archivo->getMimeType();
        if (str_contains($mimeReal, 'php') || str_contains($mimeReal, 'x-sh')) {
            abort(422, 'Tipo de archivo no permitido.');
        }

        return true;
    }

    /**
     * Valida archivos de imagen clínica (imágenes + PDF, máx 15 MB).
     */
    protected function validarImagenClinica(UploadedFile $archivo): bool
    {
        $this->validarArchivoSeguro($archivo);

        if (!in_array($archivo->getMimeType(), $this->mimeImagenes)) {
            abort(422, 'Solo se permiten imágenes y PDFs.');
        }

        if ($archivo->getSize() > 15 * 1024 * 1024) {
            abort(422, 'El archivo no puede superar 15 MB.');
        }

        return true;
    }

    /**
     * Genera un nombre de archivo seguro basado en UUID.
     */
    protected function nombreArchivoSeguro(UploadedFile $archivo): string
    {
        $extension = strtolower($archivo->getClientOriginalExtension());
        return Str::uuid() . '.' . $extension;
    }
}
