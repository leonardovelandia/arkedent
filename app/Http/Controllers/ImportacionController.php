<?php
namespace App\Http\Controllers;

use App\Models\Importacion;
use App\Models\ImportacionDetalle;
use App\Services\ImportacionService;
use Illuminate\Http\Request;

class ImportacionController extends Controller
{
    public function index()
    {
        $importaciones = Importacion::with('registradoPor')
            ->where('activo', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total'           => Importacion::where('activo', true)->count(),
            'total_pacientes' => Importacion::where('activo', true)->sum('registros_importados'),
            'con_errores'     => Importacion::where('activo', true)->where('registros_error', '>', 0)->count(),
            'ultima'          => Importacion::where('activo', true)->latest()->first(),
        ];

        return view('importacion.index', compact('importaciones', 'stats'));
    }

    public function create()
    {
        return view('importacion.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fuente'     => 'required|string',
            'tipo_datos' => 'required|string',
            'archivo'    => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
            'notas'      => 'nullable|string|max:1000',
        ]);

        $archivo = $request->file('archivo');
        $ruta    = $archivo->store('importaciones', 'public');

        $importacion = Importacion::create([
            'fuente'         => $request->fuente,
            'tipo_datos'     => $request->tipo_datos,
            'archivo_nombre' => $archivo->getClientOriginalName(),
            'archivo_path'   => $ruta,
            'user_id'        => auth()->id(),
            'notas'          => $request->notas,
            'estado'         => 'pendiente',
        ]);

        return redirect()
            ->route('importacion.show', $importacion)
            ->with('exito', 'Archivo cargado exitosamente. Revisa el detalle y confirma la importación.');
    }

    public function show($id)
    {
        $importacion  = Importacion::with(['registradoPor', 'detalles'])->findOrFail($id);
        $filtroEstado = request('filtro', 'todos');

        $detallesQuery = ImportacionDetalle::where('importacion_id', $id);
        if ($filtroEstado !== 'todos') {
            $detallesQuery->where('estado', $filtroEstado);
        }
        $detalles = $detallesQuery->orderBy('fila_numero')->paginate(50)->withQueryString();

        return view('importacion.show', compact('importacion', 'detalles', 'filtroEstado'));
    }

    public function procesar($id)
    {
        $importacion = Importacion::findOrFail($id);

        if (!in_array($importacion->estado, ['pendiente', 'error'])) {
            return back()->with('error', 'Esta importación ya fue procesada.');
        }

        $servicio  = new ImportacionService($importacion);
        $resultado = $servicio->procesar();

        if ($resultado) {
            return redirect()
                ->route('importacion.show', $importacion)
                ->with('exito', "Importación completada: {$importacion->fresh()->registros_importados} registros importados.");
        }

        return redirect()
            ->route('importacion.show', $importacion)
            ->with('error', 'La importación falló. Revisa el log de errores.');
    }

    public function previsualizar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
            'fuente'  => 'required|string',
        ]);

        $archivo   = $request->file('archivo');
        $extension = strtolower($archivo->getClientOriginalExtension());
        $datos     = [];
        $headers   = [];

        if (in_array($extension, ['csv', 'txt'])) {
            $handle = fopen($archivo->getPathname(), 'r');
            // BOM check
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") rewind($handle);

            // Detect separator
            $firstLine = fgets($handle);
            rewind($handle);
            $sep = ';';
            foreach ([';', ',', '|'] as $s) {
                if (substr_count($firstLine, $s) > substr_count($firstLine, $sep)) $sep = $s;
            }

            $count = 0;
            while (($row = fgetcsv($handle, 2000, $sep)) !== false && $count <= 10) {
                if (empty($headers)) { $headers = array_map('trim', $row); continue; }
                $datos[] = array_map('trim', $row);
                $count++;
            }
            fclose($handle);
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($archivo->getPathname());
                $sheet       = $spreadsheet->getActiveSheet();
                $count       = 0;
                foreach ($sheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $rowData = [];
                    foreach ($cellIterator as $cell) {
                        $rowData[] = trim((string)($cell->getValue() ?? ''));
                    }
                    if (empty($headers)) { $headers = $rowData; continue; }
                    if ($count >= 10) break;
                    $datos[] = $rowData;
                    $count++;
                }
            }
        }

        return response()->json([
            'headers' => $headers,
            'filas'   => $datos,
            'total'   => count($datos),
        ]);
    }

    public function revertir($id)
    {
        $importacion = Importacion::findOrFail($id);

        if (!$importacion->puede_revertir) {
            return back()->with('error', 'Esta importación no puede revertirse.');
        }

        $servicio  = new ImportacionService($importacion);
        $resultado = $servicio->revertir();

        if ($resultado) {
            return back()->with('exito', 'Importación revertida. Los registros creados fueron eliminados.');
        }

        return back()->with('error', 'No se pudo revertir la importación.');
    }

    public function descargarPlantilla(string $tipo)
    {
        $plantillas = [
            'pacientes' => [
                'nombre'   => 'Plantilla_Pacientes_Arkedent.csv',
                'headers'  => ['Nombres','Apellidos','Tipo Documento','Numero Documento','Fecha Nacimiento','Genero','Telefono','Email','Direccion','Ciudad','Ocupacion','Acudiente','Telefono Emergencia'],
                'ejemplo'  => ['Maria Fernanda','Gonzalez Lopez','CC','1234567890','1990-05-15','Femenino','3001234567','maria@email.com','Calle 45 #12-34','Medellin','Profesora','Juan Gonzalez','3009876543'],
                'ejemplo2' => ['Carlos Alberto','Ramirez Torres','TI','987654321','2008-11-22','Masculino','3109998877','','Av. 80 #56-78','Bogota','Estudiante','Ana Torres','3201112233'],
            ],
            'citas' => [
                'nombre'   => 'Plantilla_Citas_Arkedent.csv',
                'headers'  => ['Numero Documento Paciente','Fecha','Hora Inicio','Hora Fin','Procedimiento','Estado','Notas'],
                'ejemplo'  => ['1234567890','2024-03-15','09:00','10:00','Limpieza dental','atendida','Paciente puntual'],
                'ejemplo2' => ['987654321','2024-04-20','14:00','15:00','Extraccion','pendiente',''],
            ],
            'pagos' => [
                'nombre'   => 'Plantilla_Pagos_Arkedent.csv',
                'headers'  => ['Numero Documento Paciente','Fecha Pago','Valor','Metodo Pago','Concepto','Numero Recibo'],
                'ejemplo'  => ['1234567890','2024-03-15','150000','efectivo','Limpieza dental','REC-001'],
                'ejemplo2' => ['987654321','2024-04-20','80000','transferencia','Consulta','REC-002'],
            ],
            'evoluciones' => [
                'nombre'   => 'Plantilla_Evoluciones_Arkedent.csv',
                'headers'  => ['Numero Documento Paciente','Fecha','Procedimiento','Descripcion','Materiales','Observaciones'],
                'ejemplo'  => ['1234567890','2024-03-15','Profilaxis','Se realizo limpieza profesional','Pasta profilactica','Excelente higiene'],
                'ejemplo2' => ['987654321','2024-04-20','Extraccion molar','Extraccion pieza 36 sin complicaciones','Anestesia local','Indicaciones post-operatorio dadas'],
            ],
            'historia_clinica' => [
                'nombre'   => 'Plantilla_HistoriaClinica_Arkedent.csv',
                'headers'  => ['Numero Documento Paciente','Fecha Apertura','Motivo Consulta','Enfermedad Actual','Antecedentes Medicos','Medicamentos','Alergias','Antecedentes Familiares','Presion Arterial','Observaciones'],
                'ejemplo'  => ['1234567890','2024-01-10','Dolor dental','Caries profunda','Hipertension','Losartan 50mg','Penicilina','Diabetes familiar','120/80',''],
                'ejemplo2' => ['987654321','2024-02-15','Control general','Sin novedad','Ninguno','','','','130/85','Paciente cooperador'],
            ],
            'tratamientos' => [
                'nombre'   => 'Plantilla_Tratamientos_Arkedent.csv',
                'headers'  => ['Numero Documento Paciente','Nombre Tratamiento','Valor Total','Saldo Pendiente','Estado','Fecha Inicio','Fecha Fin','Notas'],
                'ejemplo'  => ['1234567890','Ortodoncia completa','4500000','2000000','en_proceso','2024-01-15','','Brackets metalicos'],
                'ejemplo2' => ['987654321','Limpieza y blanqueamiento','350000','0','terminado','2024-03-01','2024-03-01',''],
            ],
            'consentimientos' => [
                'nombre'   => 'Plantilla_Consentimientos_Arkedent.csv',
                'headers'  => ['Numero Documento Paciente','Fecha Autorizacion','Acepta Almacenamiento','Acepta WhatsApp','Acepta Email','Acepta Llamadas','Acepta Recordatorios','Acepta Compartir','Firmado'],
                'ejemplo'  => ['1234567890','2024-01-10','si','si','si','no','si','no','si'],
                'ejemplo2' => ['987654321','2024-02-15','si','no','si','si','si','no','si'],
            ],
        ];

        $plantilla = $plantillas[$tipo] ?? $plantillas['pacientes'];

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $plantilla['nombre'] . '"',
        ];

        $callback = function() use ($plantilla) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $plantilla['headers'], ';');
            fputcsv($file, $plantilla['ejemplo'], ';');
            if (isset($plantilla['ejemplo2'])) {
                fputcsv($file, $plantilla['ejemplo2'], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function plantillas()
    {
        return view('importacion.plantillas');
    }

    public function destroy($id)
    {
        $importacion = Importacion::findOrFail($id);
        $importacion->update(['activo' => false]);
        return redirect()->route('importacion.index')
            ->with('exito', 'Registro de importación eliminado.');
    }
}
