<?php
namespace App\Services;

use App\Models\AutorizacionDatos;
use App\Models\Cita;
use App\Models\Evolucion;
use App\Models\HistoriaClinica;
use App\Models\Importacion;
use App\Models\ImportacionDetalle;
use App\Models\Paciente;
use App\Models\Pago;
use App\Models\Tratamiento;
use Illuminate\Support\Facades\DB;

class ImportacionService
{
    private Importacion $importacion;
    private array $errores = [];
    private array $cachePacientes = [];

    public function __construct(Importacion $importacion)
    {
        $this->importacion = $importacion;
    }

    // ─── PROCESO PRINCIPAL ────────────────────────────────────────

    public function procesar(): bool
    {
        try {
            $this->importacion->update([
                'estado'            => 'procesando',
                'fecha_importacion' => now(),
            ]);

            $datos = $this->leerArchivo();

            if (empty($datos)) {
                throw new \Exception('El archivo no contiene datos válidos.');
            }

            $this->importacion->update(['total_registros' => count($datos)]);

            DB::beginTransaction();

            foreach ($datos as $index => $fila) {
                $this->procesarFila($fila, $index + 1);
            }

            DB::commit();

            $this->importacion->update([
                'estado'  => 'completado',
                'errores' => $this->errores,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->importacion->update([
                'estado'  => 'error',
                'errores' => array_merge($this->errores, ['Error general: ' . $e->getMessage()]),
            ]);
            return false;
        }
    }

    // ─── LECTURA DE ARCHIVO ───────────────────────────────────────

    private function leerArchivo(): array
    {
        $path      = storage_path('app/public/' . $this->importacion->archivo_path);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($extension, ['csv', 'txt'])) {
            return $this->leerCSV($path);
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            return $this->leerExcel($path);
        }

        throw new \Exception("Formato de archivo no soportado: {$extension}");
    }

    private function leerCSV(string $path): array
    {
        $datos       = [];
        $separadores = [';', ',', '|', "\t"];

        $handle       = fopen($path, 'r');
        $primeraLinea = fgets($handle);
        fclose($handle);

        $separador = ',';
        foreach ($separadores as $sep) {
            if (substr_count($primeraLinea, $sep) > substr_count($primeraLinea, $separador)) {
                $separador = $sep;
            }
        }

        if (($handle = fopen($path, 'r')) !== false) {
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") rewind($handle);

            $headers = null;
            while (($row = fgetcsv($handle, 2000, $separador)) !== false) {
                if ($headers === null) {
                    $headers = array_map('trim', $row);
                    continue;
                }
                if (count($headers) <= count($row)) {
                    $datos[] = array_combine($headers, array_map('trim', array_slice($row, 0, count($headers))));
                }
            }
            fclose($handle);
        }

        return $datos;
    }

    private function leerExcel(string $path): array
    {
        if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            throw new \Exception('Para importar archivos Excel instala: composer require phpoffice/phpspreadsheet');
        }

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet       = $spreadsheet->getActiveSheet();
        $datos       = [];
        $headers     = null;

        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = trim((string)($cell->getValue() ?? ''));
            }

            if ($headers === null) {
                $headers = $rowData;
                continue;
            }

            if (count(array_filter($rowData))) {
                if (count($headers) <= count($rowData)) {
                    $datos[] = array_combine($headers, array_slice($rowData, 0, count($headers)));
                }
            }
        }

        return $datos;
    }

    // ─── PROCESAMIENTO DE FILAS ───────────────────────────────────

    private function procesarFila(array $fila, int $numero): void
    {
        $fuente = $this->importacion->fuente;
        $tipo   = $this->importacion->tipo_datos;

        $datosTransformados = match($fuente) {
            'dentox'         => $this->transformarDentox($fila, $tipo),
            'odontosof'      => $this->transformarOdontosof($fila, $tipo),
            'dentalpro'      => $this->transformarDentalPro($fila, $tipo),
            'excel_generico',
            'csv_generico'   => $this->transformarGenerico($fila, $tipo),
            default          => $fila,
        };

        if (empty($datosTransformados)) {
            $this->registrarDetalle($numero, $fila, null, 'omitido', 'Fila vacía o inválida');
            $this->importacion->increment('registros_omitidos');
            return;
        }

        $resultado = match($tipo) {
            'pacientes'       => $this->importarPaciente($datosTransformados),
            'historia_clinica'=> $this->importarHistoriaClinica($datosTransformados),
            'citas'           => $this->importarCita($datosTransformados),
            'tratamientos'    => $this->importarTratamiento($datosTransformados),
            'pagos'           => $this->importarPago($datosTransformados),
            'evoluciones'     => $this->importarEvolucion($datosTransformados),
            'consentimientos' => $this->importarConsentimiento($datosTransformados),
            default           => ['estado' => 'omitido', 'mensaje' => "Tipo '{$tipo}' no reconocido"],
        };

        $this->registrarDetalle(
            $numero, $fila, $datosTransformados,
            $resultado['estado'], $resultado['mensaje'] ?? null,
            $resultado['id'] ?? null, $resultado['modelo'] ?? null
        );

        match($resultado['estado']) {
            'importado' => $this->importacion->increment('registros_importados'),
            'duplicado' => $this->importacion->increment('registros_duplicados'),
            'omitido'   => $this->importacion->increment('registros_omitidos'),
            'error'     => $this->importacion->increment('registros_error'),
            default     => null,
        };
    }

    // ─── TRANSFORMADORES POR FUENTE ───────────────────────────────

    private function transformarDentox(array $f, string $tipo): array
    {
        return match($tipo) {
            'pacientes' => [
                'nombre'              => $this->txt($f, ['nombres','primer_nombre','NOMBRES','Nombre','nombre']),
                'apellido'            => $this->txt($f, ['apellidos','primer_apellido','APELLIDOS','Apellido','apellido']),
                'tipo_documento'      => $this->tipoDoc($f, ['tipo_documento','TIPO_DOC','tipo_doc']),
                'numero_documento'    => $this->num($f, ['cedula','documento','CEDULA','Documento']),
                'fecha_nacimiento'    => $this->fecha($f, ['fecha_nacimiento','FECHA_NAC','fechaNacimiento']),
                'genero'              => $this->genero($f, ['sexo','genero','SEXO','Sexo']),
                'telefono'            => $this->num($f, ['celular','telefono','CELULAR','Telefono']),
                'email'               => strtolower($this->txt($f, ['correo','email','CORREO','Email'])),
                'direccion'           => $this->txt($f, ['direccion','DIRECCION','Direccion']),
                'ciudad'              => $this->txt($f, ['ciudad','CIUDAD','Ciudad']),
                'ocupacion'           => $this->txt($f, ['ocupacion','OCUPACION']),
                'nombre_acudiente'    => $this->txt($f, ['acudiente','responsable','nombre_acudiente']),
                'telefono_emergencia' => $this->num($f, ['tel_acudiente','tel_emergencia','telacudiente']),
            ],
            'historia_clinica' => [
                'documento_paciente'         => $this->num($f, ['cedula','documento','CEDULA']),
                'fecha_apertura'             => $this->fecha($f, ['fecha','fecha_apertura','FECHA']),
                'motivo_consulta'            => $this->txt($f, ['motivo','motivo_consulta','MOTIVO']),
                'enfermedad_actual'          => $this->txt($f, ['enfermedad','enfermedad_actual','ENFERMEDAD']),
                'antecedentes_medicos'       => $this->txt($f, ['antecedentes','ant_medicos','ANTECEDENTES']),
                'medicamentos_actuales'      => $this->txt($f, ['medicamentos','MEDICAMENTOS']),
                'alergias'                   => $this->txt($f, ['alergias','ALERGIAS']),
                'antecedentes_familiares'    => $this->txt($f, ['ant_familiares','antecedentes_familiares']),
                'antecedentes_odontologicos' => $this->txt($f, ['ant_odonto','antecedentes_odontologicos']),
                'presion_arterial'           => $this->txt($f, ['presion','presion_arterial','PA']),
                'frecuencia_cardiaca'        => $this->txt($f, ['frecuencia','FC','frecuencia_cardiaca']),
                'observaciones_generales'    => $this->txt($f, ['observaciones','OBSERVACIONES']),
            ],
            'citas' => [
                'documento_paciente' => $this->num($f, ['cedula','documento','CEDULA']),
                'fecha'              => $this->fecha($f, ['fecha','FECHA','fecha_cita']),
                'hora_inicio'        => $this->hora($f, ['hora','hora_inicio','HORA','hora_cita']),
                'hora_fin'           => $this->hora($f, ['hora_fin','HORA_FIN']),
                'procedimiento'      => $this->txt($f, ['procedimiento','tratamiento','PROCEDIMIENTO']),
                'estado'             => $this->estadoCita($f, ['estado','ESTADO']),
                'notas'              => $this->txt($f, ['notas','observaciones','NOTAS']),
            ],
            'tratamientos' => [
                'documento_paciente' => $this->num($f, ['cedula','documento','CEDULA']),
                'nombre'             => $this->txt($f, ['tratamiento','nombre','TRATAMIENTO','procedimiento']),
                'valor_total'        => $this->dinero($f, ['valor','precio','costo','VALOR','total']),
                'saldo_pendiente'    => $this->dinero($f, ['saldo','saldo_pendiente','SALDO']),
                'estado'             => $this->estadoTratamiento($f, ['estado','ESTADO']),
                'fecha_inicio'       => $this->fecha($f, ['fecha_inicio','fecha','FECHA']),
                'fecha_fin'          => $this->fecha($f, ['fecha_fin','fecha_terminacion']),
                'notas'              => $this->txt($f, ['notas','observaciones','NOTAS']),
            ],
            'pagos' => [
                'documento_paciente' => $this->num($f, ['cedula','documento','CEDULA']),
                'concepto'           => $this->txt($f, ['concepto','descripcion','CONCEPTO','tratamiento']),
                'valor'              => $this->dinero($f, ['valor','monto','VALOR','total']),
                'metodo_pago'        => $this->metodoPago($f, ['metodo_pago','forma_pago','METODO','medio_pago']),
                'referencia_pago'    => $this->txt($f, ['referencia','ref','REFERENCIA','recibo']),
                'fecha_pago'         => $this->fecha($f, ['fecha','fecha_pago','FECHA']),
                'observaciones'      => $this->txt($f, ['observaciones','notas','OBSERVACIONES']),
            ],
            'evoluciones' => [
                'documento_paciente'        => $this->num($f, ['cedula','documento','CEDULA']),
                'fecha'                     => $this->fecha($f, ['fecha','FECHA','fecha_evolucion']),
                'hora'                      => $this->hora($f, ['hora','HORA']),
                'procedimiento'             => $this->txt($f, ['procedimiento','tratamiento','PROCEDIMIENTO']),
                'descripcion'               => $this->txt($f, ['descripcion','notas','DESCRIPCION','evolucion']),
                'presion_arterial'          => $this->txt($f, ['presion','PA','presion_arterial']),
                'proxima_cita_fecha'        => $this->fecha($f, ['proxima_cita','proxima_fecha','PROXIMA_CITA']),
                'proxima_cita_procedimiento'=> $this->txt($f, ['prox_procedimiento','proximo_procedimiento']),
                'observaciones'             => $this->txt($f, ['observaciones','OBSERVACIONES']),
            ],
            'consentimientos' => [
                'documento_paciente'         => $this->num($f, ['cedula','documento','CEDULA']),
                'fecha_autorizacion'         => $this->fecha($f, ['fecha','fecha_autorizacion','FECHA']),
                'acepta_almacenamiento'      => $this->bool($f, ['acepta_datos','almacenamiento','acepta']),
                'acepta_contacto_whatsapp'   => $this->bool($f, ['whatsapp','acepta_whatsapp']),
                'acepta_contacto_email'      => $this->bool($f, ['email_contacto','acepta_email']),
                'acepta_contacto_llamada'    => $this->bool($f, ['llamadas','acepta_llamada']),
                'acepta_recordatorios'       => $this->bool($f, ['recordatorios','acepta_recordatorios']),
                'acepta_compartir_entidades' => $this->bool($f, ['compartir','acepta_compartir']),
                'firmado'                    => $this->bool($f, ['firmado','firmada','tiene_firma']),
            ],
            default => $f,
        };
    }

    private function transformarOdontosof(array $f, string $tipo): array
    {
        return match($tipo) {
            'pacientes' => [
                'nombre'              => $this->txt($f, ['Nombre','nombre','PrimerNombre']),
                'apellido'            => $this->txt($f, ['Apellido','apellido','PrimerApellido']),
                'tipo_documento'      => $this->tipoDoc($f, ['TipoDoc','tipo_doc','TipoDocumento']),
                'numero_documento'    => $this->num($f, ['Documento','documento','Cedula','cedula']),
                'fecha_nacimiento'    => $this->fecha($f, ['FechaNacimiento','fecha_nacimiento']),
                'genero'              => $this->genero($f, ['Genero','genero','Sexo','sexo']),
                'telefono'            => $this->num($f, ['Telefono','Celular','telefono','celular']),
                'email'               => strtolower($this->txt($f, ['Email','Correo','email'])),
                'direccion'           => $this->txt($f, ['Direccion','Dirección','direccion']),
                'ciudad'              => $this->txt($f, ['Ciudad','ciudad']),
                'ocupacion'           => $this->txt($f, ['Ocupacion','Profesion','ocupacion']),
                'nombre_acudiente'    => $this->txt($f, ['Acudiente','NombreAcudiente','nombre_acudiente']),
                'telefono_emergencia' => $this->num($f, ['TelefonoAcudiente','TelAcudiente','telefono_acudiente']),
            ],
            'historia_clinica' => [
                'documento_paciente'         => $this->num($f, ['Documento','Cedula','documento']),
                'fecha_apertura'             => $this->fecha($f, ['FechaApertura','Fecha','fecha']),
                'motivo_consulta'            => $this->txt($f, ['MotivoConsulta','Motivo','motivo']),
                'enfermedad_actual'          => $this->txt($f, ['EnfermedadActual','Enfermedad']),
                'antecedentes_medicos'       => $this->txt($f, ['AntecedentesMedicos','Antecedentes']),
                'medicamentos_actuales'      => $this->txt($f, ['Medicamentos','MedicamentosActuales']),
                'alergias'                   => $this->txt($f, ['Alergias','alergia']),
                'antecedentes_familiares'    => $this->txt($f, ['AntecedentesFamiliares']),
                'antecedentes_odontologicos' => $this->txt($f, ['AntecedentesOdonto','AnteOdonto']),
                'presion_arterial'           => $this->txt($f, ['PresionArterial','PA','Presion']),
                'frecuencia_cardiaca'        => $this->txt($f, ['FrecuenciaCardiaca','FC']),
                'observaciones_generales'    => $this->txt($f, ['Observaciones','observaciones']),
            ],
            'citas' => [
                'documento_paciente' => $this->num($f, ['Documento','Cedula']),
                'fecha'              => $this->fecha($f, ['Fecha','FechaCita']),
                'hora_inicio'        => $this->hora($f, ['Hora','HoraInicio','HoraCita']),
                'hora_fin'           => $this->hora($f, ['HoraFin']),
                'procedimiento'      => $this->txt($f, ['Procedimiento','Tratamiento']),
                'estado'             => $this->estadoCita($f, ['Estado','EstadoCita']),
                'notas'              => $this->txt($f, ['Notas','Observaciones','nota']),
            ],
            'tratamientos' => [
                'documento_paciente' => $this->num($f, ['Documento','Cedula']),
                'nombre'             => $this->txt($f, ['Tratamiento','Procedimiento','NombreTratamiento']),
                'valor_total'        => $this->dinero($f, ['Valor','Costo','Total','ValorTotal']),
                'saldo_pendiente'    => $this->dinero($f, ['Saldo','SaldoPendiente']),
                'estado'             => $this->estadoTratamiento($f, ['Estado','EstadoTratamiento']),
                'fecha_inicio'       => $this->fecha($f, ['FechaInicio','Fecha']),
                'fecha_fin'          => $this->fecha($f, ['FechaFin','FechaTerminacion']),
                'notas'              => $this->txt($f, ['Notas','Observaciones']),
            ],
            'pagos' => [
                'documento_paciente' => $this->num($f, ['Documento','Cedula']),
                'concepto'           => $this->txt($f, ['Concepto','Descripcion','Tratamiento']),
                'valor'              => $this->dinero($f, ['Valor','Monto','Total']),
                'metodo_pago'        => $this->metodoPago($f, ['MetodoPago','FormaPago','MedioPago']),
                'referencia_pago'    => $this->txt($f, ['Referencia','Recibo','NumRecibo']),
                'fecha_pago'         => $this->fecha($f, ['FechaPago','Fecha']),
                'observaciones'      => $this->txt($f, ['Observaciones','Notas']),
            ],
            'evoluciones' => [
                'documento_paciente'         => $this->num($f, ['Documento','Cedula']),
                'fecha'                      => $this->fecha($f, ['Fecha','FechaEvolucion']),
                'hora'                       => $this->hora($f, ['Hora']),
                'procedimiento'              => $this->txt($f, ['Procedimiento','Tratamiento']),
                'descripcion'                => $this->txt($f, ['Descripcion','Evolucion','Notas']),
                'presion_arterial'           => $this->txt($f, ['PresionArterial','PA']),
                'proxima_cita_fecha'         => $this->fecha($f, ['ProximaCita','ProximaFecha']),
                'proxima_cita_procedimiento' => $this->txt($f, ['ProxProcedimiento','ProxTratamiento']),
                'observaciones'              => $this->txt($f, ['Observaciones']),
            ],
            'consentimientos' => [
                'documento_paciente'         => $this->num($f, ['Documento','Cedula']),
                'fecha_autorizacion'         => $this->fecha($f, ['Fecha','FechaAutorizacion']),
                'acepta_almacenamiento'      => $this->bool($f, ['AceptaDatos','Almacenamiento','acepta']),
                'acepta_contacto_whatsapp'   => $this->bool($f, ['Whatsapp','AceptaWhatsapp']),
                'acepta_contacto_email'      => $this->bool($f, ['EmailContacto','AceptaEmail']),
                'acepta_contacto_llamada'    => $this->bool($f, ['Llamadas','AceptaLlamada']),
                'acepta_recordatorios'       => $this->bool($f, ['Recordatorios','AceptaRecordatorios']),
                'acepta_compartir_entidades' => $this->bool($f, ['Compartir','AceptaCompartir']),
                'firmado'                    => $this->bool($f, ['Firmado','TieneFirma']),
            ],
            default => $f,
        };
    }

    private function transformarDentalPro(array $f, string $tipo): array
    {
        return match($tipo) {
            'pacientes' => [
                'nombre'              => $this->txt($f, ['first_name','nombre','name']),
                'apellido'            => $this->txt($f, ['last_name','apellido','surname']),
                'tipo_documento'      => $this->tipoDoc($f, ['doc_type','tipo_doc']),
                'numero_documento'    => $this->num($f, ['doc_number','document','cedula']),
                'fecha_nacimiento'    => $this->fecha($f, ['birth_date','birthdate','fecha_nacimiento']),
                'genero'              => $this->genero($f, ['gender','sex','genero','sexo']),
                'telefono'            => $this->num($f, ['phone','mobile','telefono','celular']),
                'email'               => strtolower($this->txt($f, ['email','correo'])),
                'direccion'           => $this->txt($f, ['address','direccion']),
                'ciudad'              => $this->txt($f, ['city','ciudad']),
                'ocupacion'           => $this->txt($f, ['occupation','job','ocupacion']),
                'nombre_acudiente'    => $this->txt($f, ['guardian_name','acudiente']),
                'telefono_emergencia' => $this->num($f, ['emergency_phone','guardian_phone']),
            ],
            'historia_clinica' => [
                'documento_paciente'         => $this->num($f, ['doc_number','document','cedula']),
                'fecha_apertura'             => $this->fecha($f, ['opening_date','date','fecha']),
                'motivo_consulta'            => $this->txt($f, ['chief_complaint','reason','motivo']),
                'enfermedad_actual'          => $this->txt($f, ['current_illness','present_illness']),
                'antecedentes_medicos'       => $this->txt($f, ['medical_history','antecedentes']),
                'medicamentos_actuales'      => $this->txt($f, ['medications','current_medications']),
                'alergias'                   => $this->txt($f, ['allergies','alergias']),
                'observaciones_generales'    => $this->txt($f, ['notes','observations','observaciones']),
            ],
            'citas' => [
                'documento_paciente' => $this->num($f, ['doc_number','document','cedula']),
                'fecha'              => $this->fecha($f, ['date','appointment_date','fecha']),
                'hora_inicio'        => $this->hora($f, ['start_time','time','hora']),
                'hora_fin'           => $this->hora($f, ['end_time','hora_fin']),
                'procedimiento'      => $this->txt($f, ['procedure','treatment','procedimiento']),
                'estado'             => $this->estadoCita($f, ['status','estado']),
                'notas'              => $this->txt($f, ['notes','notas']),
            ],
            'tratamientos' => [
                'documento_paciente' => $this->num($f, ['doc_number','document','cedula']),
                'nombre'             => $this->txt($f, ['treatment','procedure','nombre','tratamiento']),
                'valor_total'        => $this->dinero($f, ['total','amount','price','valor']),
                'saldo_pendiente'    => $this->dinero($f, ['balance','outstanding','saldo']),
                'estado'             => $this->estadoTratamiento($f, ['status','estado']),
                'fecha_inicio'       => $this->fecha($f, ['start_date','fecha_inicio','fecha']),
                'fecha_fin'          => $this->fecha($f, ['end_date','fecha_fin']),
                'notas'              => $this->txt($f, ['notes','notas']),
            ],
            'pagos' => [
                'documento_paciente' => $this->num($f, ['doc_number','document','cedula']),
                'concepto'           => $this->txt($f, ['concept','description','concepto']),
                'valor'              => $this->dinero($f, ['amount','total','value','valor']),
                'metodo_pago'        => $this->metodoPago($f, ['payment_method','method','metodo']),
                'referencia_pago'    => $this->txt($f, ['reference','receipt','referencia']),
                'fecha_pago'         => $this->fecha($f, ['payment_date','date','fecha']),
                'observaciones'      => $this->txt($f, ['notes','observations','observaciones']),
            ],
            'evoluciones' => [
                'documento_paciente'         => $this->num($f, ['doc_number','document','cedula']),
                'fecha'                      => $this->fecha($f, ['date','evolution_date','fecha']),
                'hora'                       => $this->hora($f, ['time','hora']),
                'procedimiento'              => $this->txt($f, ['procedure','treatment']),
                'descripcion'                => $this->txt($f, ['description','notes','evolution']),
                'presion_arterial'           => $this->txt($f, ['blood_pressure','bp','presion']),
                'proxima_cita_fecha'         => $this->fecha($f, ['next_appointment','next_date']),
                'proxima_cita_procedimiento' => $this->txt($f, ['next_procedure','next_treatment']),
                'observaciones'              => $this->txt($f, ['observations','notas']),
            ],
            'consentimientos' => [
                'documento_paciente'         => $this->num($f, ['doc_number','document','cedula']),
                'fecha_autorizacion'         => $this->fecha($f, ['consent_date','date','fecha']),
                'acepta_almacenamiento'      => $this->bool($f, ['data_storage','storage','consent']),
                'acepta_contacto_whatsapp'   => $this->bool($f, ['whatsapp_contact','whatsapp']),
                'acepta_contacto_email'      => $this->bool($f, ['email_contact','email_consent']),
                'acepta_contacto_llamada'    => $this->bool($f, ['call_consent','phone_consent']),
                'acepta_recordatorios'       => $this->bool($f, ['reminders','reminder_consent']),
                'acepta_compartir_entidades' => $this->bool($f, ['data_sharing','share_data']),
                'firmado'                    => $this->bool($f, ['signed','firma']),
            ],
            default => $f,
        };
    }

    private function transformarGenerico(array $f, string $tipo): array
    {
        return match($tipo) {
            'pacientes' => [
                'nombre'              => $this->txt($f, ['Nombres','nombres','Nombre','nombre','first_name']),
                'apellido'            => $this->txt($f, ['Apellidos','apellidos','Apellido','apellido','last_name']),
                'tipo_documento'      => $this->tipoDoc($f, ['Tipo Documento','tipo_documento','TipoDoc','doc_type']),
                'numero_documento'    => $this->num($f, ['Numero Documento','numero_documento','Cedula','cedula','doc_number']),
                'fecha_nacimiento'    => $this->fecha($f, ['Fecha Nacimiento','fecha_nacimiento','birth_date']),
                'genero'              => $this->genero($f, ['Genero','genero','Sexo','sexo','gender']),
                'telefono'            => $this->num($f, ['Telefono','telefono','Celular','celular','phone']),
                'email'               => strtolower($this->txt($f, ['Email','email','Correo','correo'])),
                'direccion'           => $this->txt($f, ['Direccion','direccion','Dirección','address']),
                'ciudad'              => $this->txt($f, ['Ciudad','ciudad','city']),
                'ocupacion'           => $this->txt($f, ['Ocupacion','ocupacion','Profesion','occupation']),
                'nombre_acudiente'    => $this->txt($f, ['Acudiente','acudiente','nombre_acudiente','guardian']),
                'telefono_emergencia' => $this->num($f, ['Telefono Acudiente','telefono_acudiente','tel_emergencia','emergency_phone']),
            ],
            'historia_clinica' => [
                'documento_paciente'         => $this->num($f, ['Cedula','cedula','Documento','documento','Numero Documento']),
                'fecha_apertura'             => $this->fecha($f, ['Fecha Apertura','fecha_apertura','Fecha','fecha']),
                'motivo_consulta'            => $this->txt($f, ['Motivo Consulta','motivo_consulta','Motivo','motivo']),
                'enfermedad_actual'          => $this->txt($f, ['Enfermedad Actual','enfermedad_actual','Enfermedad']),
                'antecedentes_medicos'       => $this->txt($f, ['Antecedentes Medicos','antecedentes_medicos','Antecedentes']),
                'medicamentos_actuales'      => $this->txt($f, ['Medicamentos','medicamentos_actuales','Medicamentos Actuales']),
                'alergias'                   => $this->txt($f, ['Alergias','alergias']),
                'antecedentes_familiares'    => $this->txt($f, ['Antecedentes Familiares','antecedentes_familiares']),
                'antecedentes_odontologicos' => $this->txt($f, ['Antecedentes Odontologicos','antecedentes_odontologicos']),
                'presion_arterial'           => $this->txt($f, ['Presion Arterial','presion_arterial','PA']),
                'frecuencia_cardiaca'        => $this->txt($f, ['Frecuencia Cardiaca','frecuencia_cardiaca','FC']),
                'observaciones_generales'    => $this->txt($f, ['Observaciones','observaciones']),
            ],
            'citas' => [
                'documento_paciente' => $this->num($f, ['Cedula','cedula','Documento','documento']),
                'fecha'              => $this->fecha($f, ['Fecha','fecha','Fecha Cita']),
                'hora_inicio'        => $this->hora($f, ['Hora','hora','Hora Inicio']),
                'hora_fin'           => $this->hora($f, ['Hora Fin','hora_fin']),
                'procedimiento'      => $this->txt($f, ['Procedimiento','procedimiento','Tratamiento']),
                'estado'             => $this->estadoCita($f, ['Estado','estado']),
                'notas'              => $this->txt($f, ['Notas','notas','Observaciones']),
            ],
            'tratamientos' => [
                'documento_paciente' => $this->num($f, ['Cedula','cedula','Documento','documento']),
                'nombre'             => $this->txt($f, ['Tratamiento','tratamiento','Procedimiento','Nombre']),
                'valor_total'        => $this->dinero($f, ['Valor','valor','Total','Precio']),
                'saldo_pendiente'    => $this->dinero($f, ['Saldo','saldo','Saldo Pendiente']),
                'estado'             => $this->estadoTratamiento($f, ['Estado','estado']),
                'fecha_inicio'       => $this->fecha($f, ['Fecha Inicio','fecha_inicio','Fecha']),
                'fecha_fin'          => $this->fecha($f, ['Fecha Fin','fecha_fin']),
                'notas'              => $this->txt($f, ['Notas','notas','Observaciones']),
            ],
            'pagos' => [
                'documento_paciente' => $this->num($f, ['Cedula','cedula','Documento','documento']),
                'concepto'           => $this->txt($f, ['Concepto','concepto','Descripcion','Tratamiento']),
                'valor'              => $this->dinero($f, ['Valor','valor','Monto','Total']),
                'metodo_pago'        => $this->metodoPago($f, ['Metodo Pago','metodo_pago','Forma Pago']),
                'referencia_pago'    => $this->txt($f, ['Referencia','referencia','Recibo']),
                'fecha_pago'         => $this->fecha($f, ['Fecha Pago','fecha_pago','Fecha']),
                'observaciones'      => $this->txt($f, ['Observaciones','observaciones','Notas']),
            ],
            'evoluciones' => [
                'documento_paciente'         => $this->num($f, ['Cedula','cedula','Documento','documento']),
                'fecha'                      => $this->fecha($f, ['Fecha','fecha']),
                'hora'                       => $this->hora($f, ['Hora','hora']),
                'procedimiento'              => $this->txt($f, ['Procedimiento','procedimiento','Tratamiento']),
                'descripcion'                => $this->txt($f, ['Descripcion','descripcion','Evolucion','Notas']),
                'presion_arterial'           => $this->txt($f, ['Presion','presion_arterial','PA']),
                'proxima_cita_fecha'         => $this->fecha($f, ['Proxima Cita','proxima_cita','Proxima Fecha']),
                'proxima_cita_procedimiento' => $this->txt($f, ['Proximo Procedimiento','prox_procedimiento']),
                'observaciones'              => $this->txt($f, ['Observaciones','observaciones']),
            ],
            'consentimientos' => [
                'documento_paciente'         => $this->num($f, ['Cedula','cedula','Documento','documento']),
                'fecha_autorizacion'         => $this->fecha($f, ['Fecha Autorizacion','fecha_autorizacion','Fecha']),
                'acepta_almacenamiento'      => $this->bool($f, ['Acepta Datos','acepta_datos','Acepta']),
                'acepta_contacto_whatsapp'   => $this->bool($f, ['Whatsapp','acepta_whatsapp']),
                'acepta_contacto_email'      => $this->bool($f, ['Email Contacto','acepta_email']),
                'acepta_contacto_llamada'    => $this->bool($f, ['Llamadas','acepta_llamada']),
                'acepta_recordatorios'       => $this->bool($f, ['Recordatorios','acepta_recordatorios']),
                'acepta_compartir_entidades' => $this->bool($f, ['Compartir','acepta_compartir']),
                'firmado'                    => $this->bool($f, ['Firmado','firmado','Tiene Firma']),
            ],
            default => $f,
        };
    }

    // ─── IMPORTADORES ────────────────────────────────────────────

    private function importarPaciente(array $datos): array
    {
        if (empty($datos['numero_documento'])) {
            return ['estado' => 'omitido', 'mensaje' => 'Sin número de documento'];
        }
        if (empty($datos['nombre']) && empty($datos['apellido'])) {
            return ['estado' => 'omitido', 'mensaje' => 'Sin nombre ni apellido'];
        }

        $existente = Paciente::where('numero_documento', $datos['numero_documento'])->first();
        if ($existente) {
            return [
                'estado'  => 'duplicado',
                'mensaje' => "Ya existe: {$existente->nombre_completo} ({$existente->numero_documento})",
                'id'      => $existente->id,
                'modelo'  => 'App\\Models\\Paciente',
            ];
        }

        try {
            $paciente = Paciente::create([
                'nombre'              => $datos['nombre'] ?? '',
                'apellido'            => $datos['apellido'] ?? '',
                'tipo_documento'      => $datos['tipo_documento'] ?? 'CC',
                'numero_documento'    => $datos['numero_documento'],
                'fecha_nacimiento'    => $datos['fecha_nacimiento'] ?? null,
                'genero'              => $datos['genero'] ?? 'otro',
                'telefono'            => $datos['telefono'] ?: null,
                'email'               => $datos['email'] ?: null,
                'direccion'           => $datos['direccion'] ?: null,
                'ciudad'              => $datos['ciudad'] ?: null,
                'ocupacion'           => $datos['ocupacion'] ?: null,
                'nombre_acudiente'    => $datos['nombre_acudiente'] ?: null,
                'telefono_emergencia' => $datos['telefono_emergencia'] ?: null,
                'activo'              => true,
                'importado'           => true,
                'fuente_importacion'  => $this->importacion->fuente,
            ]);

            $this->cachePacientes[$paciente->numero_documento] = $paciente;

            return [
                'estado'  => 'importado',
                'mensaje' => "Importado: {$paciente->nombre_completo}",
                'id'      => $paciente->id,
                'modelo'  => 'App\\Models\\Paciente',
            ];
        } catch (\Exception $e) {
            return ['estado' => 'error', 'mensaje' => 'Error al crear paciente: ' . $e->getMessage()];
        }
    }

    private function importarHistoriaClinica(array $datos): array
    {
        $paciente = $this->buscarPaciente($datos['documento_paciente'] ?? '');
        if (!$paciente) {
            return ['estado' => 'omitido', 'mensaje' => 'Paciente no encontrado: ' . ($datos['documento_paciente'] ?? 'sin documento')];
        }

        $existente = HistoriaClinica::where('paciente_id', $paciente->id)->first();
        if ($existente) {
            return [
                'estado'  => 'duplicado',
                'mensaje' => "Historia clínica ya existe para {$paciente->nombre_completo}",
                'id'      => $existente->id,
                'modelo'  => 'App\\Models\\HistoriaClinica',
            ];
        }

        try {
            $historia = HistoriaClinica::create([
                'paciente_id'                => $paciente->id,
                'fecha_apertura'             => $datos['fecha_apertura'] ?? now()->toDateString(),
                'motivo_consulta'            => $datos['motivo_consulta'] ?: null,
                'enfermedad_actual'          => $datos['enfermedad_actual'] ?: null,
                'antecedentes_medicos'       => $datos['antecedentes_medicos'] ?: null,
                'medicamentos_actuales'      => $datos['medicamentos_actuales'] ?: null,
                'alergias'                   => $datos['alergias'] ?: null,
                'antecedentes_familiares'    => $datos['antecedentes_familiares'] ?: null,
                'antecedentes_odontologicos' => $datos['antecedentes_odontologicos'] ?: null,
                'presion_arterial'           => $datos['presion_arterial'] ?: null,
                'frecuencia_cardiaca'        => $datos['frecuencia_cardiaca'] ?: null,
                'observaciones_generales'    => $datos['observaciones_generales'] ?: null,
                'activo'                     => true,
            ]);

            return [
                'estado'  => 'importado',
                'mensaje' => "Historia clínica importada para {$paciente->nombre_completo}",
                'id'      => $historia->id,
                'modelo'  => 'App\\Models\\HistoriaClinica',
            ];
        } catch (\Exception $e) {
            return ['estado' => 'error', 'mensaje' => 'Error al crear historia clínica: ' . $e->getMessage()];
        }
    }

    private function importarCita(array $datos): array
    {
        $paciente = $this->buscarPaciente($datos['documento_paciente'] ?? '');
        if (!$paciente) {
            return ['estado' => 'omitido', 'mensaje' => 'Paciente no encontrado: ' . ($datos['documento_paciente'] ?? 'sin documento')];
        }
        if (empty($datos['fecha'])) {
            return ['estado' => 'omitido', 'mensaje' => 'Fila sin fecha de cita'];
        }

        try {
            $cita = Cita::create([
                'paciente_id'  => $paciente->id,
                'fecha'        => $datos['fecha'],
                'hora_inicio'  => $datos['hora_inicio'] ?: '08:00',
                'hora_fin'     => $datos['hora_fin'] ?: null,
                'procedimiento'=> $datos['procedimiento'] ?: 'Importada',
                'estado'       => $datos['estado'] ?? 'completada',
                'notas'        => $datos['notas'] ?: null,
                'activo'       => true,
            ]);

            return [
                'estado'  => 'importado',
                'mensaje' => "Cita importada: {$paciente->nombre_completo} — {$datos['fecha']}",
                'id'      => $cita->id,
                'modelo'  => 'App\\Models\\Cita',
            ];
        } catch (\Exception $e) {
            return ['estado' => 'error', 'mensaje' => 'Error al crear cita: ' . $e->getMessage()];
        }
    }

    private function importarTratamiento(array $datos): array
    {
        $paciente = $this->buscarPaciente($datos['documento_paciente'] ?? '');
        if (!$paciente) {
            return ['estado' => 'omitido', 'mensaje' => 'Paciente no encontrado: ' . ($datos['documento_paciente'] ?? 'sin documento')];
        }
        if (empty($datos['nombre'])) {
            return ['estado' => 'omitido', 'mensaje' => 'Fila sin nombre de tratamiento'];
        }

        $valor    = (float) ($datos['valor_total'] ?? 0);
        $saldo    = $datos['saldo_pendiente'] !== '' ? (float) $datos['saldo_pendiente'] : $valor;

        try {
            $tratamiento = Tratamiento::create([
                'paciente_id'    => $paciente->id,
                'nombre'         => $datos['nombre'],
                'valor_total'    => $valor,
                'saldo_pendiente'=> $saldo,
                'estado'         => $datos['estado'] ?? 'en_proceso',
                'fecha_inicio'   => $datos['fecha_inicio'] ?: now()->toDateString(),
                'fecha_fin'      => $datos['fecha_fin'] ?: null,
                'notas'          => $datos['notas'] ?: null,
                'activo'         => true,
            ]);

            return [
                'estado'  => 'importado',
                'mensaje' => "Tratamiento importado: {$datos['nombre']} — {$paciente->nombre_completo}",
                'id'      => $tratamiento->id,
                'modelo'  => 'App\\Models\\Tratamiento',
            ];
        } catch (\Exception $e) {
            return ['estado' => 'error', 'mensaje' => 'Error al crear tratamiento: ' . $e->getMessage()];
        }
    }

    private function importarPago(array $datos): array
    {
        $paciente = $this->buscarPaciente($datos['documento_paciente'] ?? '');
        if (!$paciente) {
            return ['estado' => 'omitido', 'mensaje' => 'Paciente no encontrado: ' . ($datos['documento_paciente'] ?? 'sin documento')];
        }
        if (empty($datos['valor']) || (float)$datos['valor'] <= 0) {
            return ['estado' => 'omitido', 'mensaje' => 'Fila sin valor de pago'];
        }

        try {
            $pago = Pago::create([
                'paciente_id'     => $paciente->id,
                'concepto'        => $datos['concepto'] ?: 'Pago importado',
                'valor'           => (float) $datos['valor'],
                'metodo_pago'     => $datos['metodo_pago'] ?? 'efectivo',
                'referencia_pago' => $datos['referencia_pago'] ?: null,
                'fecha_pago'      => $datos['fecha_pago'] ?: now()->toDateString(),
                'observaciones'   => $datos['observaciones'] ?: null,
                'es_pago_libre'   => true,
                'activo'          => true,
                'anulado'         => false,
            ]);

            return [
                'estado'  => 'importado',
                'mensaje' => "Pago importado: \${$datos['valor']} — {$paciente->nombre_completo}",
                'id'      => $pago->id,
                'modelo'  => 'App\\Models\\Pago',
            ];
        } catch (\Exception $e) {
            return ['estado' => 'error', 'mensaje' => 'Error al crear pago: ' . $e->getMessage()];
        }
    }

    private function importarEvolucion(array $datos): array
    {
        $paciente = $this->buscarPaciente($datos['documento_paciente'] ?? '');
        if (!$paciente) {
            return ['estado' => 'omitido', 'mensaje' => 'Paciente no encontrado: ' . ($datos['documento_paciente'] ?? 'sin documento')];
        }
        if (empty($datos['fecha'])) {
            return ['estado' => 'omitido', 'mensaje' => 'Fila sin fecha de evolución'];
        }

        $historia = HistoriaClinica::where('paciente_id', $paciente->id)->first();

        try {
            $evolucion = Evolucion::create([
                'paciente_id'                => $paciente->id,
                'historia_clinica_id'        => $historia?->id,
                'fecha'                      => $datos['fecha'],
                'hora'                       => $datos['hora'] ?: now()->format('H:i'),
                'procedimiento'              => $datos['procedimiento'] ?: 'Evolución importada',
                'descripcion'                => $datos['descripcion'] ?: null,
                'presion_arterial'           => $datos['presion_arterial'] ?: null,
                'proxima_cita_fecha'         => $datos['proxima_cita_fecha'] ?: null,
                'proxima_cita_procedimiento' => $datos['proxima_cita_procedimiento'] ?: null,
                'observaciones'              => $datos['observaciones'] ?: null,
                'activo'                     => true,
                'firmado'                    => false,
            ]);

            return [
                'estado'  => 'importado',
                'mensaje' => "Evolución importada: {$paciente->nombre_completo} — {$datos['fecha']}",
                'id'      => $evolucion->id,
                'modelo'  => 'App\\Models\\Evolucion',
            ];
        } catch (\Exception $e) {
            return ['estado' => 'error', 'mensaje' => 'Error al crear evolución: ' . $e->getMessage()];
        }
    }

    private function importarConsentimiento(array $datos): array
    {
        $paciente = $this->buscarPaciente($datos['documento_paciente'] ?? '');
        if (!$paciente) {
            return ['estado' => 'omitido', 'mensaje' => 'Paciente no encontrado: ' . ($datos['documento_paciente'] ?? 'sin documento')];
        }

        $existente = AutorizacionDatos::where('paciente_id', $paciente->id)->first();
        if ($existente) {
            return [
                'estado'  => 'duplicado',
                'mensaje' => "Consentimiento ya existe para {$paciente->nombre_completo}",
                'id'      => $existente->id,
                'modelo'  => 'App\\Models\\AutorizacionDatos',
            ];
        }

        try {
            $consentimiento = AutorizacionDatos::create([
                'paciente_id'                => $paciente->id,
                'fecha_autorizacion'         => $datos['fecha_autorizacion'] ?: now()->toDateString(),
                'acepta_almacenamiento'      => $datos['acepta_almacenamiento'] ?? true,
                'acepta_contacto_whatsapp'   => $datos['acepta_contacto_whatsapp'] ?? false,
                'acepta_contacto_email'      => $datos['acepta_contacto_email'] ?? false,
                'acepta_contacto_llamada'    => $datos['acepta_contacto_llamada'] ?? false,
                'acepta_recordatorios'       => $datos['acepta_recordatorios'] ?? false,
                'acepta_compartir_entidades' => $datos['acepta_compartir_entidades'] ?? false,
                'firmado'                    => $datos['firmado'] ?? false,
            ]);

            return [
                'estado'  => 'importado',
                'mensaje' => "Consentimiento importado: {$paciente->nombre_completo}",
                'id'      => $consentimiento->id,
                'modelo'  => 'App\\Models\\AutorizacionDatos',
            ];
        } catch (\Exception $e) {
            return ['estado' => 'error', 'mensaje' => 'Error al crear consentimiento: ' . $e->getMessage()];
        }
    }

    // ─── HELPERS ─────────────────────────────────────────────────

    /** Busca paciente por documento usando caché interno */
    private function buscarPaciente(string $documento): ?Paciente
    {
        $documento = preg_replace('/[^0-9A-Za-z\-]/', '', $documento);
        if (empty($documento)) return null;

        if (isset($this->cachePacientes[$documento])) {
            return $this->cachePacientes[$documento];
        }

        $paciente = Paciente::where('numero_documento', $documento)->first();
        if ($paciente) {
            $this->cachePacientes[$documento] = $paciente;
        }
        return $paciente;
    }

    /** Obtiene texto del primer campo que exista en la fila */
    private function txt(array $f, array $claves): string
    {
        foreach ($claves as $clave) {
            if (isset($f[$clave]) && $f[$clave] !== '') {
                return trim(preg_replace('/\s+/', ' ', $f[$clave]));
            }
        }
        return '';
    }

    /** Obtiene número limpio (solo dígitos) */
    private function num(array $f, array $claves): string
    {
        return preg_replace('/[^0-9]/', '', $this->txt($f, $claves));
    }

    /** Parsea fecha en múltiples formatos */
    private function fecha(array $f, array $claves): ?string
    {
        $valor = $this->txt($f, $claves);
        if (empty($valor)) return null;

        $formatos = ['d/m/Y','Y-m-d','d-m-Y','m/d/Y','d/m/y','Y/m/d','d.m.Y','d-m-y','Y.m.d'];
        foreach ($formatos as $formato) {
            $d = \DateTime::createFromFormat($formato, trim($valor));
            if ($d && $d->format('Y') > 1900 && $d->format('Y') < 2100) {
                return $d->format('Y-m-d');
            }
        }
        return null;
    }

    /** Parsea hora en formato HH:MM */
    private function hora(array $f, array $claves): string
    {
        $valor = $this->txt($f, $claves);
        if (empty($valor)) return '';

        if (preg_match('/^(\d{1,2})[:\.](\d{2})/', $valor, $m)) {
            return str_pad($m[1], 2, '0', STR_PAD_LEFT) . ':' . $m[2];
        }
        if (preg_match('/^(\d{1,2})$/', $valor, $m)) {
            return str_pad($m[1], 2, '0', STR_PAD_LEFT) . ':00';
        }
        return '';
    }

    /** Extrae valor numérico monetario */
    private function dinero(array $f, array $claves): string
    {
        $valor = $this->txt($f, $claves);
        if (empty($valor)) return '';
        // Elimina símbolo de moneda, espacios, puntos de miles; convierte coma decimal
        $valor = preg_replace('/[^\d,\.]/', '', $valor);
        // Si tiene punto y coma, el punto es separador de miles
        if (strpos($valor, ',') !== false && strpos($valor, '.') !== false) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        } elseif (strpos($valor, ',') !== false) {
            // Coma como decimal
            $valor = str_replace(',', '.', $valor);
        }
        return is_numeric($valor) ? $valor : '';
    }

    /** Parsea booleano */
    private function bool(array $f, array $claves): bool
    {
        $valor = strtolower($this->txt($f, $claves));
        return in_array($valor, ['1','true','si','sí','yes','x','v','activo','acepta','firmado']);
    }

    private function tipoDoc(array $f, array $claves): string
    {
        $mapa = [
            'CC' => 'CC','C.C' => 'CC','CEDULA' => 'CC','CÉDULA' => 'CC',
            'TI' => 'TI','T.I' => 'TI','TARJETA' => 'TI','TARJETA DE IDENTIDAD' => 'TI',
            'CE' => 'CE','C.E' => 'CE','EXTRANJERIA' => 'CE','CÉDULA EXTRANJERÍA' => 'CE',
            'PA' => 'PA','PASAPORTE' => 'PA','PAS' => 'PA',
            'RC' => 'RC','REGISTRO CIVIL' => 'RC',
            'NIT' => 'NIT',
        ];
        return $mapa[strtoupper(trim($this->txt($f, $claves)))] ?? 'CC';
    }

    private function genero(array $f, array $claves): string
    {
        $mapa = [
            'M' => 'masculino','MASCULINO' => 'masculino','HOMBRE' => 'masculino',
            'H' => 'masculino','MALE' => 'masculino',
            'F' => 'femenino','FEMENINO' => 'femenino','MUJER' => 'femenino',
            'DAMA' => 'femenino','FEMALE' => 'femenino',
        ];
        return $mapa[strtoupper(trim($this->txt($f, $claves)))] ?? 'otro';
    }

    private function estadoCita(array $f, array $claves): string
    {
        $valor = strtolower($this->txt($f, $claves));
        return match(true) {
            in_array($valor, ['pendiente','pending','programada','agendada']) => 'pendiente',
            in_array($valor, ['confirmada','confirmado','confirmed'])          => 'confirmada',
            in_array($valor, ['completada','completado','realizada','atendida','done','completed']) => 'completada',
            in_array($valor, ['cancelada','cancelado','cancelled','canceled']) => 'cancelada',
            in_array($valor, ['no_asistio','no asistio','inasistencia','no_show']) => 'no_asistio',
            default => 'completada',
        };
    }

    private function estadoTratamiento(array $f, array $claves): string
    {
        $valor = strtolower($this->txt($f, $claves));
        return match(true) {
            in_array($valor, ['en_proceso','en proceso','activo','active','iniciado']) => 'en_proceso',
            in_array($valor, ['terminado','terminada','finalizado','completado','completed','done']) => 'terminado',
            in_array($valor, ['cancelado','cancelada','cancelled']) => 'cancelado',
            in_array($valor, ['presupuestado','presupuesto','cotizado']) => 'presupuestado',
            default => 'en_proceso',
        };
    }

    private function metodoPago(array $f, array $claves): string
    {
        $valor = strtolower($this->txt($f, $claves));
        return match(true) {
            in_array($valor, ['efectivo','cash','contado']) => 'efectivo',
            str_contains($valor, 'tarjeta') || str_contains($valor, 'card') || str_contains($valor, 'credito') || str_contains($valor, 'debito') => 'tarjeta',
            str_contains($valor, 'transfer') || str_contains($valor, 'bancaria') || str_contains($valor, 'nequi') || str_contains($valor, 'daviplata') => 'transferencia',
            str_contains($valor, 'cheque') || str_contains($valor, 'check') => 'cheque',
            default => 'efectivo',
        };
    }

    private function registrarDetalle(int $numero, array $original, ?array $transformado, string $estado, ?string $mensaje, ?int $registroId = null, ?string $modelo = null): void
    {
        ImportacionDetalle::create([
            'importacion_id'      => $this->importacion->id,
            'fila_numero'         => $numero,
            'datos_originales'    => $original,
            'datos_transformados' => $transformado,
            'modelo'              => $modelo,
            'registro_id'         => $registroId,
            'estado'              => $estado,
            'mensaje'             => $mensaje,
        ]);
    }

    // ─── REVERTIR ─────────────────────────────────────────────────

    public function revertir(): bool
    {
        if (!$this->importacion->puede_revertir) return false;

        try {
            DB::beginTransaction();

            $detalles = ImportacionDetalle::where('importacion_id', $this->importacion->id)
                ->where('estado', 'importado')
                ->whereNotNull('registro_id')
                ->get();

            foreach ($detalles as $detalle) {
                $modelo = $detalle->modelo ?? 'App\\Models\\Paciente';
                if (class_exists($modelo)) {
                    $modelo::find($detalle->registro_id)?->delete();
                }
            }

            $this->importacion->update([
                'estado'         => 'revertido',
                'puede_revertir' => false,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
