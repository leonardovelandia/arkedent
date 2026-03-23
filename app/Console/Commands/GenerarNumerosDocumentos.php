<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HistoriaClinica;
use App\Models\Evolucion;
use App\Models\Cita;
use App\Models\Consentimiento;
use App\Models\CorreccionHistoria;
use App\Models\CorreccionEvolucion;
use App\Models\Tratamiento;

class GenerarNumerosDocumentos extends Command
{
    protected $signature   = 'sistema:generar-numeros';
    protected $description = 'Genera números de documento (HC-, EVO-, CIT-, etc.) para registros existentes que no tienen número asignado.';

    public function handle(): int
    {
        $tareas = [
            ['modelo' => HistoriaClinica::class,   'campo' => 'numero_historia',      'prefijo' => 'HC'],
            ['modelo' => Evolucion::class,          'campo' => 'numero_evolucion',     'prefijo' => 'EVO'],
            ['modelo' => Cita::class,               'campo' => 'numero_cita',          'prefijo' => 'CIT'],
            ['modelo' => Consentimiento::class,     'campo' => 'numero_consentimiento','prefijo' => 'CON'],
            ['modelo' => CorreccionHistoria::class, 'campo' => 'numero_correccion',    'prefijo' => 'CRH'],
            ['modelo' => CorreccionEvolucion::class,'campo' => 'numero_correccion',    'prefijo' => 'CRE'],
            ['modelo' => Tratamiento::class,        'campo' => 'numero_tratamiento',   'prefijo' => 'TRT'],
        ];

        foreach ($tareas as $tarea) {
            $modelo  = $tarea['modelo'];
            $campo   = $tarea['campo'];
            $prefijo = $tarea['prefijo'];

            $sinNumero = $modelo::whereNull($campo)->orderBy('id')->get();

            if ($sinNumero->isEmpty()) {
                $this->line("  <fg=gray>{$prefijo}: sin registros pendientes.</>");
                continue;
            }

            $this->line("  <fg=cyan>{$prefijo}: asignando números a {$sinNumero->count()} registros…</>");

            $bar = $this->output->createProgressBar($sinNumero->count());
            $bar->start();

            foreach ($sinNumero as $registro) {
                $numero = $modelo::generarNumero($prefijo, $campo);
                $modelo::where('id', $registro->id)->update([$campo => $numero]);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        $this->info('Numeración completada.');

        return self::SUCCESS;
    }
}
