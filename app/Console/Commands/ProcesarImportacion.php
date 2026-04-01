<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Importacion;
use App\Services\ImportacionService;

class ProcesarImportacion extends Command
{
    protected $signature   = 'importacion:procesar {id}';
    protected $description = 'Procesa una importación pendiente';

    public function handle()
    {
        $importacion = Importacion::findOrFail($this->argument('id'));

        if (!in_array($importacion->estado, ['pendiente', 'error'])) {
            $this->error("La importación {$importacion->numero_formateado} no está en estado pendiente.");
            return 1;
        }

        $this->info("Procesando importación {$importacion->numero_formateado}...");
        $bar = $this->output->createProgressBar($importacion->total_registros ?: 100);

        $servicio  = new ImportacionService($importacion);
        $resultado = $servicio->procesar();

        $bar->finish();
        $this->newLine();

        $imp = $importacion->fresh();
        if ($resultado) {
            $this->info("Importación completada:");
            $this->line("   Importados:  {$imp->registros_importados}");
            $this->line("   Duplicados:  {$imp->registros_duplicados}");
            $this->line("   Omitidos:    {$imp->registros_omitidos}");
            $this->line("   Errores:     {$imp->registros_error}");
            return 0;
        } else {
            $this->error("La importación falló. Revisa el log en el sistema.");
            return 1;
        }
    }
}
